<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    /**
     * Theme setup
     */

    add_action( 'after_setup_theme', function() {
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'menus' );
        add_theme_support( 'html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ] );
        add_theme_support( 'align-wide' );
    } );

    /**
     * Includes
     */

    require_once get_stylesheet_directory() . '/vendor/autoload.php';

    require_once get_stylesheet_directory() . '/inc/contest-entry-form.php';
    require_once get_stylesheet_directory() . '/inc/contest-entry-voting.php';
    require_once get_stylesheet_directory() . '/inc/contest-entry-import.php';
    require_once get_stylesheet_directory() . '/inc/latest-articles.php';

    /**
     * Register block assets
     */

    add_action('init', function () {
        wp_register_script(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js',
            array(),
            '12.2.0',
            true
        );

        wp_register_style(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css',
            array(),
            '12.2.0'
        );

        wp_register_style(
            'hero',
            get_stylesheet_directory_uri() . '/blocks/hero/hero.css',
            array(),
            filemtime( get_stylesheet_directory() . '/blocks/hero/hero.css' )
        );

        wp_register_script(
            'leaderboard',
            get_stylesheet_directory_uri() . '/blocks/leaderboard/leaderboard.js',
            array(),
            filemtime( get_stylesheet_directory() . '/blocks/leaderboard/leaderboard.js' ),
            true
        );

        wp_register_style(
            'leaderboard',
            get_stylesheet_directory_uri() . '/blocks/leaderboard/leaderboard.css',
            array(),
            filemtime( get_stylesheet_directory() . '/blocks/leaderboard/leaderboard.css' )
        );

        wp_register_style(
            'prizes',
            get_stylesheet_directory_uri() . '/blocks/prizes/prizes.css',
            array(),
            filemtime( get_stylesheet_directory() . '/blocks/prizes/prizes.css' )
        );

        wp_register_style(
            'page-title',
            get_stylesheet_directory_uri() . '/blocks/page-title/page-title.css',
            array(),
            filemtime( get_stylesheet_directory() . '/blocks/page-title/page-title.css' )
        );
    });

    /**
     * Register blocks
     */

    add_action('init', function () {
        register_block_type(
            get_stylesheet_directory() . '/blocks/hero'
        );

        register_block_type(
            get_stylesheet_directory() . '/blocks/leaderboard'
        );

        register_block_type(
            get_stylesheet_directory() . '/blocks/prizes'
        );

        register_block_type(
            get_stylesheet_directory() . '/blocks/prize'
        );

        register_block_type(
            get_stylesheet_directory() . '/blocks/page-title'
        );
    });

    /**
     * Load CSS and JS
     */

    add_action( 'wp_enqueue_scripts', function() {
        wp_enqueue_style(
            'pt-sans',
            'https://fonts.googleapis.com/css2?family=PT+Sans:wght@400;700&display=swap',
        );

        wp_enqueue_style(
            'din-2014-narrow',
            'https://use.typekit.net/yoq8bhl.css',
        );

        wp_enqueue_script(
            'viewport-width',
            get_stylesheet_directory_uri() . '/assets/js/viewport-width.js',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/js/viewport-width.js' ),
            true,
        );

        wp_enqueue_script(
            'header-menu',
            get_stylesheet_directory_uri() . '/assets/js/header-menu.js',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/js/header-menu.js' ),
            true,
        );

        wp_enqueue_style(
            'reset',
            get_stylesheet_directory_uri() . '/assets/css/reset.css',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/css/reset.css'),
        );

        wp_enqueue_style(
            'theme',
            get_stylesheet_directory_uri() . '/assets/css/theme.css',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/css/theme.css'),
        );

        wp_enqueue_style(
            'utilities',
            get_stylesheet_directory_uri() . '/assets/css/utilities.css',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/css/utilities.css'),
        );

        wp_enqueue_style(
            'components',
            get_stylesheet_directory_uri() . '/assets/css/components.css',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/css/components.css'),
        );
    } );

    /**
     * Save/Load ACF json
     */

    add_filter( 'acf/settings/save_json', function() {
        return get_stylesheet_directory() . '/acf-json';
    } );

    add_filter( 'acf/settings/load_json', function( $paths ) {
        $paths[] = get_stylesheet_directory() . '/acf-json';
        return $paths;
    } );

    /**
     * Update votes on post save
     */

    add_action( 'save_post_contest_entry', function( $post_id ) {
        if ( get_post_meta( $post_id, 'votes', true ) === '' ) {
            update_post_meta( $post_id, 'votes', 0 );
        }
    } );

    /**
     * Allow svg
     */

    add_filter( 'upload_mimes', function( $mimes ) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    } );