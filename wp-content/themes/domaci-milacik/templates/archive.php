<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    the_archive_title();

    while ( have_posts() ) {
        the_post();

        ?>
            <main <?php post_class( 'main' ); ?> role="main">
                <div class="content">
                    <section class="container">
                        <h1 class="text-3xl md:text-5xl">
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