<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    if ( is_singular() ) {
        get_template_part( 'templates/content/single' );
    } elseif ( is_archive() ) {
        get_template_part( 'templates/content/archive' );
    } else {
        get_template_part( 'templates/content/404' );
    }

    get_footer();