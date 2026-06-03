<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

	$attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';
	$subtitle = $attributes['subtitle'] ?? '';
	$primaryButtonText = $attributes['primaryButtonText'] ?? '';
	$primaryButtonUrl = $attributes['primaryButtonUrl'] ?? '';
	$secondaryButtonText = $attributes['secondaryButtonText'] ?? '';
	$secondaryButtonUrl = $attributes['secondaryButtonUrl'] ?? '';
	$mobileBackgroundImageId = $attributes['mobileBackgroundImageId'] ?? null;
	$desktopBackgroundImageId = $attributes['desktopBackgroundImageId'] ?? null;

?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'px-6 pt-[56.25%] pb-12 relative text-white bg-neutral-500 first:mt-0! md:px-12 md:py-24' ) ); ?>>
	<?php 
		if ( $mobileBackgroundImageId ) {
			?>
				<picture class="w-full h-full absolute inset-0">
					<?php 
						if ( $desktopBackgroundImageId ) {
							$desktopSrcset = wp_get_attachment_image_srcset( $desktopBackgroundImageId, 'full' );

							if ( $desktopSrcset ) {
								?>
									<source 
										media="(min-width: 768px)"
										srcset="<?php echo esc_attr( $desktopSrcset ); ?>" />
								<?php
							}
						}

						echo wp_get_attachment_image( $mobileBackgroundImageId, 'full', false, array( 'class' => 'w-full h-full absolute inset-0 object-cover object-top md:object-center' ) );
					?>
				</picture>
			<?php
		}
	?>
	<div class="relative">
		<div class="max-w-6xl mx-auto">
			<div class="grid gap-6 md:grid-cols-2">
				<div class="space-y-6">
					<div class="space-y-4">
						<h1 class="font-heading text-5xl font-bold uppercase tracking-tight md:text-6xl lg:text-7xl"><?php echo $title ? wp_kses_post( $title ) : 'Nadpis'; ?></h1>
						<?php 
							if ( $subtitle ) {
								?>
									<p><?php echo wp_kses_post( $subtitle ); ?></p>
								<?php
							}
						?>
					</div>
					<?php 
						if ( ( $primaryButtonText && $primaryButtonUrl ) || ( $secondaryButtonText && $secondaryButtonUrl ) ) {
							?>
								<div class="flex flex-wrap items-center gap-4">
									<?php 
										if ( $primaryButtonText && $primaryButtonUrl ) {
											?>	
												<a class="button button-md button-primary outline-white" href="<?php echo esc_url( $primaryButtonUrl ); ?>">
													<?php echo esc_html( $primaryButtonText ); ?>
												</a>
											<?php
										}
										if ( $secondaryButtonText && $secondaryButtonUrl ) {
											?>
												<a class="button button-md button-white button-outline outline-white" href="<?php echo esc_url( $secondaryButtonUrl ); ?>">
													<?php echo esc_html( $secondaryButtonText ); ?>
												</a>
											<?php
										}
									?>
								</div>
							<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>
</section>