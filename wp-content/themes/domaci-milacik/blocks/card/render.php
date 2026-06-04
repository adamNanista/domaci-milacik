<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $iconId = $attributes['iconId'] ?? null;
    $title = $attributes['title'] ?? '';
    $subtitle = $attributes['subtitle'] ?? '';
    $imageId = $attributes['imageId'] ?? null;
    $highlight = $attributes['highlight'] ?? '';
    $description = $attributes['description'] ?? '';
    $variant = $attributes['variant'] ?? 'primary';

    $variantClasses = array(
        'wrapper'       => array(
            'primary'   => 'bg-primary-100',
            'accent'    => 'bg-accent-100',
            'success'   => 'bg-success-100',
        ),
        'highlight'       => array(
            'primary'   => 'text-primary-700',
            'accent'    => 'text-accent-700',
            'success'   => 'text-success-700',
        ),
    );

?>

<article <?php echo get_block_wrapper_attributes( array( 'class' => "space-y-6 p-6 {$variantClasses['wrapper'][$variant]} rounded-xl" ) ); ?>>
    <div class="flex items-start gap-6">
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
        <div class="flex-1 self-center space-y-1">
            <h2 class="font-heading text-2xl font-bold uppercase tracking-tight">
                <?php
                    echo wp_kses_post( $title );
                ?>
            </h2>
            <p class="text-neutral-500">
                <?php
                    echo wp_kses_post( $subtitle );
                ?>
            </p>
        </div>
    </div>
    <div class="flex flex-col items-center gap-6 md:flex-row">
        <?php
            if ( $imageId  ) {
                ?>
                    <div class="md:flex-none md:w-1/2">
                        <?php
                            echo wp_get_attachment_image( $imageId, 'full', false, array( 'class' => 'w-full' ) );
                        ?>
                    </div>
                <?php
            }
        ?>
        <div class="md:flex-1 md:space-y-2">
            <h2 class="<?php echo $variantClasses['highlight'][$variant]; ?> font-heading text-3xl font-bold uppercase tracking-tight">
                <?php
                    echo wp_kses_post( $highlight );
                ?>
            </h2>
            <p class="text-neutral-500">
                <?php
                    echo wp_kses_post( $description );
                ?>
            </p>
        </div>
    </div>
</article>