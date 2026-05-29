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
        'posts_per_page'    => 10,
        'meta_key'          => 'votes',
        'orderby'           => 'meta_value_num',
        'order'             => 'DESC',
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {

        ?>
            <section class="leaderboard">
                <div class="container">
                    <div class="flex items-start gap-4 md:gap-1.5">
                        <div class="flex-1">
                            <h2 class="font-heading text-4xl font-bold text-uppercase"><?php echo $title ? wp_kses_post( $title ) : 'Rebríček miláčikov'; ?></h2>
                        </div>
                        <div class="flex-none flex items-center gap-3 md:gap-4">
                            <button class="leaderboard-button-prev" type="button">
                                <span class="visually-hidden">Predošlý</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left-icon lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
                            </button>
                            <button class="leaderboard-button-next" type="button">
                                <span class="visually-hidden">Ďalší</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="leaderboard-slider-wrapper">
                        <div class="leaderboard-slider swiper" id="leaderboard-slider">
                            <div class="swiper-wrapper">
                                <?php 
                                    while ( $query->have_posts() ) {
                                        $query->the_post();

                                        $votes = (int) get_field('votes');

                                        ?>
                                            <div class="swiper-slide">
                                                <article class="card">
                                                    <a href="<?php the_permalink(); ?>" class="card-thumbnail">
                                                        <?php the_post_thumbnail( 'full' ); ?>
                                                    </a>
                                                    <div class="card-content">
                                                        <div class="space-y-4">
                                                            <div class="space-y-2">
                                                                <h3 class="text-2xl font-bold">
                                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                                </h3>
                                                                <p class="votes votes-lg">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart-icon lucide-heart"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/></svg>
                                                                    <?php echo esc_html( $votes ); ?>
                                                                </p>
                                                            </div>
                                                            <a href="<?php the_permalink(); ?>" class="button button-sm button-wide button-primary">Hlasovať</a>
                                                        </div>
                                                    </div>
                                                </article>
                                            </div>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="text-center">
                        <a href="<?php echo $buttonUrl ? esc_url( $buttonUrl ) : home_url( '/milacikovia' ); ?>" class="link link-sm link-primary">
                            <?php echo $buttonText ? esc_html( $buttonText ) : 'Zobraziť celú galériu'; ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    </div>
                </div>
            </section>
        <?php
    }

    wp_reset_postdata();