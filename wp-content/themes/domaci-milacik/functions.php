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
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ]);

        register_nav_menus([
            'primary' => __('Primary Menu', 'domaci-milacik'),
        ]);
    });

    require_once get_stylesheet_directory() . '/inc/contest-entry-form.php';

    /**
     * Load CSS and JS
     */

    add_action( 'wp_enqueue_scripts', function() {
        wp_enqueue_style(
            'utilities',
            get_stylesheet_directory_uri() . '/assets/css/utilities.css',
            array(),
            filemtime(get_stylesheet_directory() . '/assets/css/utilities.css'),
        );
    } );

    /**
     * Save/Load ACF json
     */

    add_filter('acf/settings/save_json', function() {
        return get_stylesheet_directory() . '/acf-json';
    });

    add_filter('acf/settings/load_json', function($paths) {
        $paths[] = get_stylesheet_directory() . '/acf-json';
        return $paths;
    });