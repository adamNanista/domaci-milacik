<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    use Rakit\Validation\Validator;

    add_action( 'wp_enqueue_scripts', 'enqueue_contest_entry_form_assets' );

    function enqueue_contest_entry_form_assets() {
        global $post;

        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'contest_entry_form' ) ) {
            wp_enqueue_script(
                'just-validate',
                'https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js',
                array(),
                false,
                true
            );

            wp_enqueue_style(
                'contest-entry-form',
                get_stylesheet_directory_uri() . '/assets/css/contest-entry-form.css',
                array(),
                filemtime( get_stylesheet_directory() . '/assets/css/contest-entry-form.css' ),
            );

            wp_enqueue_script(
                'contest-entry-form',
                get_stylesheet_directory_uri() . '/assets/js/contest-entry-form.js',
                array( 'just-validate' ),
                filemtime( get_stylesheet_directory() . '/assets/js/contest-entry-form.js' ),
                true
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
            <form id="contest-entry-form" enctype="multipart/form-data" novalidate="novalidate">
                <div>
                    <label for="contest-entry-form-owner-name">Meno súťažiaceho <abbr title="Povinné">*</abbr></label>
                    <input type="text" id="contest-entry-form-owner-name" name="contest-entry-form-owner-name" required placeholder="Zadajte meno súťažiaceho" />
                </div>
                <div>
                    <label for="contest-entry-form-owner-email">Email <abbr title="Povinné">*</abbr></label>
                    <input type="email" id="contest-entry-form-owner-email" name="contest-entry-form-owner-email" required placeholder="Zadajte email" />
                </div>
                <div>
                    <label for="contest-entry-form-pet-name">Názov miláčika <abbr title="Povinné">*</abbr></label>
                    <input type="text" id="contest-entry-form-pet-name" name="contest-entry-form-pet-name" required placeholder="Zadajte názov miláčika" />
                </div>
                <div>
                    <label for="contest-entry-form-pet-description">Popis miláčika <abbr title="Povinné">*</abbr></label>
                    <textarea id="contest-entry-form-pet-description" name="contest-entry-form-pet-description" required placeholder="Napšte niečo o svojom miláčikovi..."></textarea>
                </div>
                <div>
                    <label for="contest-entry-form-photo">Fotografia <abbr title="Povinné">*</abbr></label>
                    <input type="file" id="contest-entry-form-photo" name="contest-entry-form-photo" accept="image/jpeg,image/png" required />
                </div>
                <div>
                    <fieldset>
                        <legend>Video (voliteľné)</legend>
                        <div class="contest-entry-form-toggle">
                            <label>
                                <input type="radio" name="contest-entry-form-video-type" value="upload" checked /> Nahrať video
                            </label>
                            <label>
                                <input type="radio" name="contest-entry-form-video-type" value="url" /> Vložiť URL
                            </label>
                        </div>
                        <div id="contest-entry-form-video-upload-panel">
                            <label for="contest-entry-form-video-upload" class="screen-reader-text">Nahrať video</label>
                            <input type="file" id="contest-entry-form-video-upload" name="contest-entry-form-video-upload" accept="video/mp4" />
                        </div>
                        <div id="contest-entry-form-video-url-panel" class="hidden">
                            <label for="contest-entry-form-video-url" class="screen-reader-text">Vložiť URL</label>
                            <input type="url" id="contest-entry-form-video-url" name="contest-entry-form-video-url" placeholder="https://youtube.com/watch?v=..." />
                        </div>
                    </fieldset>
                </div>
                <div class="hidden">
                    <label for="contest-entry-form-website">Webstránka</label>
                    <input type="text" id="contest-entry-form-website" name="contest-entry-form-website" autocomplete="off" />
                </div>
                <div id="contest-entry-form-messages"></div>
                <div>
                    <label>
                        <input type="checkbox" id="contest-entry-form-consent-combined" name="contest-entry-form-consent-combined" required /> Súhlasím s <a href="#">pravidlami súťaže</a> a so spracovaním osobných údajov. <abbr title="Povinné">*</abbr>
                    </label>
                </div>
                <div>
                    <button type="submit" id="contest-entry-form-submit">
                        <span id="contest-entry-form-submit-text">Odoslať prihlášku</span>
                        <span id="contest-entry-form-submit-loading" class="hidden">Odosielam</span>
                    </button>
                </div>
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

        $created_post_id      = 0;
        $photo_id             = 0;
        $video_attachment_id  = 0;

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
        };

        /**
         * =========================
         * RATE LIMIT
         * =========================
         */
        $ip_hash = hash( 'sha256', $_SERVER['REMOTE_ADDR'] ?? '' );
        $rate_key = 'contest_entry_form_rate_' . $ip_hash;

        $attempts = (int) get_transient( $rate_key );
        
        if ( $attempts >= 5 ) {
            $fail( 'Príliš veľa pokusov v krátkom čase. Počkajte niekoľko minút a skúste to znova.' );
        }

        set_transient( $rate_key, $attempts + 1, 15 * MINUTE_IN_SECONDS );

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
         * SANITIZATION
         * =========================
         */
        $owner_name         = isset( $_POST['contest-entry-form-owner-name'] )          ? sanitize_text_field( wp_unslash( $_POST['contest-entry-form-owner-name'] ) )          : '';
        $owner_email        = isset( $_POST['contest-entry-form-owner-email'] )         ? sanitize_email( wp_unslash( $_POST['contest-entry-form-owner-email'] ) )              : '';
        $pet_name           = isset( $_POST['contest-entry-form-pet-name'] )            ? sanitize_text_field( wp_unslash( $_POST['contest-entry-form-pet-name'] ) )            : '';
        $pet_description    = isset( $_POST['contest-entry-form-pet-description'] )     ? sanitize_textarea_field( wp_unslash( $_POST['contest-entry-form-pet-description'] ) ) : '';
        $photo_uploaded     = isset( $_FILES['contest-entry-form-photo'] ) && $_FILES['contest-entry-form-photo']['error'] === UPLOAD_ERR_OK;
        $video_type         = isset( $_POST['contest-entry-form-video-type'] )          ? sanitize_text_field( wp_unslash( $_POST['contest-entry-form-video-type'] ) )          : 'upload';
        $video_url          = isset( $_POST['contest-entry-form-video-url'] )           ? esc_url_raw( wp_unslash( $_POST['contest-entry-form-video-url'] ) )                   : '';
        $consent_combined   = ! empty( $_POST['contest-entry-form-consent-combined'] )  ? 1                                                                                     : 0;

        /**
         * =========================
         * DUPLICATE CHECK
         * =========================
         */
        $fingerprint = wp_hash(strtolower(trim($owner_email . '|' . $pet_name)));
        $fingerprint_key = 'contest_entry_form_submission_' . $fingerprint;

        if ( get_transient( $fingerprint_key ) ) {
            $fail( 'Táto prihláška už bola odoslaná.' );
        }

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
            'contest-entry-form-photo'              => $photo_uploaded,
            'contest-entry-form-video-url'          => $video_url,
            'contest-entry-form-consent-combined'   => $consent_combined,
        ), array(
            'contest-entry-form-owner-name'         => 'required',
            'contest-entry-form-owner-email'        => 'required|email',
            'contest-entry-form-pet-name'           => 'required',
            'contest-entry-form-pet-description'    => 'required',
            'contest-entry-form-photo'              => 'required',
            'contest-entry-form-video-url'          => 'url',
            'contest-entry-form-consent-combined'   => 'required|accepted',
        ) );

        $validation->setMessages( array(
            'contest-entry-form-owner-name:required'        => 'Meno je povinné.',
            'contest-entry-form-owner-email:required'       => 'Email je povinný.',
            'contest-entry-form-owner-email:email'          => 'Neplatná emailová adresa.',
            'contest-entry-form-pet-name:required'          => 'Meno miláčika je povinné.',
            'contest-entry-form-pet-description:required'   => 'Popis miláčika je povinný.',
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
         * PHOTO UPLOAD
         * =========================
         */
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        add_filter( 'upload_mimes', 'contest_entry_form_image_mimes' );
        $photo_id = media_handle_upload( 'contest-entry-form-photo', 0 );
        remove_filter( 'upload_mimes', 'contest_entry_form_image_mimes' );

        if ( is_wp_error( $photo_id ) ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Nahrávanie fotografie zlyhalo: ' . $photo_id->get_error_message() ] );
        }

        $rollback[] = function() use ( $photo_id ) {
            wp_delete_attachment( $photo_id, true );
        };

        $photo_file = get_attached_file( $photo_id );
        $photo_check = wp_check_filetype_and_ext( $photo_file, basename( $photo_file ) );

        if ( ! in_array( $photo_check['type'], ['image/jpeg', 'image/png'], true ) ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Nepodporovaný formát fotografie.' ] );
        }

        $editor = wp_get_image_editor( $photo_file );

        if ( is_wp_error( $editor ) ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Nepodporovaný formát fotografie.' ] );
        }

        $saved = $editor->save( $photo_file );

        if ( is_wp_error( $saved ) ) {
            $fail( '', [ 'contest-entry-form-photo' => 'Nepodarilo sa spracovať fotografiu.' ] );
        }

        update_post_meta( $photo_id, '_wp_attachment_image_alt', $pet_name );

        /**
         * =========================
         * VIDEO HANDLING
         * =========================
         */
        $final_video_url = '';

        if ( $video_type === 'upload' && isset( $_FILES['contest-entry-form-video-upload'] ) && $_FILES['contest-entry-form-video-upload']['error'] !== UPLOAD_ERR_NO_FILE ) {
            add_filter( 'upload_mimes', 'contest_entry_form_video_mimes' );
            $video_id = media_handle_upload( 'contest-entry-form-video-upload', 0 );
            remove_filter( 'upload_mimes', 'contest_entry_form_video_mimes' );

            if ( is_wp_error( $video_id ) ) {
                $fail( '', [ 'contest-entry-form-video-upload' => 'Nahrávanie videa zlyhalo: ' . $video_id->get_error_message() ] );
            }

            $rollback[] = function() use ( $video_id ) {
                wp_delete_attachment( $video_id, true );
            };

            $video_file = get_attached_file( $video_id );
            $video_check = wp_check_filetype_and_ext( $video_file, basename( $video_file ) );

            if ( ! in_array( $video_check['type'], ['video/mp4'], true ) ) {
                $fail( '', [ 'contest-entry-form-video-upload' => 'Nepodporovaný formát videa.' ] );
            }

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
        update_post_meta( $post_id, '_cef_consent_combined',    $consent_combined );
        update_post_meta( $post_id, '_cef_ip_hash',             $ip_hash );
        update_post_meta( $post_id, '_cef_submitted_at',        current_time( 'mysql' ) );

        update_field( 'owner_name', $owner_name, $post_id );
        update_field( 'owner_email', $owner_email, $post_id );
        update_field( 'video_url', $final_video_url, $post_id );

        set_post_thumbnail( $post_id, $photo_id );

        set_transient($fingerprint_key, 1, DAY_IN_SECONDS);

        /**
         * =========================
         * SUCCESS
         * =========================
         */
        wp_send_json_success( array( 'message' => 'Ďakujeme! Vaša prihláška bola prijatá a čaká na schválenie.', ) );
    }

    function contest_entry_form_image_mimes() {
        return array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png'          => 'image/png',
        );
    }
    
    function contest_entry_form_video_mimes() {
        return array(
            'mp4|m4v' => 'video/mp4',
        );
    }
