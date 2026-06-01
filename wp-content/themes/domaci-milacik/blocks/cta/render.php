<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

	$attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';
	$subtitle = $attributes['subtitle'] ?? '';
	$primaryButtonText = $attributes['primaryButtonText'] ?? '';
	$primaryButtonUrl = $attributes['primaryButtonUrl'] ?? '';
	$secondaryButtonText = $attributes['secondaryButtonText'] ?? '';
	$secondaryButtonUrl = $attributes['secondaryButtonUrl'] ?? '';

?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'cta' ) ); ?>>
    <div class="space-y-6">
        <div class="space-y-4 text-center">
            <h2 class="font-heading text-5xl font-bold text-uppercase md:text-7xl"><?php echo $title ? wp_kses_post( $title ) : 'Nadpis'; ?></h2>
            <?php 
                if ( $subtitle ) {
                    ?>
                        <p class="text-base"><?php echo wp_kses_post( $subtitle ); ?></p>
                    <?php
                }
            ?>
        </div>
        <?php 
            if ( ( $primaryButtonText && $primaryButtonUrl ) || ( $secondaryButtonText && $secondaryButtonUrl ) ) {
                ?>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <?php 
                            if ( $primaryButtonText && $primaryButtonUrl ) {
                                ?>	
                                    <a class="button button-md button-white" href="<?php echo esc_url( $primaryButtonUrl ); ?>">
                                        <?php echo esc_html( $primaryButtonText ); ?>
                                    </a>
                                <?php
                            }
                            if ( $secondaryButtonText && $secondaryButtonUrl ) {
                                ?>
                                    <a class="button button-md button-white button-outline" href="<?php echo esc_url( $secondaryButtonUrl ); ?>">
                                        <?php echo esc_html( $secondaryButtonText ); ?>
                                    </a>
                                <?php
                            }
                        ?>
                    </div>
                <?php
            }
        ?>
    </div>
</section>