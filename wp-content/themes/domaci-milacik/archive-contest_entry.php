<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    if ( have_posts() ) {
        ?>
            <main class="main" role="main">
                <section class="container">
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 md:gap-6 lg:grid-cols-4">
                        <?php 
                            while ( have_posts() ) {
                                the_post();

                                $votes = (int) get_field( 'votes' );
                                ?>
                                    <article class="card">
                                        <a href="<?php the_permalink(); ?>" class="card-thumbnail">
                                            <?php the_post_thumbnail( 'medium' ); ?>
                                        </a>
                                        <div class="card-content">
                                            <h2 class="text-xl md:text-2xl">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h2>
                                            <p class="votes votes-sm md:votes-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="votes-icon"><path d="M305 151.1L320 171.8L335 151.1C360 116.5 400.2 96 442.9 96C516.4 96 576 155.6 576 229.1L576 231.7C576 343.9 436.1 474.2 363.1 529.9C350.7 539.3 335.5 544 320 544C304.5 544 289.2 539.4 276.9 529.9C203.9 474.2 64 343.9 64 231.7L64 229.1C64 155.6 123.6 96 197.1 96C239.8 96 280 116.5 305 151.1z"/></svg>
                                                <span id="contest-vote-count">
                                                    <?php echo esc_html( $votes ); ?>
                                                </span>
                                            </p>
                                            <a href="<?php the_permalink(); ?>" class="button button-sm button-wide button-primary md:button-md">Zobraziť</a>
                                        </div>
                                    </article>
                                <?php
                            } 
                        ?>
                    </div>
                </section>
            </main>
        <?php
        
    } 

    get_footer();