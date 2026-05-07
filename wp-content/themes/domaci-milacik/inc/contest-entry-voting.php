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
                true,
            );

            wp_enqueue_script(
                'contest-entry-voting',
                get_stylesheet_directory_uri() . '/assets/js/contest-entry-voting.js',
                array( 'cf-turnstile' ),
                filemtime( get_stylesheet_directory() . '/assets/js/contest-entry-voting.js' ),
                true,
            );

            wp_localize_script( 'contest-entry-voting', 'contest_entry_voting_ajax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'contest_entry_vote' ),
            ) );
        }
    }

    add_shortcode( 'contest_entry_vote_count', 'render_contest_entry_vote_count' );

    function render_contest_entry_vote_count() {
        ob_start();

        $votes = (int) get_field( 'votes' );
        ?>
            <p class="votes votes-md md:votes-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="votes-icon"><path d="M305 151.1L320 171.8L335 151.1C360 116.5 400.2 96 442.9 96C516.4 96 576 155.6 576 229.1L576 231.7C576 343.9 436.1 474.2 363.1 529.9C350.7 539.3 335.5 544 320 544C304.5 544 289.2 539.4 276.9 529.9C203.9 474.2 64 343.9 64 231.7L64 229.1C64 155.6 123.6 96 197.1 96C239.8 96 280 116.5 305 151.1z"/></svg>
                <span id="contest-vote-count">
                    <?php echo esc_html( $votes ); ?>
                </span>
            </p>
        <?php
        
        return ob_get_clean();
    }

    add_shortcode( 'contest_entry_vote_button', 'render_contest_entry_vote_button' );

    function render_contest_entry_vote_button() {
        ob_start();
        ?>
            <div>
                <div id="contest-vote-turnstile"></div>
                <button id="contest-vote-button" class="button button-md button-wide button-primary md:button-lg" data-post-id="<?php echo get_the_ID(); ?>">Hlasovať</button>
            </div>
            <p class="messages hidden">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="messages-icon messages-icon--success"><path d="M320 576C178.6 576 64 461.4 64 320C64 178.6 178.6 64 320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576zM438 209.7C427.3 201.9 412.3 204.3 404.5 215L285.1 379.2L233 327.1C223.6 317.7 208.4 317.7 199.1 327.1C189.8 336.5 189.7 351.7 199.1 361L271.1 433C276.1 438 282.9 440.5 289.9 440C296.9 439.5 303.3 435.9 307.4 430.2L443.3 243.2C451.1 232.5 448.7 217.5 438 209.7z"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="messages-icon messages-icon--error"><path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM231 231C240.4 221.6 255.6 221.6 264.9 231L319.9 286L374.9 231C384.3 221.6 399.5 221.6 408.8 231C418.1 240.4 418.2 255.6 408.8 264.9L353.8 319.9L408.8 374.9C418.2 384.3 418.2 399.5 408.8 408.8C399.4 418.1 384.2 418.2 374.9 408.8L319.9 353.8L264.9 408.8C255.5 418.2 240.3 418.2 231 408.8C221.7 399.4 221.6 384.2 231 374.9L286 319.9L231 264.9C221.6 255.5 221.6 240.3 231 231z"/></svg>
                <span id="contest-vote-messages"></span>
            </p>
            <p class="countdown hidden">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="countdown-icon"><path d="M160 64C142.3 64 128 78.3 128 96C128 113.7 142.3 128 160 128L160 139C160 181.4 176.9 222.1 206.9 252.1L274.8 320L206.9 387.9C176.9 417.9 160 458.6 160 501L160 512C142.3 512 128 526.3 128 544C128 561.7 142.3 576 160 576L480 576C497.7 576 512 561.7 512 544C512 526.3 497.7 512 480 512L480 501C480 458.6 463.1 417.9 433.1 387.9L365.2 320L433.1 252.1C463.1 222.1 480 181.4 480 139L480 128C497.7 128 512 113.7 512 96C512 78.3 497.7 64 480 64L160 64zM224 139L224 128L416 128L416 139C416 158 410.4 176.4 400 192L240 192C229.7 176.4 224 158 224 139zM240 448C243.5 442.7 247.6 437.7 252.1 433.1L320 365.2L387.9 433.1C392.5 437.7 396.5 442.7 400.1 448L240 448z"/></svg>
                <span id="contest-vote-countdown"></span>
            </p>
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