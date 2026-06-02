<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    $attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';
    $buttonText = $attributes['buttonText'] ?? '';
    $buttonUrl = $attributes['buttonUrl'] ?? '';

    $args = array(
        'post_type'         => 'contest_entry',
        'post_status'       => 'publish',
        'posts_per_page'    => 8,
        'meta_key'          => 'votes',
        'orderby'           => 'meta_value_num',
        'order'             => 'DESC',
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {

        ?>
            <section <?php echo get_block_wrapper_attributes( array( 'class' => 'leaderboard' ) ); ?>>
                <div class="flex items-start gap-4 md:gap-6">
                    <div class="flex-1">
                        <h2 class="font-heading text-4xl font-bold text-uppercase"><?php echo $title ? wp_kses_post( $title ) : 'Rebríček miláčikov'; ?></h2>
                    </div>
                    <div class="flex-none flex items-center gap-2">
                        <button class="leaderboard-button-prev" type="button">
                            <span class="visually-hidden">Predošlý</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left-icon lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                        <button class="leaderboard-button-next" type="button">
                            <span class="visually-hidden">Ďalší</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="leaderboard-slider-wrapper">
                    <div class="leaderboard-slider swiper" id="leaderboard-slider">
                        <div class="swiper-wrapper">
                            <?php 
                                while ( $query->have_posts() ) {
                                    $query->the_post();

                                    ?>
                                        <div class="swiper-slide">
                                            <?php
                                                get_template_part( 'templates/contest_entry/card' );
                                            ?>
                                        </div>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <a href="<?php echo $buttonUrl ? esc_url( $buttonUrl ) : home_url( '/milacikovia' ); ?>" class="link link-md link-primary">
                        <?php echo $buttonText ? esc_html( $buttonText ) : 'Zobraziť celú galériu'; ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                </div>
            </section>
        <?php
    }

    wp_reset_postdata();