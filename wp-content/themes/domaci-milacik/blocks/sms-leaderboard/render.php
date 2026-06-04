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
        'posts_per_page'    => -1,
        'meta_query'        => array(
            array(
                'key'       => 'sms_code',
                'compare'   => 'EXISTS',
            ),
            array(
                'key'       => 'sms_code',
                'value'     => '',
                'compare'   => '!=',
            ),
        ),
        'meta_key'          => 'sms_votes',
        'order_by'          => 'meta_value_num',
        'order'             => 'DESC',
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {

        ?>
            <section <?php echo get_block_wrapper_attributes( array( 'class' => 'space-y-8 md:space-y-12' ) ); ?>>
                <?php
                    if ( $title ) {
                        ?>
                            <div>
                                <h2 class="font-heading text-4xl font-bold uppercase tracking-tight"><?php echo wp_kses_post( $title ); ?></h2>
                            </div>
                        <?php
                    }
                ?>
                <div class="grid gap-4 xs:grid-cols-2 md:grid-cols-1 lg:grid-cols-2">
                    <?php 
                        $index = 0;

                        while ( $query->have_posts() ) {
                            $query->the_post();

                            $index++;

                            get_template_part( 'templates/contest_entry/sms-card', null, array( 'index' => $index ) );
                        }
                    ?>
                </div>
                <?php 
                    if ( $buttonText && $buttonUrl ) {
                        ?>
                            <div class="text-center">
                                <a href="<?php echo esc_url( $buttonUrl ); ?>" class="inline-flex align-top items-center justify-center gap-2.5 text-primary-600 font-bold text-center uppercase outline-black hover:underline">
                                    <?php echo esc_html( $buttonText ); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="flex-none size-4" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </div>
                        <?php
                    }
                ?>
            </section>
        <?php
    }

    wp_reset_postdata();