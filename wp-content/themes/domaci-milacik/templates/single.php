<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    while ( have_posts() ) {
        the_post();

        ?>
            <main <?php post_class( 'main' ); ?> role="main">
                <div class="content">
                    <section class="container">
                        <h1 class="font-heading text-5xl font-bold text-uppercase md:text-7xl">
                            <?php
                                the_title();
                            ?>
                        </h1>
                    </section>
                    <section class="container">
                        <?php 
                            the_content();
                        ?>
                    </section>
                </div>
            </main>
        <?php

    }