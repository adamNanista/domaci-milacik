<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    if ( have_posts() ) {
        ?>
            <main role="main">
                <section class="container">
                    <?php 
                        while ( have_posts() ) {
                            the_post();

                            the_title( '<h1>', '</h1>' );
                            the_content();
                        } 
                    ?>
                </section>
            </main>
        <?php
        
    } 

    get_footer();