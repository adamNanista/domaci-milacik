<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';

?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'space-y-8 md:space-y-12' ) ); ?>>
    <div>
        <h2 class="font-heading text-4xl font-bold uppercase tracking-tight"><?php echo $title ? wp_kses_post( $title ) : 'Výhry'; ?></h2>
    </div>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <?php echo $content; ?>
    </div>
</section>

