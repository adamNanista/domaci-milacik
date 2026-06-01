<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    while ( have_posts() ) {
        the_post();

        ?>
            <main <?php post_class( 'main' ); ?> role="main">
                <section class="detail">
                    <div class="grid gap-12 md:grid-cols-2">
                        <div class="">
                            <?php
                                the_post_thumbnail( 'contest-entry-detail' );
                            ?>
                        </div>
                        <div>
                            <h1 class="font-heading text-5xl font-bold text-uppercase md:text-6xl"><?php the_title(); ?></h1>
                            <?php
                                get_template_part( 'templates/contest_entry/vote-count' );
                                get_template_part( 'templates/contest_entry/vote-button' );

                                the_content();
                            ?>
                        </div>
                    </div>
                </div>
            </main>
        <?php
    }

    get_footer();