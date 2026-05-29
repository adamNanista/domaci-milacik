<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

?>

<main class="main" role="main">
    <section class="archive-title">
        <div class="space-y-4">
            <h1 class="font-heading text-5xl font-bold text-uppercase md:text-6xl">Všetci súťažiaci</h1>
        </div>
    </section>
    <section class="archive-grid">
        <div class="grid gap-4 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <?php
                if ( have_posts() ) {
                    while ( have_posts() ) {
                        the_post();

                        get_template_part( 'templates/contest_entry/card' );
                    }
                } else {
                    ?>
                        <p class="text-base">Zatiaľ žiadny miláčikovia</p>
                    <?php
                }
            ?>
        </div>
    </section>
</main>

<?php 

    get_footer();