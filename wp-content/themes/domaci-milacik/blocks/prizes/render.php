<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';

?>

<section class="prizes">
    <div class="container">
        <h2 class="font-heading text-4xl font-bold text-uppercase"><?php echo $title ? wp_kses_post( $title ) : 'Výhry'; ?></h2>
    </div>
    <div class="container">
        <div class="grid gap-3 md:grid-cols-3 md:gap-4">
            <?php echo $content; ?>
        </div>
    </div>
</section>

