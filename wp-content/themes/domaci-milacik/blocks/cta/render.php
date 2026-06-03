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

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'flex flex-col items-center gap-6 px-8 py-12 text-white bg-primary-600 bg-[url(../img/paw-print.svg)] bg-center bg-no-repeat bg-size-[48rem] rounded-xl md:bg-size-[72rem]' ) ); ?>>
    <div class="space-y-6">
        <div class="space-y-4 text-center">
            <h2 class="font-heading text-5xl font-bold uppercase tracking-tight md:text-6xl lg:text-7xl"><?php echo $title ? wp_kses_post( $title ) : 'Nadpis'; ?></h2>
            <?php 
                if ( $subtitle ) {
                    ?>
                        <p><?php echo wp_kses_post( $subtitle ); ?></p>
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
                                    <a class="button button-md button-white outline-white" href="<?php echo esc_url( $primaryButtonUrl ); ?>">
                                        <?php echo esc_html( $primaryButtonText ); ?>
                                    </a>
                                <?php
                            }
                            if ( $secondaryButtonText && $secondaryButtonUrl ) {
                                ?>
                                    <a class="button button-md button-white button-outline outline-white" href="<?php echo esc_url( $secondaryButtonUrl ); ?>">
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