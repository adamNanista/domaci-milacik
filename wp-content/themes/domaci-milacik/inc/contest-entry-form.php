<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    use Rakit\Validation\Validator;

    define( 'CONTEST_ENTRY_SUBMISSIONS_DB_VERSION', '1.0' );

    add_action('after_switch_theme', 'create_contest_entry_submissions_table');

    function create_contest_entry_submissions_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'contest_entry_submissions';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            fingerprint_entry CHAR(64) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY fingerprint_entry (fingerprint_entry)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        update_option( 'contest_entry_submissions_db_version', CONTEST_ENTRY_SUBMISSIONS_DB_VERSION );
    }

    add_action( 'wp_ajax_contest_entry_form_submit_entry', 'contest_entry_form_handle_submission' );
    add_action( 'wp_ajax_nopriv_contest_entry_form_submit_entry', 'contest_entry_form_handle_submission' );

    function contest_entry_form_handle_submission() {
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
        check_ajax_referer( 'contest_entry_form_submit_entry', 'nonce', true );

        /**
         * =========================
         * STATE CONTAINERS
         * =========================
         */
        $errors   = [];
        $rollback = [];

        $post_id    = 0;
        $photo_id   = 0;
        $video_id   = 0;

        $fail = function($message, $fields = []) use (&$rollback) {
            foreach (array_reverse($rollback) as $undo) {
                try {
                    $undo();
                } catch (\Throwable $e) {
                    // optionally log
                }
            }

            wp_send_json_error([
                'message' => $message,
                'fields'  => $fields
            ]);

            wp_die();
        };

        /**
         * =========================
         * HONEYPOT
         * =========================
         */
        if ( ! empty( $_POST['contest-entry-form-website'] ) ) {
            $fail( 'Prihláška zamietnutá.' );
        }

        /**
         * =========================
         * RATE LIMIT
         * =========================
         */
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $salt = get_option( 'contest_entry_form_salt', '' );

        if ( ! $salt ) {
            $salt = wp_generate_password( 64, true, true );
            update_option( 'contest_entry_form_salt', $salt, false );
        }

        $fingerprint_ip = hash_hmac( 'sha256', $ip, $salt );
        $rate_key = 'contest_entry_form_rate_' . $fingerprint_ip;

        $attempts = (int) get_transient( $rate_key );
        
        if ( $attempts >= 3 ) {
            $fail( 'Dosiahli ste limit odoslaní pre túto chvíľu. Skúste to znova o chvíľu.' );
        }

        /**
         * =========================
         * TURNSTILE CAPTCHA
         * =========================
         */
        $turnstile_token = $_POST['cf-turnstile-response'] ?? '';

        if ( empty($turnstile_token) ) {
            $fail( 'Overenie zlyhalo. Skúste to znova.' );
        }

        $response = wp_remote_post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            array(
                'timeout' => 5,
                'body' => array(
                    'secret'   => CLOUDFLARE_TURNSTILE_SECRET_KEY,
                    'response' => $turnstile_token,
                    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ),
            )
        );
        
        if ( is_wp_error( $response ) ) {
            $fail( 'Overenie zlyhalo. Skúste to znova.' );
        }

        $result = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( empty( $result['success'] ) ) {
            $fail( 'Overenie zlyhalo. Skúste to znova.' );
        }

        set_transient( $rate_key, $attempts + 1, 15 * MINUTE_IN_SECONDS );

        /**
         * =========================
         * SANITIZATION
         * =========================
         */
        $owner_name         = isset( $_POST['contest-entry-form-owner-name'] )          ? sanitize_text_field( wp_unslash( $_POST['contest-entry-form-owner-name'] ) )          : '';
        $owner_email        = isset( $_POST['contest-entry-form-owner-email'] )         ? sanitize_email( wp_unslash( $_POST['contest-entry-form-owner-email'] ) )              : '';
        $pet_name           = isset( $_POST['contest-entry-form-pet-name'] )            ? sanitize_text_field( wp_unslash( $_POST['contest-entry-form-pet-name'] ) )            : '';
        $pet_description    = isset( $_POST['contest-entry-form-pet-description'] )     ? sanitize_textarea_field( wp_unslash( $_POST['contest-entry-form-pet-description'] ) ) : '';
        $photo              = isset( $_FILES['contest-entry-form-photo'] ) && $_FILES['contest-entry-form-photo']['error'] === UPLOAD_ERR_OK;
        $video_type         = isset( $_POST['contest-entry-form-video-type'] )          ? sanitize_text_field( wp_unslash( $_POST['contest-entry-form-video-type'] ) )          : 'upload';
        $video_url          = isset( $_POST['contest-entry-form-video-url'] )           ? esc_url_raw( wp_unslash( $_POST['contest-entry-form-video-url'] ) )                   : '';
        $consent_combined   = ! empty( $_POST['contest-entry-form-consent-combined'] )  ? 1                                                                                     : 0;

        /**
         * =========================
         * VALIDATION
         * =========================
         */
        $errors = [];

        /**
         * Rakit validation
         */
        $validator = new Validator;

        $validation = $validator->make( array(
            'contest-entry-form-owner-name'         => $owner_name,
            'contest-entry-form-owner-email'        => $owner_email,
            'contest-entry-form-pet-name'           => $pet_name,
            'contest-entry-form-pet-description'    => $pet_description,
            'contest-entry-form-photo'              => $photo,
            'contest-entry-form-video-url'          => $video_url,
            'contest-entry-form-consent-combined'   => $consent_combined,
        ), array(
            'contest-entry-form-owner-name'         => 'required|max:100',
            'contest-entry-form-owner-email'        => 'required|email|max:100',
            'contest-entry-form-pet-name'           => 'required|max:100',
            'contest-entry-form-pet-description'    => 'required|max:2000',
            'contest-entry-form-photo'              => 'required',
            'contest-entry-form-video-url'          => 'url',
            'contest-entry-form-consent-combined'   => 'required|accepted',
        ) );

        $validation->setMessages( array(
            'contest-entry-form-owner-name:required'        => 'Meno je povinné.',
            'contest-entry-form-owner-name:max'             => 'Meno môže mať maximálne 100 znakov.',
            'contest-entry-form-owner-email:required'       => 'Email je povinný.',
            'contest-entry-form-owner-email:email'          => 'Neplatná emailová adresa.',
            'contest-entry-form-owner-email:max'            => 'Email môže mať maximálne 100 znakov.',
            'contest-entry-form-pet-name:required'          => 'Meno miláčika je povinné.',
            'contest-entry-form-pet-name:max'               => 'Meno miláčika môže mať maximálne 100 znakov.',
            'contest-entry-form-pet-description:required'   => 'Popis miláčika je povinný.',
            'contest-entry-form-pet-description:max'        => 'Popis miláčika môže mať maximálne 2 000 znakov.',
            'contest-entry-form-photo:required'             => 'Fotografia je povinná.',
            'contest-entry-form-video-url:url'              => 'Zadajte platnú URL adresu.',
            'contest-entry-form-consent-combined:required'  => 'Súhlas je povinný.',
            'contest-entry-form-consent-combined:accepted'  => 'Súhlas je povinný.',
        ) );

        $validation->validate();

        if ($validation->fails()) {
            $errors = array_merge($errors, $validation->errors()->firstOfAll());
        }

        /**
         * Photo validation
         */
        if (!isset($_FILES['contest-entry-form-photo'])) {
            $errors['contest-entry-form-photo'] = 'Fotografia je povinná.';
        } else {
            $file_error = $_FILES['contest-entry-form-photo']['error'];

            if ($file_error === UPLOAD_ERR_NO_FILE) {
                $errors['contest-entry-form-photo'] = 'Fotografia je povinná.';
            } elseif ($file_error === UPLOAD_ERR_INI_SIZE || $file_error === UPLOAD_ERR_FORM_SIZE) {
                $errors['contest-entry-form-photo'] = 'Fotografia je príliš veľká.';
            } elseif ($file_error !== UPLOAD_ERR_OK) {
                $errors['contest-entry-form-photo'] = 'Nahrávanie fotografie zlyhalo.';
            } elseif ($_FILES['contest-entry-form-photo']['size'] > 5 * MB_IN_BYTES) {
                $errors['contest-entry-form-photo'] = 'Fotografia musí mať menej ako 5 MB.';
            }
        }

        /**
         * Video validation
         */
        if ( $video_type === 'upload' && isset( $_FILES['contest-entry-form-video-upload'] ) && $_FILES['contest-entry-form-video-upload']['error'] !== UPLOAD_ERR_NO_FILE ) {
            $file_error = $_FILES['contest-entry-form-video-upload']['error'];

            if ($file_error === UPLOAD_ERR_INI_SIZE || $file_error === UPLOAD_ERR_FORM_SIZE) {
                $errors['contest-entry-form-video-upload'] = 'Video je príliš veľké.';
            } elseif ($file_error !== UPLOAD_ERR_OK) {
                $errors['contest-entry-form-video-upload'] = 'Nahrávanie videa zlyhalo.';
            } elseif ($_FILES['contest-entry-form-video-upload']['size'] > 30 * MB_IN_BYTES) {
                $errors['contest-entry-form-video-upload'] = 'Video musí mať menej ako 30 MB.';
            }
        } elseif ( $video_type === 'url' && ! empty( $video_url ) ) {
            $host = wp_parse_url( $video_url, PHP_URL_HOST );
            $host = preg_replace( '/^www\./', '', strtolower( $host ) );
            
            $allowed_hosts = array(
                'youtube.com',
                'youtu.be',
                'vimeo.com',
            );

            if ( ! $host || ! in_array( $host, $allowed_hosts, true ) ) {
                $errors['contest-entry-form-video-url'] = 'Povolené sú iba odkazy na YouTube alebo Vimeo';
            }
        }

        if ( ! empty( $errors ) ) {
            $fail( '', $errors );
        }

        /**
         * =========================
         * DUPLICATE CHECK
         * =========================
         */
        $fingerprint_entry = hash_hmac( 'sha256', strtolower( trim( $owner_email . '|' . $pet_name ) ), $salt );
        $entry_key = 'contest_entry_form_submission_' . $fingerprint_entry;

        if ( get_transient( $entry_key ) ) {
            $fail( 'Táto prihláška už bola odoslaná.' );
        }

        global $wpdb;

        $wpdb->suppress_errors( true );

        $inserted = $wpdb->insert(
            $wpdb->prefix . 'contest_entry_submissions',
            array(
                'fingerprint_entry' => $fingerprint_entry,
                'created_at'        => gmdate( 'Y-m-d H:i:s' )
            ),
            array( '%s' )
        );

        $wpdb->suppress_errors( false );

        if ( $inserted === false ) {
            $fail( 'Táto prihláška už bola odoslaná.' );
        }
        
        /**
         * =========================
         * PHOTO UPLOAD
         * =========================
         */
        $tmp_photo = $_FILES['contest-entry-form-photo']['tmp_name'];

        $photo_finfo = finfo_open( FILEINFO_MIME_TYPE );
        $photo_real_mime = finfo_file( $photo_finfo, $tmp_photo );
        finfo_close( $photo_finfo );

        if ( ! in_array( $photo_real_mime, [ 'image/jpeg', 'image/png' ], true ) ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Nepodporovaný formát obrázku. Povolené sú iba JPG a PNG.' ] );
        }

        $photo_size = @getimagesize( $tmp_photo );
        if ( false === $photo_size ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Súbor nie je platný obrázok.' ] );
        }

        $max_dimension = 10000;

        if ( $photo_size[0] > $max_dimension || $photo_size[1] > $max_dimension ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Obrázok je príliš veľký. Prosím, zmenšite ho pred nahraním.' ] );
        }

        $estimated_memory = $photo_size[0] * $photo_size[1] * 4;
        $limit = 100 * MB_IN_BYTES;

        if ( $estimated_memory > $limit ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Súbor je príliš náročný na spracovanie.' ] );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        try {
            add_filter( 'upload_mimes', 'contest_entry_form_image_mimes' );
            $photo_id = media_handle_upload( 'contest-entry-form-photo', 0 );
        } finally {
            remove_filter( 'upload_mimes', 'contest_entry_form_image_mimes' );
        }

        if ( is_wp_error( $photo_id ) ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Nahrávanie fotografie zlyhalo: ' . $photo_id->get_error_message() ] );
        }

        $rollback[] = function() use ( $photo_id ) {
            wp_delete_attachment( $photo_id, true );
        };

        $photo_file = get_attached_file( $photo_id );
        $editor = wp_get_image_editor( $photo_file );

        if ( is_wp_error( $editor ) ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Nepodporovaný formát fotografie.' ] );
        }

        $editor->resize( 2560, 2560, false );
        $saved = $editor->save( $photo_file );

        if ( is_wp_error( $saved ) ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Nepodarilo sa spracovať fotografiu.' ] );
        }

        $metadata = wp_generate_attachment_metadata( $photo_id, $photo_file );
        wp_update_attachment_metadata( $photo_id, $metadata );

        update_post_meta( $photo_id, '_wp_attachment_image_alt', $pet_name );

        /**
         * =========================
         * VIDEO HANDLING
         * =========================
         */
        $final_video_url = '';

        if ( $video_type === 'upload' && isset( $_FILES['contest-entry-form-video-upload'] ) && $_FILES['contest-entry-form-video-upload']['error'] !== UPLOAD_ERR_NO_FILE ) {
            $tmp_video = $_FILES['contest-entry-form-video-upload']['tmp_name'];

            $video_finfo = finfo_open( FILEINFO_MIME_TYPE );
            $video_real_mime = finfo_file( $video_finfo, $tmp_video );
            finfo_close( $video_finfo );

            if ( ! in_array( $video_real_mime, [ 'video/mp4' ], true ) ) {
                $fail( '', [ 'contest-entry-form-video-upload' => 'Nepodporovaný formát videa. Povolené je iba MP4.' ] );
            }

            try {
                add_filter( 'upload_mimes', 'contest_entry_form_video_mimes' );
                $video_id = media_handle_upload( 'contest-entry-form-video-upload', 0 );
            } finally {
                remove_filter( 'upload_mimes', 'contest_entry_form_video_mimes' );
            }

            if ( is_wp_error( $video_id ) ) {
                $fail( '', [ 'contest-entry-form-video-upload' => 'Nahrávanie videa zlyhalo: ' . $video_id->get_error_message() ] );
            }

            $rollback[] = function() use ( $video_id ) {
                wp_delete_attachment( $video_id, true );
            };

            $final_video_url = wp_get_attachment_url( $video_id );
        } elseif ( $video_type === 'url' && ! empty( $video_url ) ) {
            $final_video_url = $video_url;
        }

        /**
         * =========================
         * POST CREATION
         * =========================
         */
        $post_id = wp_insert_post( array(
            'post_type'    => 'contest_entry',
            'post_title'   => $pet_name,
            'post_content' => $pet_description,
            'post_status'  => 'pending',
        ), true );

        if ( is_wp_error( $post_id ) ) {
            $fail( 'Vašu prihlášku sa nepodarilo uložiť. Skúste to prosím znova.' );
        }

        $rollback[] = function() use ( $post_id ) {
            wp_delete_post( $post_id, true );
        };

        /**
         * =========================
         * FINAL SAVE
         * =========================
         */
        update_post_meta( $post_id, '_contest_entry_form_photo',                $photo_id );
        update_post_meta( $post_id, '_contest_entry_form_consent_combined',     $consent_combined );
        update_post_meta( $post_id, '_contest_entry_form_ip_hash',              $fingerprint_ip );
        update_post_meta( $post_id, '_contest_entry_form_submitted_at',         gmdate( 'Y-m-d H:i:s' ) );

        if ( function_exists('update_field') ) {
            update_field( 'owner_name',     $owner_name,        $post_id );
            update_field( 'owner_email',    $owner_email,       $post_id );
            update_field( 'video_url',      $final_video_url,   $post_id );
        } else {
            update_post_meta( $post_id, '_owner_name',  $owner_name );
            update_post_meta( $post_id, '_owner_email', $owner_email );
            update_post_meta( $post_id, '_video_url',   $final_video_url );
        }  

        set_post_thumbnail( $post_id, $photo_id );

        set_transient($entry_key, 1, DAY_IN_SECONDS);

        /**
         * =========================
         * GENERATE OG IMAGE
         * =========================
         */
        generate_contest_entry_og_image( $post_id );

        /**
         * =========================
         * SEND THANK YOU EMAIL
         * =========================
         */
        send_thank_you_email( $post_id );

        /**
         * =========================
         * SUCCESS
         * =========================
         */
        wp_send_json_success( array( 'message' => 'Ďakujeme! Vaša prihláška bola prijatá a čaká na schválenie.', ) );

        wp_die();
    }

    function contest_entry_form_image_mimes() {
        return array(
            'jpg|jpeg'  => 'image/jpeg',
            'png'       => 'image/png',
        );
    }
    
    function contest_entry_form_video_mimes() {
        return array(
            'mp4' => 'video/mp4',
        );
    }

    function send_thank_you_email( $post_id ) {
        if ( get_post_type( $post_id ) !== 'contest_entry' ) {
            return;
        }

        $owner_email    = get_field( 'owner_email' );
        $pet_name       = get_the_title( $post_id );
        $photo_url      = get_the_post_thumbnail_url( $post_id, 'thumbnail' );

        if ( ! $owner_email ) {
            return;
        }

        $subject    = 'Ďakujeme! Vaša prihláška bola prijatá a čaká na schválenie.';
        $body       = get_thank_you_email_html( $pet_name, $photo_url );
        $headers    = [ 'Content-Type: text/html; charset=UTF-8' ];

        wp_mail( $owner_email, $subject, $body, $headers );
    }

    function get_thank_you_email_html( $pet_name, $photo_url ) {
        ob_start();

        $template_path = get_template_part( 'templates/email/thank-you', null, array(
            'pet_name'  => $pet_name,
            'photo_url' => $photo_url,
        ) );

        return ob_get_clean();
    }

    function generate_contest_entry_og_image( $post_id ) {
        $featured_image_id = get_post_thumbnail_id( $post_id );

        if ( ! $featured_image_id ) {
            return null;
        }

        $featured_image_path = get_attached_file( $featured_image_id );

        if ( ! file_exists( $featured_image_path ) ) {
            return null;
        }

        $template_path = get_stylesheet_directory() . '/assets/img/og-template.png';

        if ( ! file_exists( $template_path ) ) {
            return null;
        }

        try {
            $canvas_width  = 1200;
            $canvas_height = 630;

            $source = new Imagick( $featured_image_path );

            $canvas = new Imagick();

            $canvas->newImage(
                $canvas_width,
                $canvas_height,
                new ImagickPixel( 'white' )
            );
            
            $canvas->setImageFormat( 'jpg' );

            $source->cropThumbnailImage(
                $canvas_width / 2,
                $canvas_height
            );

            $canvas->compositeImage(
                $source,
                Imagick::COMPOSITE_OVER,
                $canvas_width / 2,
                0
            );

            $template = new Imagick( $template_path );

            $canvas->compositeImage(
                $template,
                Imagick::COMPOSITE_OVER,
                0,
                0
            );

            $upload_dir = wp_upload_dir();

            $filename = '' . $post_id . '-og.jpg';

            $filepath = trailingslashit( $upload_dir['basedir'] ) . $filename;

            $canvas->writeImage( $filepath );

            $url = trailingslashit( $upload_dir['baseurl'] ) . $filename;

            update_post_meta(
                $post_id,
                '_contest_og_image',
                $url
            );

            $source->clear();
            $canvas->clear();
            $template->clear();

            return $url;
        } catch ( Exception $error ) {
            error_log( $error->getMessage() );

            return null;
        }
    }