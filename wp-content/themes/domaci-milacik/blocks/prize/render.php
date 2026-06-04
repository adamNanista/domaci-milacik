<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $iconId = $attributes['iconId'] ?? null;
    $title = $attributes['title'] ?? '';
    $subtitle = $attributes['subtitle'] ?? '';
    $imageId = $attributes['imageId'] ?? null;

?>

<article <?php echo get_block_wrapper_attributes( array( 'class' => 'flex flex-col border-2 border-neutral-200 rounded-xl' ) ); ?>>
    <?php 
        if ( $imageId  ) {
            ?>
                <a href="<?php echo wp_get_attachment_image_url( $imageId, 'full'); ?>" class="block overflow-hidden rounded-t-[0.625rem] focus:-outline-offset-2 hover:[&_img]:scale-105" data-fancybox="prize-gallery" data-caption="<?php echo esc_attr( $title ); ?>">
                    <?php
                        echo wp_get_attachment_image( $imageId, 'full', false, array( 'class' => 'w-full transition-transform duration-300' ) );
                    ?>
                </a>
            <?php
        }
    ?>
    <div class="p-6 space-y-2">
        <div class="flex items-center gap-4">
            <?php 
                if ( $iconId  ) {
                    ?>
                        <div class="flex-none w-12">
                            <?php 
                                echo wp_get_attachment_image( $iconId, 'full', false, array( 'class' => 'w-full' ) );
                            ?>
                        </div>
                    <?php
                }
            ?>
            <div class="space-y-1">
                <h3 class="font-heading text-3xl font-bold uppercase tracking-tight">
                    <?php 
                        echo wp_kses_post( $title );
                    ?>
                </h3>
                
            </div>
        </div>
        <p class="text-neutral-500">
            <?php 
                echo wp_kses_post( $subtitle );
            ?>
        </p>
    </div>
</article>