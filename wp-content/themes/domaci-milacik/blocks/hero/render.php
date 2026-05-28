<?php

	$attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';
	$subtitle = $attributes['subtitle'] ?? '';
	$primaryButtonText = $attributes['primaryButtonText'] ?? '';
	$primaryButtonUrl = $attributes['primaryButtonUrl'] ?? '';
	$secondaryButtonText = $attributes['secondaryButtonText'] ?? '';
	$secondaryButtonUrl = $attributes['secondaryButtonUrl'] ?? '';

?>

<section class="hero">
	<div class="container">
		<div class="grid gap-6 md:grid-cols-2">
			<div class="space-y-6 md:space-y-8">
				<div class="space-y-4 md:space-y-6">
					<h1 class="font-heading text-5xl font-bold text-uppercase md:text-7xl"><?php echo $title ? wp_kses_post( $title ) : 'Nadpis'; ?></h1>
					<p class="text-base md:text-lg"><?php echo $subtitle ? wp_kses_post( $subtitle ) : 'Podnadpis'; ?></p>
				</div>
				<div class="flex items-center gap-3 md:gap-4">
					<a class="button button-sm button-primary md:button-md" href="<?php echo $primaryButtonUrl ? esc_url( $primaryButtonUrl ) : '#'; ?>">
						<?php echo $primaryButtonText ? esc_html( $primaryButtonText ) : 'Hlavné tlačidlo'; ?>
					</a>
					<a class="button button-sm button-white button-outline md:button-md" href="<?php echo $secondaryButtonUrl ? esc_url( $secondaryButtonUrl ) : '#'; ?>">
						<?php echo $secondaryButtonText ? esc_html( $secondaryButtonText ) : 'Vedľajšie tlačidlo'; ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>