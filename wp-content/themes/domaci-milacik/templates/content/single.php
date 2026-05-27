<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    while ( have_posts() ) :
        the_post();

        ?>
            <main class="main" role="main">
                <section class="content space-y-12 md:space-y-16">
                    <div class="container space-y-4 md:space-y-6">
                        <h1 class="text-3xl md:text-5xl">
                            <?php
                                the_title();
                            ?>
                        </h1>
                    </div>
                    <div class="container">
                        <?php 
                            the_content();
                        ?>
                    </div>
                </section>
            </main>
        <?php
        
    endwhile;