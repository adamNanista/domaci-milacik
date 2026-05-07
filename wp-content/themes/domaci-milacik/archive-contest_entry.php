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

                            $votes = (int) get_field( 'votes' );
                            ?>
                                <article>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'full', array( 'class' => 'w-full' ) ); ?>
                                    </a>
                                    <h2 class="text-xl">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>
                                    <p>
                                        <?php echo esc_html( $votes ); ?>
                                    </p>
                                    <a href="<?php the_permalink(); ?>" class="button button-sm">Zobraziť</a>
                                </article>
                            <?php
                        } 
                    ?>
                </section>
            </main>
        <?php
        
    } 

    get_footer();