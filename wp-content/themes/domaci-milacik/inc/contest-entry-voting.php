<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    define( 'CONTEST_VOTES_DB_VERSION', '1.1' );

    add_action('after_switch_theme', 'create_contest_entry_votes_table');

    function create_contest_entry_votes_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'contest_entry_votes';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT UNSIGNED NOT NULL,
            fingerprint CHAR(64) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX post_id (post_id),
            INDEX fingerprint_created (fingerprint, created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    add_action( 'after_switch_theme', 'maybe_upgrade_contest_votes_table' );

    function maybe_upgrade_contest_votes_table() {
        $installed = get_option( 'contest_votes_db_version' );

        if ( $installed === CONTEST_VOTES_DB_VERSION ) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'contest_entry_votes';

        $wpdb->query(
            "ALTER TABLE {$table}
            ADD INDEX fingerprint_created (fingerprint, created_at)"
        );

        update_option( 'contest_votes_db_version', CONTEST_VOTES_DB_VERSION );
    }

    add_action( 'wp_enqueue_scripts', 'enqueue_contest_entry_voting_assets' );

    function enqueue_contest_entry_voting_assets() {
        if ( is_singular( 'contest_entry' ) ) {
            wp_enqueue_script(
                'contest-entry-voting',
                get_stylesheet_directory_uri() . '/assets/js/contest-entry-voting.js',
                array(),
                filemtime( get_stylesheet_directory() . '/assets/js/contest-entry-voting.js' ),
                true
            );

            wp_localize_script( 'contest-entry-voting', 'contest_entry_voting_ajax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'contest_entry_vote' ),
            ) );
        }
    }

    add_shortcode( 'contest_entry_voting', 'render_contest_entry_voting' );

    function render_contest_entry_voting() {
        ob_start();

        $votes = (int) get_field( 'votes' );
        ?>
            <p id="contest-vote-count">
                <?php echo esc_html( $votes ); ?>
            </p>
            <button id="contest-vote-button" data-post-id="<?php echo get_the_ID(); ?>">
                <span id="contest-vote-button-text">Hlasovať</span>
                <span id="contest-vote-button-loading" class="hidden">Odosielam hlas</span>
                <span id="contest-vote-button-voted" class="hidden">Už ste hlasovali</span>
            </button>
            <div id="contest-vote-messages"></div>
        <?php
        
        return ob_get_clean();
    }

    add_action('wp_ajax_contest_vote', 'handle_contest_vote');
    add_action('wp_ajax_nopriv_contest_vote', 'handle_contest_vote');

    function handle_contest_vote() {
        /**
         * =========================
         * AJAX GUARD
         * =========================
         */
        if ( ! wp_doing_ajax() ) {
            wp_die();
        }

        /**
         * =========================
         * VERIFY NONCE
         * =========================
         */
        check_ajax_referer( 'contest_entry_vote', 'nonce', true );

        /**
         * =========================
         * POST VALIDATION
         * =========================
         */
        $post_id = absint( $_POST['post_id'] ?? 0 );

        if ( ! $post_id || get_post_type( $post_id ) !== 'contest_entry' ) {
            wp_send_json_error( array( 'message' => 'Neplatný príspevok.' ) );
        }

        if ( get_post_status( $post_id ) !== 'publish' ) {
            wp_send_json_error( array( 'message' => 'Hlasovanie nie je dostupné.' ) );

            wp_die();
        }

        /**
         * =========================
         * COOKIE
         * =========================
         */
        if ( empty( $_COOKIE['contest_vote_id'] ) ) {
            $cookie_value = wp_generate_uuid4();
            
            setcookie(
            'contest_vote_id',
            $cookie_value,
            [
                'expires'  => time() + YEAR_IN_SECONDS,
                'path'     => COOKIEPATH,
                'domain'   => COOKIE_DOMAIN,
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax',
            ] );

            $_COOKIE['contest_vote_id'] = $cookie_value;
        } else {
            $cookie_value = sanitize_text_field( $_COOKIE['contest_vote_id'] );
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $salt = get_option( 'contest_vote_salt', '' );

        if ( ! $salt ) {
            $salt = wp_generate_password( 64, true, true );
            update_option( 'contest_vote_salt', $salt, false );
        }

        /**
         * =========================
         * FINGERPRINT
         * =========================
         */
        $fingerprint = hash_hmac( 'sha256', $ip . '|' . $cookie_value . '|' . $post_id, $salt );

        /**
         * =========================
         * HARD 1-HOUR CHECK (DATABASE)
         * =========================
         */
        global $wpdb;

        $last_vote = $wpdb->get_var( $wpdb->prepare(
            "SELECT created_at
            FROM {$wpdb->prefix}contest_entry_votes
            WHERE fingerprint = %s
            ORDER BY created_at DESC
            LIMIT 1",
            $fingerprint
        ) );

        if ( $last_vote && strtotime($last_vote) > time() - HOUR_IN_SECONDS ) {
            wp_send_json_error( array( 'message' => 'Už ste hlasovali.' ) );

            wp_die();
        }

        /**
         * =========================
         * POST RATE LIMIT
         * =========================
         */
        $vote_key = 'contest_vote_' . $fingerprint;

        if ( get_transient( $vote_key ) ) {
            wp_send_json_error( array( 'message' => 'Už ste hlasovali. Skúste znova o hodinu.' ) );

            wp_die();
        }

        /**
         * =========================
         * GLOBAL RATE LIMIT
         * =========================
         */
        $rate_key = 'contest_vote_rate_' . hash_hmac( 'sha256', $ip, $salt );
        $attempts = (int) get_transient( $rate_key );

        if ( $attempts >= 20 ) {
            wp_send_json_error( array( 'message' => 'Príliš veľa hlasovaní. Skúste neskôr.' ) );

            wp_die();
        }

        set_transient( $vote_key, 1, HOUR_IN_SECONDS );
        set_transient( $rate_key, $attempts + 1, 10 * MINUTE_IN_SECONDS );

        /**
         * =========================
         * ATOMIC INCREMENT
         * =========================
         */
        $updated = $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->postmeta} 
                SET meta_value = meta_value + 1 
                WHERE post_id = %d AND meta_key = 'votes'",
                $post_id
            )
        );

        if ( ! $updated ) {
            add_post_meta( $post_id, 'votes', 1, true );
        }

        /**
         * =========================
         * LOG VOTE
         * =========================
         */
        $wpdb->insert(
            $wpdb->prefix . 'contest_entry_votes',
            array(
                'post_id'     => $post_id,
                'fingerprint' => $fingerprint,
                'created_at'  => current_time( 'mysql', true )
            ),
            array( '%d', '%s', '%s' )
        );

        $votes = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value FROM {$wpdb->postmeta}
                WHERE post_id = %d AND meta_key = 'votes'",
                $post_id
            )
        );

        wp_send_json_success( array( 'message' => 'Váš hlas bol zapísaný.', 'votes' => $votes ) );

        wp_die();
    }

    add_filter( 'acf/prepare_field/name=votes', function( $field ) {
        $field['disabled'] = 1;
        return $field;
    } );

    add_filter( 'acf/update_value/name=votes', function( $value, $post_id, $field ) {
        return get_field( 'votes', $post_id );
    }, 10, 3);