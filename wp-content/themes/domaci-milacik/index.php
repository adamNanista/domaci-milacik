<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    if ( is_singular() ) {
        get_template_part( 'templates/single' );
    } elseif ( is_archive() ) {
        get_template_part( 'templates/archive' );
    } else {
        get_template_part( 'templates/404' );
    }

    get_footer();