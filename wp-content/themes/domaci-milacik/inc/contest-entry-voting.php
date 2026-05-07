<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    define( 'CONTEST_ENTRY_VOTES_DB_VERSION', '1.0' );

    add_action('after_switch_theme', 'create_contest_entry_votes_table');

    function create_contest_entry_votes_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'contest_entry_votes';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT UNSIGNED NOT NULL,
            fingerprint_ip CHAR(64) NOT NULL,
            fingerprint_cookie CHAR(64) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX post_id (post_id),
            INDEX fingerprint_ip_created (fingerprint_ip, created_at),
            INDEX fingerprint_cookie_created (fingerprint_cookie, created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        update_option( 'contest_entry_votes_db_version', CONTEST_ENTRY_VOTES_DB_VERSION );
    }

    add_action( 'wp_enqueue_scripts', 'enqueue_contest_entry_voting_assets' );

    function enqueue_contest_entry_voting_assets() {
        if ( is_singular( 'contest_entry' ) ) {
            wp_enqueue_script(
                'cf-turnstile',
                'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit',
                array(),
                null,
                true
            );

            wp_enqueue_script(
                'contest-entry-voting',
                get_stylesheet_directory_uri() . '/assets/js/contest-entry-voting.js',
                array( 'cf-turnstile' ),
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
            <div>
                <p id="contest-vote-count">
                    <?php echo esc_html( $votes ); ?>
                </p>
            </div>
            <div id="contest-vote-turnstile"></div>
            <div>
                <button id="contest-vote-button" data-post-id="<?php echo get_the_ID(); ?>">
                    <span id="contest-vote-button-text">Hlasovať</span>
                    <span id="contest-vote-button-loading" class="hidden">Odosielam hlas</span>
                    <span id="contest-vote-button-voted" class="hidden">Už ste hlasovali</span>
                </button>
            </div>
            <div id="contest-vote-messages"></div>
            <div id="contest-vote-countdown"></div>
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

            wp_die();
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
        $fingerprint_ip = hash_hmac( 'sha256', $ip . '|' . $post_id, $salt );
        $fingerprint_cookie = hash_hmac( 'sha256', $cookie_value . '|' . $post_id, $salt );

        /**
         * =========================
         * HARD 1-HOUR CHECK (DATABASE)
         * =========================
         */
        global $wpdb;

        $since = gmdate( 'Y-m-d H:i:s', time() - HOUR_IN_SECONDS );

        $already_voted  = $wpdb->get_var( $wpdb->prepare(
            "SELECT 1
            FROM {$wpdb->prefix}contest_entry_votes
            WHERE ( fingerprint_ip = %s OR fingerprint_cookie = %s )
            AND created_at > %s
            LIMIT 1",
            $fingerprint_ip,
            $fingerprint_cookie,
            $since
        ) );

        if ( $already_voted  ) {
            wp_send_json_error( array( 'message' => 'Hlasovať môžete raz za hodinu.' ) );

            wp_die();
        }

        /**
         * =========================
         * POST RATE LIMIT
         * =========================
         */
        $vote_key = 'contest_vote_' . $fingerprint_ip;

        if ( get_transient( $vote_key ) ) {
            wp_send_json_error( array( 'message' => 'Už ste hlasovali. Skúste znova o hodinu.' ) );

            wp_die();
        }

        /**
         * =========================
         * GLOBAL RATE LIMIT
         * =========================
         */
        $turnstile_token = sanitize_text_field( $_POST['turnstile_token'] ?? '' );

        $rate_key = 'contest_vote_rate_' . $fingerprint_ip;
        $attempts = (int) get_transient( $rate_key );

        if ( $attempts >= 5 ) {
            wp_send_json_error( array( 'message' => 'Príliš veľa hlasovaní. Skúste neskôr.' ) );

            wp_die();
        }

        if ( $attempts >= 3 ) {
            if ( empty( $turnstile_token ) || ! verify_contest_vote_turnstile_token( $turnstile_token ) ) {
                wp_send_json_error( array(
                    'require_turnstile' => true,
                    'message'           => 'Prebieha overenie, prosím čakajte...'
                ) );

                wp_die();
            }
        }

        set_transient( $vote_key, 1, HOUR_IN_SECONDS );
        set_transient( $rate_key, $attempts + 1, 15 * MINUTE_IN_SECONDS );

        /**
         * =========================
         * LOG VOTE
         * =========================
         */
        $inserted = $wpdb->insert(
            $wpdb->prefix . 'contest_entry_votes',
            array(
                'post_id'               => $post_id,
                'fingerprint_ip'        => $fingerprint_ip,
                'fingerprint_cookie'    => $fingerprint_cookie,
                'created_at'            => gmdate( 'Y-m-d H:i:s' ),
            ),
            array( '%d', '%s', '%s', '%s' )
        );

        if ( ! $inserted ) {
            delete_transient( $vote_key );
            set_transient( $rate_key, max( 0, $attempts ), 15 * MINUTE_IN_SECONDS );

            wp_send_json_error( array( 'message' => 'Hlas sa nepodarilo zaznamenať. Skúste to znova.' ) );

            wp_die();
        }

        /**
         * =========================
         * ATOMIC INCREMENT
         * =========================
         */
        $updated = $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->postmeta} 
                SET meta_value = CAST(meta_value AS UNSIGNED) + 1 
                WHERE post_id = %d AND meta_key = 'votes'",
                $post_id
            )
        );

        if ( ! $updated && ! metadata_exists( 'post', $post_id, 'votes' ) ) {
            add_post_meta( $post_id, 'votes', 1, true );
        }

        /**
         * =========================
         * SUCCESS
         * =========================
         */
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

    add_action( 'wp_ajax_contest_vote_status', 'check_contest_vote_status' );
    add_action( 'wp_ajax_nopriv_contest_vote_status', 'check_contest_vote_status' );

    function check_contest_vote_status() {
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

            wp_die();
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
            $cookie_value = '';
        } else {
            $cookie_value = sanitize_text_field( $_COOKIE['contest_vote_id'] );
        }

        

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $salt = get_option( 'contest_vote_salt', '' );

        if ( ! $salt ) {
            wp_send_json_success( array( 'can_vote' => true ) );

            wp_die();
        }

        /**
         * =========================
         * FINGERPRINT
         * =========================
         */
        $fingerprint_ip = hash_hmac( 'sha256', $ip . '|' . $post_id, $salt );
        $fingerprint_cookie = hash_hmac( 'sha256', $cookie_value . '|' . $post_id, $salt );

        /**
         * =========================
         * HARD 1-HOUR CHECK (DATABASE)
         * =========================
         */
        global $wpdb;

        $since = gmdate( 'Y-m-d H:i:s', time() - HOUR_IN_SECONDS );

        $last_vote_at  = $wpdb->get_var( $wpdb->prepare(
            "SELECT created_at
            FROM {$wpdb->prefix}contest_entry_votes
            WHERE ( fingerprint_ip = %s OR fingerprint_cookie = %s )
            AND created_at > %s
            ORDER BY created_at DESC
            LIMIT 1",
            $fingerprint_ip,
            $fingerprint_cookie,
            $since
        ) );

        if ( ! $last_vote_at  ) {
            wp_send_json_success( array( 'can_vote' => true ) );

            wp_die();
        }

        /**
         * =========================
         * TIME TO NEXT VOTE
         * =========================
         */
        $elapsed = time() - strtotime( $last_vote_at );

        /**
         * =========================
         * SUCCESS
         * =========================
         */
        wp_send_json_success( array(
            'can_vote'      => false,
            'next_vote_in'  => HOUR_IN_SECONDS - $elapsed,
            'message'       => 'Už ste hlasovali.',
        ) );

        wp_die();
    }

    add_filter( 'acf/prepare_field/name=votes', function( $field ) {
        $field['disabled'] = 1;
        return $field;
    } );

    add_filter( 'acf/prepare_field/name=sms_votes', function( $field ) {
        $field['disabled'] = 1;
        return $field;
    } );

    function verify_contest_vote_turnstile_token( $turnstile_token ) {
        if ( empty( $turnstile_token ) ) {
            return false;
        }

        $response = wp_remote_post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            array(
                'timeout' => 10,
                'body' => array(
                    'secret'   => '0x4AAAAAADI3OtR1T6YzpJ79IWI2dhIiEM0',
                    'response' => $turnstile_token,
                    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ),
            )
        );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        return ! empty( $data['success'] );
    }