<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

?>

<main class="main" role="main">
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