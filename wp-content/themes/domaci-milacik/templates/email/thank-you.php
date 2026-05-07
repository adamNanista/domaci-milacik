<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    ?>
    <img src="<?php echo esc_url( $args[ 'photo_url' ] ); ?>" width="150" height="150" />
    <h1><?php echo esc_html( $args[ 'pet_name' ] ); ?></h1>