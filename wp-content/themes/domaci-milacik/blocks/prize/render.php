<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $imageId = $attributes['imageId'] ?? null;
    $position = $attributes['position'] ?? '';
    $prize = $attributes['prize'] ?? '';

?>

<article class="prize">
    <?php 
        if ( $imageId  ) {
            ?>
                <div class="prize-icon">
                    <?php 
                        echo wp_get_attachment_image( $imageId, 'full' );
                    ?>
                </div>
            <?php
        }
    ?>
    <div class="prize-content">
        <div class="space-y-1">
            <h3 class="font-heading text-lg font-bold text-uppercase">
                <?php 
                    echo wp_kses_post( $position );
                ?>
            </h3>
            <p class="font-heading text-red-600 text-5xl font-bold text-uppercase">
                <?php 
                    echo wp_kses_post( $prize );
                ?>
            </p>
        </div>
    </div>
</article>