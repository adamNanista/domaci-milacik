<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    $votes = (int) get_field( 'votes' );

?>

<article class="card">
    <a href="<?php the_permalink(); ?>" class="card-thumbnail">
        <?php the_post_thumbnail( 'full' ); ?>
    </a>
    <div class="card-content">
        <div class="space-y-4">
            <div class="space-y-2">
                <h3 class="text-2xl font-bold">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                <p class="votes votes-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart-icon lucide-heart"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/></svg>
                    <?php echo esc_html( $votes ); ?>
                </p>
            </div>
            <a href="<?php the_permalink(); ?>" class="button button-md button-wide button-primary">Hlasovať</a>
        </div>
    </div>
</article>