<?php
    $imageId = $attributes['imageId'] ?? '';
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
            <h3 class="font-heading text-base font-bold text-uppercase md:text-lg">
                <?php 
                    echo wp_kses_post( $position );
                ?>
            </h3>
            <p class="font-heading text-red-600 text-3xl font-bold text-uppercase md:text-5xl">
                <?php 
                    echo wp_kses_post( $prize );
                ?>
            </p>
        </div>
    </div>
</article>