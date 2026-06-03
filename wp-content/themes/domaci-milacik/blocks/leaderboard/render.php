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
            <section <?php echo get_block_wrapper_attributes( array( 'class' => 'space-y-8 md:space-y-12' ) ); ?>>
                <div class="flex items-start gap-4">
                    <div class="flex-1">
                        <h2 class="font-heading text-4xl font-bold uppercase tracking-tight"><?php echo $title ? wp_kses_post( $title ) : 'Rebríček miláčikov'; ?></h2>
                    </div>
                    <div class="flex-none flex items-center gap-2">
                        <button class="cursor-pointer inline-flex align-top items-center justify-center w-8 h-8 text-neutral-500 rounded-sm outline-black transition-colors hover:bg-neutral-100 disabled:cursor-default disabled:pointer-events-none disabled:opacity-50" id="leaderboard-button-prev" type="button">
                            <span class="sr-only">Predošlý</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                        <button class="cursor-pointer inline-flex align-top items-center justify-center w-8 h-8 text-neutral-500 rounded-sm outline-black transition-colors hover:bg-neutral-100 disabled:cursor-default disabled:pointer-events-none disabled:opacity-50" id="leaderboard-button-next" type="button">
                            <span class="sr-only">Ďalší</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="min-[75rem]:relative min-[75rem]:before:block min-[75rem]:before:w-[calc((var(--viewport-width)-100%)/2)] min-[75rem]:before:h-auto min-[75rem]:before:absolute min-[75rem]:before:top-0 min-[75rem]:before:right-full min-[75rem]:before:bottom-0 min-[75rem]:before:z-10 min-[75rem]:before:bg-linear-to-r min-[75rem]:before:from-white min-[75rem]:before:to-white/0 min-[75rem]:after:block min-[75rem]:after:w-[calc((var(--viewport-width)-100%)/2)] min-[75rem]:after:h-auto min-[75rem]:after:absolute min-[75rem]:after:top-0 min-[75rem]:after:left-full min-[75rem]:after:bottom-0 min-[75rem]:after:z-10 min-[75rem]:after:bg-linear-to-r min-[75rem]:after:from-white/0 min-[75rem]:after:to-white">
                    <div class="[--gutter:calc((var(--viewport-width)-100%)/2)] -mx-(--gutter)! px-(--gutter)! swiper" id="leaderboard-slider">
                        <div class="swiper-wrapper">
                            <?php 
                                while ( $query->have_posts() ) {
                                    $query->the_post();

                                    ?>
                                        <div class="w-[calc(var(--viewport-width)-calc(2*var(--gutter))-1.5rem-16px)]! h-auto! min-[30rem]:w-[calc((var(--viewport-width)-calc(2*var(--gutter))-1.5rem-32px)/2)]! md:w-[calc((var(--viewport-width)-calc(2*var(--gutter))-3rem-48px)/3)]! lg:w-[calc((var(--viewport-width)-calc(2*var(--gutter))-3rem-64px)/4)]! min-[75rem]:w-[calc((var(--viewport-width)-calc(2*var(--gutter))-48px)/4)]! min-[75rem]:transition-[opacity,transform]! min-[75rem]:not-[.swiper-slide-fully-visible]:opacity-50 swiper-slide">
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
                    <a href="<?php echo $buttonUrl ? esc_url( $buttonUrl ) : home_url( '/milacikovia' ); ?>" class="inline-flex align-top items-center justify-center gap-2.5 text-primary-600 font-bold text-center uppercase outline-black hover:underline">
                        <?php echo $buttonText ? esc_html( $buttonText ) : 'Zobraziť celú galériu'; ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="flex-none size-4" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                </div>
            </section>
        <?php
    }

    wp_reset_postdata();