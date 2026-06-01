<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $imageId = $attributes['imageId'] ?? null;
    $title = $attributes['title'] ?? '';
    $subtitle = $attributes['subtitle'] ?? '';

?>

<article <?php echo get_block_wrapper_attributes( array( 'class' => 'step' ) ); ?>>
    <?php 
        if ( $imageId  ) {
            ?>
                <div class="step-icon">
                    <?php 
                        echo wp_get_attachment_image( $imageId, 'full' );
                    ?>
                </div>
            <?php
        }
    ?>
    <div class="step-content">
        <div class="space-y-3 text-center">
            <h3 class="font-heading text-2xl font-bold text-uppercase">
                <?php 
                    echo wp_kses_post( $title );
                ?>
            </h3>
            <p class="text-base text-neutral-500">
                <?php 
                    echo wp_kses_post( $subtitle );
                ?>
            </p>
        </div>
    </div>
</article>