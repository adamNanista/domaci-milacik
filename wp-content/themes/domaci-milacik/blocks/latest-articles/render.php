<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    $attributes = $attributes ?? [];

	$title = $attributes['title'] ?? '';
    $buttonText = $attributes['buttonText'] ?? '';
    $buttonUrl = $attributes['buttonUrl'] ?? '';

    $cache_key  = 'latest_articles_data';
    $data       = get_transient( $cache_key );

    if ( false === $data ) {
        $request    = new WP_REST_Request( 'GET', '/latest-articles/v1/articles' );
        $response   = rest_do_request( $request );

        if ( is_wp_error( $response ) ) {
            return;
        }

        $data = $response->get_data();
        set_transient( $cache_key, $data, HOUR_IN_SECONDS );
    }

    ?>

    <section <?php echo get_block_wrapper_attributes( array( 'class' => 'space-y-8 md:space-y-12' ) ); ?>>
        <div class="flex items-start gap-4">
            <div class="flex-1">
                <h2 class="font-heading text-4xl font-bold uppercase tracking-tight">
                    <?php echo $title ? wp_kses_post( $title ) : 'Mohlo by vás zaujímať'; ?>
                </h2>
            </div>
            <div class="flex-none flex items-center gap-2">
                <button class="cursor-pointer inline-flex align-top items-center justify-center w-8 h-8 text-neutral-500 rounded-sm outline-black transition-colors hover:bg-neutral-100 disabled:cursor-default disabled:pointer-events-none disabled:opacity-50" id="latest-articles-button-prev" type="button">
                    <span class="sr-only">Predošlý</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button class="cursor-pointer inline-flex align-top items-center justify-center w-8 h-8 text-neutral-500 rounded-sm outline-black transition-colors hover:bg-neutral-100 disabled:cursor-default disabled:pointer-events-none disabled:opacity-50" id="latest-articles-button-next" type="button">
                    <span class="sr-only">Ďalší</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </div>
        <div>
            <?php render_latest_articles( $data ); ?>
        </div>
        <div class="text-center">
            <a href="<?php echo $buttonUrl ? esc_url( $buttonUrl ) : esc_url( 'https://www.cas.sk/' ); ?>" class="inline-flex align-top items-center justify-center gap-2.5 text-primary-600 font-bold text-center uppercase outline-black hover:underline" target="_blank">
                <?php echo $buttonText ? esc_html( $buttonText ) : 'Viac článkov'; ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="flex-none size-4" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>
    </section>

    

    