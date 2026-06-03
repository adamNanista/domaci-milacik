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
    <div class="flex items-center gap-4 px-8 py-6">
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
            <h3 class="font-heading text-2xl font-bold uppercase tracking-tight">
                <?php 
                    echo wp_kses_post( $title );
                ?>
            </h3>
            
        </div>
    </div>
    <?php 
        if ( $imageId  ) {
            echo wp_get_attachment_image( $imageId, 'full', false, array( 'class' => 'w-full' ) );
        }
    ?>
    <div class="px-8 py-6">
        <p class="text-neutral-500">
            <?php 
                echo wp_kses_post( $subtitle );
            ?>
        </p>
    </div>
</article>