<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

?>

<main class="flex-1 px-6 *:max-w-6xl *:mx-auto *:mb-12 *:first:mt-12 [&>.alignfull]:max-w-none [&>.alignfull]:-mx-6 md:px-12 md:*:mb-24 md:*:first:mt-24 md:[&>.alignfull]:-mx-12" role="main">
    <?php 
        while ( have_posts() ) {
            the_post();

            the_title();
            the_content();
        }
    ?>
</main>

<?php

    get_footer();