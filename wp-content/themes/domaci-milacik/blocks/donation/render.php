<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $imageId = $attributes['imageId'] ?? null;
    $title = $attributes['title'] ?? '';
    $subtitle = $attributes['subtitle'] ?? '';

?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'flex flex-col items-center gap-8 px-8 py-6 bg-success-100 rounded-xl md:flex-row-reverse md:justify-between md:items-start md:gap-6' ) ); ?>>
    <?php 
        if ( $imageId  ) {
            ?>
                <div class="w-24 md:flex-none">
                    <?php 
                        echo wp_get_attachment_image( $imageId, 'full', false, array( 'class' => 'w-full' ) );
                    ?>
                </div>
            <?php
        }
    ?>
    <div class="space-y-4 text-center md:self-center md:text-left">
        <h3 class="font-heading text-4xl font-bold uppercase tracking-tight">
            <?php 
                echo wp_kses_post( $title );
            ?>
        </h3>
        <p class="text-neutral-500">
            <?php 
                echo wp_kses_post( $subtitle );
            ?>
        </p>
    </div>
</section>