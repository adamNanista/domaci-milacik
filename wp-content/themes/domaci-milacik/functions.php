<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    add_filter('acf/settings/save_json', function() {
        return get_stylesheet_directory() . '/acf-json'; // Save
    });

    add_filter('acf/settings/load_json', function($paths) {
        $paths[] = get_stylesheet_directory() . '/acf-json';
        return $paths;
    });