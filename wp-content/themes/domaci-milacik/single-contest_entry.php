<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    if ( have_posts() ) {
        ?>
            <main role="main">
                <section class="container">
                    <article class="grid lg:grid-cols-2">
                        <?php 
                            while ( have_posts() ) {
                                the_post();

                                $voting = '[contest_entry_voting]';
                                $permalink = get_the_permalink();
                                ?>
                                    <div>
                                        <?php 
                                            the_post_thumbnail( 'full', array( 'class' => 'w-full' ) ); 
                                        ?>
                                    </div>
                                    <div>
                                        <h1 class="text-3xl">
                                            <?php 
                                                the_title();
                                            ?>
                                        </h1>
                                        <div>
                                            <?php 
                                                echo do_shortcode( $voting );
                                            ?>
                                        </div>
                                        <div>
                                            <?php 
                                                the_content(); 
                                            ?>
                                        </div>
                                        <ul>
                                            <li>
                                                <a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . $permalink ); ?>">Facebook</a>
                                            </li>
                                        </ul>
                                    </div>
                                <?php
                            } 
                        ?>
                    <article>
                </section>
            </main>
        <?php
    } 

    get_footer();