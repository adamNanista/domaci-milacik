<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    while ( have_posts() ) {
        the_post();

        ?>
            <main <?php post_class( 'flex-1 px-6 [&>*]:max-w-[var(--wp--style--global--content-size)] [&>*]:mx-auto [&>*]:mb-12 [&>:first-child]:mt-12 [&>.alignfull]:max-w-none [&>.alignfull]:-mx-6 md:px-12 md:[&>*]:mb-24 md:[&>:first-child]:mt-24 md:[&>.alignfull]:-mx-12' ); ?> role="main">
                <?php 
                    the_content();
                ?>
            </main>
        <?php
    }

    get_footer();