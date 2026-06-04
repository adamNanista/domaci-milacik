<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    while ( have_posts() ) {
        the_post();

        ?>
            <main <?php post_class( 'flex-1 px-6 *:max-w-6xl *:mx-auto *:mb-12 *:first:mt-12 [&>.alignfull]:max-w-none [&>.alignfull]:-mx-6 md:px-12 md:*:mb-24 md:*:first:mt-24 md:[&>.alignfull]:-mx-12' ); ?> role="main">
                <section>
                    <h1 class="font-heading text-5xl font-bold uppercase tracking-tight md:text-6xl"><?php the_title(); ?></h1>
                </section>
                <section class="space-y-4">
                    <?php 
                        the_content();
                    ?>
                </section>
            </main>
        <?php
    }

    get_footer();