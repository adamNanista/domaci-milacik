<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

?>

<article class="card">
    <a href="<?php the_permalink(); ?>" class="card-thumbnail">
        <?php the_post_thumbnail( 'contest-entry-card' ); ?>
    </a>
    <div class="card-content">
        <div class="card-content-header">
            <h3 class="text-2xl font-bold">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            <?php get_template_part( 'templates/contest_entry/vote-count' ); ?>
        </div>
        <div class="card-content-footer">
            <a href="<?php the_permalink(); ?>" class="button button-md button-wide button-primary">Hlasovať</a>
        </div>
    </div>
</article>