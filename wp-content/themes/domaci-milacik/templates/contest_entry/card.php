<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

?>

<article class="flex flex-col h-full border-2 border-neutral-200 rounded-xl">
    <a href="<?php the_permalink(); ?>" class="flex-none block overflow-hidden rounded-t-[0.625rem] focus:-outline-offset-2 hover:[&_img]:scale-105">
        <?php the_post_thumbnail( 'contest-entry-card', array( 'class' => 'w-full aspect-4/3 object-cover object-center transition-transform duration-300' ) ); ?>
    </a>
    <div class="flex-1 flex flex-col p-6 space-y-4">
        <div class="flex-1 space-y-2">
            <h3 class="text-2xl font-bold">
                <a href="<?php the_permalink(); ?>" class="hover:underline"><?php the_title(); ?></a>
            </h3>
            <?php get_template_part( 'templates/contest_entry/vote-count', 'md' ); ?>
        </div>
        <div class="flex-none">
            <a href="<?php the_permalink(); ?>" class="button button-md button-wide button-primary w-full">Hlasovať</a>
        </div>
    </div>
</article>