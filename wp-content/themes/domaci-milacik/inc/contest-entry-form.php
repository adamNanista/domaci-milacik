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

    add_action( 'wp_enqueue_scripts', 'enqueue_contest_entry_form_assets' );

    function enqueue_contest_entry_form_assets() {
        if ( is_page( 10 ) ) {
            wp_enqueue_script(
                'just-validate',
                'https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js',
                array(),
                false,
                true,
            );

            wp_enqueue_script(
                'cf-turnstile',
                'https://challenges.cloudflare.com/turnstile/v0/api.js',
                array(),
                null,
                true,
            );

            wp_enqueue_script(
                'contest-entry-form',
                get_stylesheet_directory_uri() . '/assets/js/contest-entry-form.js',
                array( 'just-validate', 'cf-turnstile' ),
                filemtime( get_stylesheet_directory() . '/assets/js/contest-entry-form.js' ),
                true,
            );

            wp_localize_script( 'contest-entry-form', 'contest_entry_form_ajax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'contest_entry_form_submit_entry' ),
            ) );
        }
    }

    add_shortcode( 'contest_entry_form', 'render_contest_entry_form' );

    function render_contest_entry_form() {
        ob_start();

        ?>
            <form class="grid gap-6 md:grid-cols-2" id="contest-entry-form" enctype="multipart/form-data" novalidate="novalidate">
                <div class="space-y-6">
                    <label class="field">
                        <span class="field-label">Meno <abbr class="required" title="Povinné">*</abbr></span>
                        <input class="field-input" type="text" id="contest-entry-form-owner-name" name="contest-entry-form-owner-name" required placeholder="Zadajte meno" />
                    </label>
                    <label class="field">
                        <span class="field-label">Email <abbr class="required" title="Povinné">*</abbr></span>
                        <input class="field-input" type="email" id="contest-entry-form-owner-email" name="contest-entry-form-owner-email" required placeholder="Zadajte email" />
                    </label>
                    <label class="field">
                        <span class="field-label">Meno miláčika <abbr class="required" title="Povinné">*</abbr></span>
                        <input class="field-input" type="text" id="contest-entry-form-pet-name" name="contest-entry-form-pet-name" required placeholder="Zadajte meno miláčika" />
                    </label>
                    <label class="field">
                        <span class="field-label">Popis miláčika <abbr class="required" title="Povinné">*</abbr></span>
                        <textarea class="field-textarea" id="contest-entry-form-pet-description" name="contest-entry-form-pet-description" required placeholder="Napíšte niečo o svojom miláčikovi" rows="4"></textarea>
                    </label>
                </div>
                <div class="space-y-6">
                    <fieldset class="dropzone">
                        <legend class="dropzone-legend">Fotografia miláčika <abbr class="required" title="Povinné">*</abbr></legend>
                        <label class="dropzone-area" id="contest-entry-form-photo-panel">
                            <input class="visually-hidden" type="file" id="contest-entry-form-photo" name="contest-entry-form-photo" accept="image/jpeg,image/png" required />
                            <span class="dropzone-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-image-icon lucide-image"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                            </span>
                            <span class="dropzone-label">Pretiahnite fotku vášho miláčika sem</span>
                            <span class="dropzone-hint">alebo kliknite a vyberte zo zariadenia · JPG / PNG · max 5 MB</span>
                            <span class="dropzone-button button button-sm button-primary">Vybrať súbor</span>
                        </label>
                    </fieldset>
                    <fieldset class="dropzone">
                        <legend class="dropzone-legend">Video miláčika (voliteľné)</legend>
                        <div class="dropzone-tabs tabs">
                            <label class="tab">
                                <input class="visually-hidden" type="radio" name="contest-entry-form-video-type" value="url" checked /> 
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-link-icon lucide-link"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                Vložiť URL
                            </label>
                            <label class="tab">
                                <input class="visually-hidden" type="radio" name="contest-entry-form-video-type" value="upload" />
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-upload-icon lucide-upload"><path d="M12 3v12"/><path d="m17 8-5-5-5 5"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/></svg>
                                Nahrať video
                            </label>
                        </div>
                        <label class="field" id="contest-entry-form-video-url-panel">
                            <span class="field-label screen-reader-text">Vložiť URL</span>
                            <input class="field-input" type="url" id="contest-entry-form-video-url" name="contest-entry-form-video-url" placeholder="https://youtube.com/watch?v=..." />
                        </label>
                        <label class="dropzone-area hidden" id="contest-entry-form-video-upload-panel">
                            <input class="visually-hidden" type="file" id="contest-entry-form-video-upload" name="contest-entry-form-video-upload" accept="video/mp4" />
                            <span class="dropzone-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-video-icon lucide-video"><path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"/><rect x="2" y="6" width="14" height="12" rx="2"/></svg>
                            </span>
                            <span class="dropzone-label">Pretiahnite video vášho miláčika sem</span>
                            <span class="dropzone-hint">alebo kliknite a vyberte zo zariadenia · MP4 · max 30 MB</span>
                            <span class="dropzone-button button button-sm button-primary">Vybrať súbor</span>
                        </label>
                    </fieldset>
                </div>
                <div class="md:col-span-full">
                    <label class="field hidden">
                        <span class="field-label">Webstránka</span>
                        <input class="input" type="text" id="contest-entry-form-website" name="contest-entry-form-website" autocomplete="off" />
                    </label>
                    <div>
                        <label class="field checkbox">
                            <input class="field-checkbox" type="checkbox" id="contest-entry-form-consent-combined" name="contest-entry-form-consent-combined" required /> 
                            <span class="field-label">Súhlasím s <a href="#">pravidlami súťaže</a> a so spracovaním osobných údajov. <abbr class="required" title="Povinné">*</abbr></span>
                        </label>
                    </div>
                    <div id="contest-entry-form-turnstile" class="cf-turnstile" data-sitekey="<?php echo CLOUDFLARE_TURNSTILE_SITE_KEY; ?>"></div>
                </div>
                <div class="md:col-span-full">
                    <button id="contest-entry-form-submit" class="button button-lg button-wide button-primary" type="submit">Odoslať prihlášku</button>
                </div>
                <p class="messages hidden md:col-span-full">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="messages-icon success"><path d="M320 576C178.6 576 64 461.4 64 320C64 178.6 178.6 64 320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576zM438 209.7C427.3 201.9 412.3 204.3 404.5 215L285.1 379.2L233 327.1C223.6 317.7 208.4 317.7 199.1 327.1C189.8 336.5 189.7 351.7 199.1 361L271.1 433C276.1 438 282.9 440.5 289.9 440C296.9 439.5 303.3 435.9 307.4 430.2L443.3 243.2C451.1 232.5 448.7 217.5 438 209.7z"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="messages-icon error"><path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM231 231C240.4 221.6 255.6 221.6 264.9 231L319.9 286L374.9 231C384.3 221.6 399.5 221.6 408.8 231C418.1 240.4 418.2 255.6 408.8 264.9L353.8 319.9L408.8 374.9C418.2 384.3 418.2 399.5 408.8 408.8C399.4 418.1 384.2 418.2 374.9 408.8L319.9 353.8L264.9 408.8C255.5 418.2 240.3 418.2 231 408.8C221.7 399.4 221.6 384.2 231 374.9L286 319.9L231 264.9C221.6 255.5 221.6 240.3 231 231z"/></svg>
                    <span id="contest-entry-form-messages"></span>
                </p>
            </form>
        <?php

        return ob_get_clean();
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

        $post_id                = 0;
        $photo_id               = 0;
        $video_id               = 0;

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
            $fail( 'Dosiahli ste limit odoslaní pre túto chvíľu. Skúste to znova o 15 minút.' );
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
        $photo_url      = get_post_thumbnail_url( $post_id, 'thumbnail' );

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