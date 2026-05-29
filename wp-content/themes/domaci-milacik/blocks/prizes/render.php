<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';

?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'prizes' ) ); ?>>
    <h2 class="font-heading text-4xl font-bold text-uppercase"><?php echo $title ? wp_kses_post( $title ) : 'Výhry'; ?></h2>
    <div class="grid gap-4 md:grid-cols-3">
        <?php echo $content; ?>
    </div>
</section>

