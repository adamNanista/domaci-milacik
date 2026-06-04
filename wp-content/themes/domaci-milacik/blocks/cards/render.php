<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'grid gap-4 md:grid-cols-2' ) ); ?>>
    <?php echo $content; ?>
</section>

