<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    add_action( 'wp_enqueue_scripts', 'contest_entry_form_enqueue_assets' );

    function contest_entry_form_enqueue_assets() {
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

    add_shortcode( 'contest_entry_form', 'contest_entry_form' );

    function contest_entry_form() {
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
                        <div>
                            <label for="contest-entry-form-video-upload" class="screen-reader-text">Nahrať video</label>
                            <input type="file" id="contest-entry-form-video-upload" name="contest-entry-form-video-url" accept="video/mp4" />
                        </div>
                        <div>
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
                        <input type="checkbox" name="contest-entry-form-consent-combined" required /> Súhlasím s <a href="#">pravidlami súťaže</a> a so spracovaním osobných údajov. <abbr title="Povinné">*</abbr>
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