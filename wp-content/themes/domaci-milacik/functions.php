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
     * Change default posts per page count
     */

    add_action( 'pre_get_posts', function( $query ) {
        if ( ! is_admin() && $query->is_main_query() ) {
            $query->set( 'posts_per_page', 8 );
        }
    } );

    /**
     * Custom image sizes
     */

    add_image_size( 'contest-entry-card', 272, 204, true );
    add_image_size( 'contest-entry-detail', 552, 552, true );

    /**
     * Includes
     */

    require_once get_stylesheet_directory() . '/vendor/autoload.php';

    require_once get_stylesheet_directory() . '/inc/contest-entry-form.php';
    require_once get_stylesheet_directory() . '/inc/contest-entry-voting.php';
    require_once get_stylesheet_directory() . '/inc/contest-entry-import.php';
    require_once get_stylesheet_directory() . '/inc/latest-articles.php';

    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        require_once get_stylesheet_directory() . '/inc/contest-entry-seeder.php';
    }

    /**
     * Register block assets
     */

    add_action( 'init', function () {

        /**
         * Swiper
         */

        wp_register_script(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js',
            array(),
            null,
            true
        );

        wp_register_style(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css',
        );

        /**
         * Just Validate
         */

        wp_register_script(
            'just-validate',
            'https://cdn.jsdelivr.net/npm/just-validate@4.3.0/dist/just-validate.production.min.js',
            array(),
            false,
            true,
        );

        /**
         * CloudFlare Turnstile
         */

        wp_register_script(
            'cf-turnstile',
            'https://challenges.cloudflare.com/turnstile/v0/api.js',
            array(),
            null,
            true,
        );

        wp_register_script(
            'cf-turnstile-explicit',
            'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit',
            array(),
            null,
            true,
        );

        /**
         * Fancybox
         */

        wp_register_script(
            'fancybox',
            'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js',
            array(),
            null,
            true,
        );

        wp_register_style(
            'fancybox',
            'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css',
        );

        /**
         * Fonts
         */

        wp_register_style(
            'pt-sans',
            'https://fonts.googleapis.com/css2?family=PT+Sans:wght@400;700&display=swap',
        );

        wp_register_style(
            'din-2014-narrow',
            'https://use.typekit.net/yoq8bhl.css',
        );

        /**
         * Viewport width
         */

        wp_register_script(
            'viewport-width',
            get_stylesheet_directory_uri() . '/assets/js/viewport-width.js',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/js/viewport-width.js' ),
            true,
        );

        /**
         * Copy to clipboard
         */

        wp_register_script(
            'copy-to-clipboard',
            get_stylesheet_directory_uri() . '/assets/js/copy-to-clipboard.js',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/js/copy-to-clipboard.js' ),
            true,
        );

        /**
         * Header menu
         */

        wp_register_script(
            'header-menu',
            get_stylesheet_directory_uri() . '/assets/js/header-menu.js',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/js/header-menu.js' ),
            true,
        );

        /**
         * Global style
         */

        wp_register_style(
            'style',
            get_stylesheet_directory_uri() . '/assets/css/style.css',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/css/style.css'),
        );

        /**
         * Leaderboard
         */

        wp_register_script(
            'leaderboard',
            get_stylesheet_directory_uri() . '/blocks/leaderboard/leaderboard.js',
            array(),
            filemtime( get_stylesheet_directory() . '/blocks/leaderboard/leaderboard.js' ),
            true
        );

        /**
         * Prizes
         */

        wp_register_script(
            'prizes',
            get_stylesheet_directory_uri() . '/blocks/prizes/prizes.js',
            array(),
            filemtime( get_stylesheet_directory() . '/blocks/prizes/prizes.js' ),
            true
        );

        /**
         * Entry form
         */

        wp_register_script(
            'entry-form',
            get_stylesheet_directory_uri() . '/blocks/entry-form/entry-form.js',
            array(),
            filemtime( get_stylesheet_directory() . '/blocks/entry-form/entry-form.js' ),
            true
        );

        wp_localize_script( 'entry-form', 'contest_entry_form_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'contest_entry_form_submit_entry' ),
        ) );

        /**
         * Entry voting
         */

        wp_register_script(
            'contest-entry-voting',
            get_stylesheet_directory_uri() . '/assets/js/contest-entry-voting.js',
            array( 'cf-turnstile' ),
            filemtime( get_stylesheet_directory() . '/assets/js/contest-entry-voting.js' ),
            true,
        );

        wp_localize_script( 'contest-entry-voting', 'contest_entry_voting_ajax', array(
            'ajax_url'              => admin_url( 'admin-ajax.php' ),
            'nonce'                 => wp_create_nonce( 'contest_entry_vote' ),
            'turnstile_site_key'    => CLOUDFLARE_TURNSTILE_SITE_KEY,
        ) );

        /**
         * Detail gallery
         */

        wp_register_script(
            'detail-gallery',
            get_stylesheet_directory_uri() . '/assets/js/detail-gallery.js',
            array( 'fancybox' ),
            filemtime( get_stylesheet_directory() . '/assets/js/detail-gallery.js' ),
            true,
        );

    } );

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
            get_stylesheet_directory() . '/blocks/steps'
        );

        register_block_type(
            get_stylesheet_directory() . '/blocks/step'
        );

        register_block_type(
            get_stylesheet_directory() . '/blocks/donation'
        );

        register_block_type(
            get_stylesheet_directory() . '/blocks/cta'
        );

        register_block_type(
            get_stylesheet_directory() . '/blocks/page-title'
        );

        register_block_type(
            get_stylesheet_directory() . '/blocks/entry-form'
        );

        register_block_type(
            get_stylesheet_directory() . '/blocks/sms-leaderboard'
        );
    });

    /**
     * Load assets
     */

    add_action( 'wp_enqueue_scripts', function() {

        /**
         * Fonts
         */

        wp_enqueue_style( 'pt-sans' );
        wp_enqueue_style( 'din-2014-narrow' );

        /**
         * Viewport width
         */

        wp_enqueue_script( 'viewport-width' );

        /**
         * Header menu
         */

        wp_enqueue_script( 'header-menu' );

        /**
         * Global style
         */

        wp_enqueue_style( 'style' );

        if ( is_singular( 'contest_entry' ) ) {

            /**
             * CloudFlare Turnstile
             */

            wp_enqueue_script( 'cf-turnstile-explicit' );

            /**
             * Fancybox
             */

            wp_enqueue_script( 'fancybox' );
            wp_enqueue_style( 'fancybox' );

            /**
             * Copy to clipboard
             */

            wp_enqueue_script( 'copy-to-clipboard' );

            /**
             * Detail gallery
             */

            wp_enqueue_script( 'detail-gallery' );

            /**
             * Entry voting
             */

            wp_enqueue_script( 'contest-entry-voting' );
            
        }

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

        if ( get_post_meta( $post_id, 'sms_votes', true ) === '' ) {
            update_post_meta( $post_id, 'sms_votes', 0 );
        }
    } );

    /**
     * Allow svg
     */

    add_filter( 'upload_mimes', function( $mimes ) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    } );

    /**
     * Change contest_entry Yoast SEO og image
     */

    add_filter( 'wpseo_opengraph_image', function( $image ) {
        if ( ! is_singular( 'contest_entry' ) ) {
            return $image;
        }

        $custom = get_post_meta(
            get_the_ID(),
            '_contest_og_image',
            true
        );

        return $custom ?: $image;
    } );

    /**
     * Add SMS toggle and number to general settings
     */

    add_action('admin_init', function() {
        register_setting('general', 'sms_contest_enabled');

        add_settings_field(
            'sms_contest_enabled',
            'SMS fáza',
            function() {
                ?>
                <input type="checkbox"
                    name="sms_contest_enabled"
                    value="1"
                    <?php checked(get_option('sms_contest_enabled'), 1); ?>>
                <?php
            },
            'general'
        );

        register_setting('general', 'sms_contest_number');

        add_settings_field(
            'sms_contest_number',
            'SMS číslo',
            function() {
                ?>
                <input type="text"
                    name="sms_contest_number"
                    value="<?php echo esc_attr(get_option('sms_contest_number')); ?>"
                    class="regular-text">
                <?php
            },
            'general'
        );

        register_setting('general', 'sms_contest_price');

        add_settings_field(
            'sms_contest_price',
            'Cena SMS',
            function() {
                ?>
                <input type="text"
                    name="sms_contest_price"
                    value="<?php echo esc_attr(get_option('sms_contest_price')); ?>"
                    class="regular-text">
                <?php
            },
            'general'
        );
    });