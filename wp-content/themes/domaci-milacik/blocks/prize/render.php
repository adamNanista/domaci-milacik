<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $imageId = $attributes['imageId'] ?? null;
    $title = $attributes['title'] ?? '';
    $subtitle = $attributes['subtitle'] ?? '';

?>

<article <?php echo get_block_wrapper_attributes( array( 'class' => 'flex items-center gap-8 px-8 py-6 bg-neutral-100 rounded-xl' ) ); ?>>
    <?php 
        if ( $imageId  ) {
            ?>
                <div class="flex-none w-24">
                    <?php 
                        echo wp_get_attachment_image( $imageId, 'full', false, array( 'class' => 'w-full' ) );
                    ?>
                </div>
            <?php
        }
    ?>
    <div class="space-y-1">
        <h3 class="font-heading text-2xl font-bold uppercase tracking-tight">
            <?php 
                echo wp_kses_post( $title );
            ?>
        </h3>
        <p class="font-heading text-red-600 text-5xl font-bold uppercase tracking-tight">
            <?php 
                echo wp_kses_post( $subtitle );
            ?>
        </p>
    </div>
</article>