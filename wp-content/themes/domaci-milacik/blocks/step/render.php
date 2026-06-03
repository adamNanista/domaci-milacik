<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $imageId = $attributes['imageId'] ?? null;
    $title = $attributes['title'] ?? '';
    $subtitle = $attributes['subtitle'] ?? '';

?>

<article <?php echo get_block_wrapper_attributes( array( 'class' => '[counter-increment:step] flex flex-col items-center gap-8' ) ); ?>>
    <?php 
        if ( $imageId  ) {
            ?>
                <div class="w-24 relative before:content-[counter(step)] before:inline-flex before:align-top before:items-center before:justify-center before:w-8 before:h-8 before:absolute before:-top-1 before:-right-1 before:text-white before:font-bold before:text-center before:bg-primary-600 before:rounded-full">
                    <?php 
                        echo wp_get_attachment_image( $imageId, 'full', false, array( 'class' => 'w-full' ) );
                    ?>
                </div>
            <?php
        }
    ?>
    <div class="space-y-3 text-center">
        <h3 class="font-heading text-2xl font-bold uppercase tracking-tight">
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
</article>