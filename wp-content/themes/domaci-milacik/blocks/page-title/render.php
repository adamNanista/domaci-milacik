<?php

	$attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';
	$subtitle = $attributes['subtitle'] ?? '';

?>

<section class="page-title">
    <div class="container">
        <div class="space-y-4 md:space-y-6">
            <h1 class="font-heading text-6xl font-bold text-uppercase md:text-7xl"><?php echo $title ? wp_kses_post( $title ) : 'Nadpis'; ?></h1>
            <p class="text-lg"><?php echo $subtitle ? wp_kses_post( $subtitle ) : 'Podnadpis'; ?></p>
        </div>
    </div>
</section>