<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    add_action( 'wp_enqueue_scripts', 'enqueue_contest_entry_form_assets' );

    function enqueue_contest_entry_form_assets() {
        global $post;

        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'contest_entry_form' ) ) {
            wp_enqueue_style(
                'contest-entry-form',
                get_stylesheet_directory_uri() . '/assets/css/contest-entry-form.css',
                array(),
                filemtime(get_stylesheet_directory() . '/assets/css/contest-entry-form.css'),
            );

            wp_enqueue_script(
                'contest-entry-form',
                get_stylesheet_directory_uri() . '/assets/js/contest-entry-form.js',
                array( 'jquery' ),
                filemtime(get_stylesheet_directory() . '/assets/js/contest-entry-form.js'),
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
            <form id="contest-entry-form" enctype="multipart/form-data" novalidate>
                <div>
                    <label for="contest-entry-form-name">Meno súťažiaceho <abbr title="Povinné">*</abbr></label>
                    <input type="text" id="contest-entry-form-name" name="contest-entry-form-name" required placeholder="Zadajte meno súťažiaceho" />
                </div>
                <div>
                    <label for="contest-entry-form-email">Email <abbr title="Povinné">*</abbr></label>
                    <input type="email" id="contest-entry-form-email" name="contest-entry-form-email" required placeholder="Zadajte email" />
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
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'contest_entry_form_submit_entry' ) ) {
            wp_send_json_error( array( 'message' => 'Security check failed. Please refresh the page and try again.' ) );
        }

        // Honeypot
        if ( ! empty( $_POST['contest-entry-form-website'] ) ) {
            wp_send_json_error( array( 'message' => 'Submission rejected.' ) );
        }

        // Sanitize fields
        $name               = isset( $_POST['contest-entry-form-name'] )                ? sanitize_text_field( wp_unslash( $_POST['contest-entry-form-name'] ) )                : '';
        $email              = isset( $_POST['contest-entry-form-email'] )               ? sanitize_email( wp_unslash( $_POST['contest-entry-form-email'] ) )                    : '';
        $pet_name           = isset( $_POST['contest-entry-form-pet-name'] )            ? sanitize_text_field( wp_unslash( $_POST['contest-entry-form-pet-name'] ) )            : '';
        $pet_description    = isset( $_POST['contest-entry-form-pet-description'] )     ? sanitize_textarea_field( wp_unslash( $_POST['contest-entry-form-pet-description'] ) ) : '';
        $video_type         = isset( $_POST['contest-entry-form-video-type'] )          ? sanitize_text_field( wp_unslash( $_POST['contest-entry-form-video-type'] ) )          : 'upload';
        $video_url          = isset( $_POST['contest-entry-form-video-url'] )           ? esc_url_raw( wp_unslash( $_POST['contest-entry-form-video-url'] ) )                   : '';
        $consent_comibned   = !empty( $_POST['contest-entry-form-consent-combined'] )   ? 1                                                                                     : 0;

        // Validate required fields
        $errors = array();
        if ( empty( $name ) )                                       $errors[] = 'Name is required.';
        if ( empty( $email ) || ! is_email( $email ) )              $errors[] = 'A valid email address is required.';
        if ( empty( $pet_name ) )                                   $errors[] = 'Pet name is required.';
        if ( empty( $pet_description ) )                            $errors[] = 'Pet description is required.';
        if ( empty( $_FILES['contest-entry-form-photo']['name'] ) ) $errors[] = 'A photo is required.';
        if ( empty( $consent_comibned ) )                           $errors[] = 'Consent is required.';
    
        if ( ! empty( $errors ) ) {
            wp_send_json_error( array( 'message' => implode( ' ', $errors ) ) );
        }

        // Load WP upload helpers
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Validate photo type & size
        $allowed_photo_types = array( 'image/jpeg', 'image/png' );
        if ( ! in_array( $_FILES['contest-entry-form-photo']['type'], $allowed_photo_types, true ) ) {
            wp_send_json_error( array( 'message' => 'Invalid photo type. Please upload a JPG or PNG image.' ) );
        }
        if ( $_FILES['contest-entry-form-photo']['size'] > 5 * MB_IN_BYTES ) {
            wp_send_json_error( array( 'message' => 'Photo must be under 5 MB.' ) );
        }

        // Upload photo
        add_filter( 'upload_mimes', 'contest_entry_form_image_mimes' );
        $photo_id = media_handle_upload( 'contest-entry-form-photo', 0 );
        remove_filter( 'upload_mimes', 'contest_entry_form_image_mimes' );
    
        if ( is_wp_error( $photo_id ) ) {
            wp_send_json_error( array( 'message' => 'Photo upload failed: ' . $photo_id->get_error_message() ) );
        }

        // Handle optional video
        $video_attachment_id = 0;
        $final_video_url     = '';

        if ( $video_type === 'upload' && ! empty( $_FILES['contest-entry-form-video-upload']['name'] ) ) {
            $allowed_video_types = array( 'video/mp4' );
            if ( ! in_array( $_FILES['contest-entry-form-video-upload']['type'], $allowed_video_types, true ) ) {
                wp_send_json_error( array( 'message' => 'Invalid video type. Please upload an MP4, MOV, or AVI file.' ) );
            }
            if ( $_FILES['contest-entry-form-video-upload']['size'] > 30 * MB_IN_BYTES ) {
                wp_send_json_error( array( 'message' => 'Video must be under 30 MB.' ) );
            }
    
            add_filter( 'upload_mimes', 'contest_entry_form_video_mimes' );
            $video_id = media_handle_upload( 'contest-entry-form-video-upload', 0 );
            remove_filter( 'upload_mimes', 'contest_entry_form_video_mimes' );
    
            if ( is_wp_error( $video_id ) ) {
                wp_send_json_error( array( 'message' => 'Video upload failed: ' . $video_id->get_error_message() ) );
            }
    
            $video_attachment_id = $video_id;
            $final_video_url     = wp_get_attachment_url( $video_id );
    
        } elseif ( $video_type === 'url' && ! empty( $video_url ) ) {
            $final_video_url = $video_url;
        }

        // Create the post
        $post_id = wp_insert_post( array(
            'post_type'    => 'contest_entry',
            'post_title'   => $name,
            'post_content' => $pet_description,
            'post_status'  => 'pending',
        ), true );

        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( array( 'message' => 'Could not save your entry. Please try again.' ) );
        }

        // Save meta
        update_post_meta( $post_id, '_cef_email',               $email );
        update_post_meta( $post_id, '_cef_pet_name',            $pet_name );
        update_post_meta( $post_id, '_cef_photo_id',            $photo_id );
        update_post_meta( $post_id, '_cef_photo_url',           wp_get_attachment_url( $photo_id ) );
        update_post_meta( $post_id, '_cef_video_type',          $video_type );
        update_post_meta( $post_id, '_cef_video_url',           $final_video_url );
        if ( $video_attachment_id ) {
            update_post_meta( $post_id, '_cef_video_attachment_id', $video_attachment_id );
        }
        update_post_meta( $post_id, '_cef_consent_combined',    $consent_comibned );
        update_post_meta( $post_id, '_cef_submitted_ip',        sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ) );
        update_post_meta( $post_id, '_cef_submitted_at',        current_time( 'mysql' ) );

        // Set featured image
        set_post_thumbnail( $post_id, $photo_id );

        wp_send_json_success( array(
            'message' => 'Thank you! Your entry has been submitted and is pending review.',
        ) );
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