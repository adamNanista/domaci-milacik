<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    if ( have_posts() ) {
        ?>
            <main class="main" role="main">
                <?php
                    while ( have_posts() ) {
                        the_post();

                        ?>
                            <section class="content space-y-12 md:space-y-16">
                                <div class="container space-y-4 md:space-y-6">
                                    <h1 class="text-3xl md:text-5xl">
                                        <?php
                                            the_title();
                                        ?>
                                    </h1>
                                    <?php 
                                        $subtitle = get_post_meta( get_the_ID(), '_subtitle', true );

                                        if ( ! empty( $subtitle ) ) {
                                            ?>
                                                <p class="text-base md:text-xl">
                                                    <?php echo esc_html( $subtitle ); ?>
                                                </p>
                                            <?php
                                        }
                                    ?>
                                </div>
                                <div class="container">
                                    <?php 
                                        the_content();
                                    ?>
                                </div>
                            </section>
                        <?php  
                    } 
                ?>
            </main>
        <?php
    } 

    get_footer();