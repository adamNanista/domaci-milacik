<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

	$attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';
	$subtitle = $attributes['subtitle'] ?? '';

?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'space-y-4' ) ); ?>>
    <h1 class="font-heading text-5xl font-bold uppercase tracking-tight md:text-6xl"><?php echo $title ? wp_kses_post( $title ) : 'Nadpis'; ?></h1>
    <?php 
        if ( $subtitle ) {
            ?>
                <p><?php echo wp_kses_post( $subtitle ); ?></p>
            <?php
        }
    ?>
</section>