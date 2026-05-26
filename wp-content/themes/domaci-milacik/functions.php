<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    /**
     * Theme setup
     */

    add_action('after_setup_theme', function() {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('menus');
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ]);
    });

    /**
     * Includes
     */

    require_once get_stylesheet_directory() . '/vendor/autoload.php';

    require_once get_stylesheet_directory() . '/inc/contest-entry-form.php';
    require_once get_stylesheet_directory() . '/inc/contest-entry-voting.php';
    require_once get_stylesheet_directory() . '/inc/contest-entry-import.php';
    require_once get_stylesheet_directory() . '/inc/latest-articles.php';

    /**
     * Load CSS and JS
     */

    add_action( 'wp_enqueue_scripts', function() {
        wp_enqueue_style(
            'pt-sans',
            'https://fonts.googleapis.com/css2?family=PT+Sans:wght@400;700&display=swap',
        );

        wp_enqueue_style(
            'reset',
            get_stylesheet_directory_uri() . '/assets/css/reset.css',
            array(),
            filemtime(get_stylesheet_directory() . '/assets/css/reset.css'),
        );

        wp_enqueue_style(
            'theme',
            get_stylesheet_directory_uri() . '/assets/css/theme.css',
            array(),
            filemtime(get_stylesheet_directory() . '/assets/css/theme.css'),
        );

        wp_enqueue_style(
            'utilities',
            get_stylesheet_directory_uri() . '/assets/css/utilities.css',
            array(),
            filemtime(get_stylesheet_directory() . '/assets/css/utilities.css'),
        );

        wp_enqueue_style(
            'components',
            get_stylesheet_directory_uri() . '/assets/css/components.css',
            array(),
            filemtime(get_stylesheet_directory() . '/assets/css/components.css'),
        );

        wp_enqueue_script(
            'header-menu',
            get_stylesheet_directory_uri() . '/assets/js/header-menu.js',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/js/header-menu.js' ),
            true,
        );
    } );

    /**
     * Save/Load ACF json
     */

    add_filter( 'acf/settings/save_json', function() {
        return get_stylesheet_directory() . '/acf-json';
    } );

    add_filter( 'acf/settings/load_json', function($paths) {
        $paths[] = get_stylesheet_directory() . '/acf-json';
        return $paths;
    } );

    /**
     * Add subtitle to pages
     */

    add_action( 'edit_form_after_title', function($post) {
        if ($post->post_type != 'page') {
            return;
        }

        $value = get_post_meta( $post->ID, '_subtitle', true );

        wp_nonce_field( 'save_subtitle', 'subtitle_nonce' );

        echo 
        '
            <input 
                type="text"
                name="subtitle"
                value="' . esc_attr( $value ) . '"
                placeholder="Zadajte podnadpis"
                style="
                    width:100%;
                    margin-top:5px;
                "
            />
        ';
    } );

    add_action( 'save_post', function( $post_id ) {
        if ( ! isset( $_POST['subtitle_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['subtitle_nonce'], 'save_subtitle' ) ) {
            return;
        }

        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return;
        }

        if ( isset( $_POST['subtitle'] ) ) {
            update_post_meta(
                $post_id,
                '_subtitle',
                sanitize_text_field( $_POST['subtitle'] )
            );
        }
    } );