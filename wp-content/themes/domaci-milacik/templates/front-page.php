<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    while ( have_posts() ) :
        the_post();

        ?>
            <main <?php post_class( 'main' ); ?> role="main">
                <?php 
                    the_content();
                ?>
            </main>
        <?php

    endwhile;