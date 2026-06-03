<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

?>

<main class="flex-1 px-6 *:max-w-6xl *:mx-auto *:mb-12 *:first:mt-12 [&>.alignfull]:max-w-none [&>.alignfull]:-mx-6 md:px-12 md:*:mb-24 md:*:first:mt-24 md:[&>.alignfull]:-mx-12" role="main">
    <section>
        <h1 class="font-heading text-5xl font-bold uppercase tracking-tight md:text-6xl">Všetci súťažiaci</h1>
    </section>

    <?php 
        if ( have_posts() ) {
            ?>
                <section class="grid gap-4 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <?php 
                        while ( have_posts() ) {
                            the_post();

                            get_template_part( 'templates/contest_entry/card' );
                        }
                    ?>
                </section>
            <?php
            the_posts_pagination();
        } else {
            ?>
                <section>
                    <p>Zatiaľ žiadny miláčikovia</p>
                </section>
            <?php
        }
    ?>
</main>

<?php 

    get_footer();