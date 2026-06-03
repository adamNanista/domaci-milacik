<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

?>

<main class="flex-1 px-6 *:max-w-6xl *:mx-auto *:mb-12 *:first:mt-12 [&>.alignfull]:max-w-none [&>.alignfull]:-mx-6 md:px-12 md:*:mb-24 md:*:first:mt-24 md:[&>.alignfull]:-mx-12" role="main">
    <section>
        <h1 class="font-heading text-5xl font-bold uppercase tracking-tight md:text-6xl"><?php the_archive_title(); ?></h1>
    </section>
    <section class="grid gap-4 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <?php 
            while ( have_posts() ) {
                the_post();

                ?>
                    <article class="flex flex-col h-full overflow-hidden border-2 border-neutral-200 rounded-xl">
                        <a href="<?php the_permalink(); ?>" class="flex-none block overflow-hidden focus:-outline-offset-2 hover:[&_img]:scale-105">
                            <?php the_post_thumbnail( 'contest-entry-card', array( 'class' => 'w-full aspect-4/3 object-cover object-center transition-transform duration-300' ) ); ?>
                        </a>
                        <div class="flex-1 flex flex-col p-6 space-y-4">
                            <div class="flex-1 space-y-2">
                                <h3 class="text-2xl font-bold">
                                    <a href="<?php the_permalink(); ?>" class="hover:underline"><?php the_title(); ?></a>
                                </h3>
                            </div>
                        </div>
                    </article>
                <?php
            }
        ?>
    </section>
    <?php
        the_posts_pagination();
    ?>
</main>

<?php 

    get_footer();