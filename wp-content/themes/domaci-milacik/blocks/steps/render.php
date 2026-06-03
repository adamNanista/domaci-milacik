<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
    
    $attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';

?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => '[counter-reset:step] space-y-8 md:space-y-12' ) ); ?>>
    <div class="text-center">
        <h2 class="font-heading text-4xl font-bold uppercase tracking-tight"><?php echo $title ? wp_kses_post( $title ) : 'Kroky'; ?></h2>
    </div>
    <div class="grid gap-6 md:grid-cols-2 md:gap-4 lg:grid-cols-4">
        <?php echo $content; ?>
    </div>
</section>

