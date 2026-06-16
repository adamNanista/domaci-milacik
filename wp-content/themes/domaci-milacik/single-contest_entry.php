<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    while ( have_posts() ) {
        the_post();

        ?>
            <main <?php post_class( 'flex-1 px-6 *:max-w-6xl *:mx-auto *:mb-12 *:first:mt-12 [&>.alignfull]:max-w-none [&>.alignfull]:-mx-6 md:px-12 md:*:mb-24 md:*:first:mt-24 md:[&>.alignfull]:-mx-12' ); ?> role="main">
                <section class="mt-0! px-4 py-3 bg-neutral-100 md:px-6 alignfull">
                    <div class="max-w-6xl mx-auto">
                        <?php
                            $back_button_url = '';

                            if ( get_option( 'sms_contest_enabled' ) ) {
                                $back_button_url = home_url( '/finale/' );
                            } else {
                                $back_button_url = home_url( '/milacikovia/' );
                            }
                        ?>
                        <a href="<?php echo esc_url( $back_button_url ); ?>" class="inline-flex align-top items-center justify-center gap-2.5 text-neutral-500 font-bold text-center uppercase outline-black hover:underline">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="flex-none size-4" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                            Späť na galériu
                        </a>    
                    </div>
                </section>
                <section class="grid gap-12 md:grid-cols-2 md:items-start">
                    <div class="min-w-0 relative">
                        <?php 
                            $images = get_field( 'gallery' );

                            if ( $images ) {
                                ?>
                                    <div class="group/slider rounded-xl swiper" id="detail-slider">
                                        <div class="swiper-wrapper">
                                            <?php 
                                                foreach ( $images as $image ) {
                                                    ?>
                                                        <div class="h-auto! swiper-slide">
                                                            <a href="<?php echo wp_get_attachment_image_url( $image, 'full' ); ?>" class="block overflow-hidden rounded-xl focus:-outline-offset-2 hover:[&_img]:scale-105" data-fancybox="detail-lightbox">
                                                                <?php echo wp_get_attachment_image( $image, 'contest-entry-detail', false, array( 'class' => 'block w-full aspect-square object-cover object-center transition-transform duration-300' ) ); ?>
                                                            </a>
                                                        </div>
                                                    <?php
                                                }
                                            ?>
                                        </div>
                                        <button class="cursor-pointer inline-flex align-top items-center justify-center w-12 h-12 -mt-6 absolute top-1/2 left-4 z-10 opacity-0 text-white bg-black/50 border-2 border-transparent rounded-md outline-white backdrop-blur-sm transition hover:opacity-100 hover:bg-black/60 group-hover/slider:opacity-100" id="detail-button-prev" type="button">
                                            <span class="sr-only">Predošlý</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="size-6" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                                        </button>
                                        <button class="cursor-pointer inline-flex align-top items-center justify-center w-12 h-12 -mt-6 absolute top-1/2 right-4 z-10 opacity-0 text-white bg-black/50 border-2 border-transparent rounded-md outline-white backdrop-blur-sm transition hover:opacity-100 hover:bg-black/60 group-hover/slider:opacity-100" id="detail-button-next" type="button">
                                            <span class="sr-only">Ďalší</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="size-6" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                                        </button>
                                    </div>
                                <?php
                            } else {
                                ?>
                                    <a href="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>" class="block overflow-hidden rounded-xl focus:-outline-offset-2 hover:[&_img]:scale-105" data-fancybox="detail-lightbox">
                                        <?php the_post_thumbnail( 'contest-entry-detail', array( 'class' => 'block w-full aspect-square object-cover object-center transition-transform duration-300' ) ); ?>
                                    </a>
                                <?php
                            }

                            $video_url = get_field( 'video_url' );

                            if ( $video_url ) {
                                ?>
                                    <a href="<?php echo esc_url( $video_url ); ?>" class="inline-flex align-top items-center justify-center gap-2 h-10 px-4 absolute right-4 bottom-4 z-10 text-white text-sm font-bold uppercase bg-black/50 border-2 border-transparent rounded-sm outline-white backdrop-blur-sm transition-colors hover:bg-black/60" data-fancybox="detail-lightbox">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="size-3.5" aria-hidden="true"><path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"/></svg>
                                        Prehrať video
                                    </a>
                                <?php
                            }
                        ?>
                    </div>
                    <div class="self-center space-y-8 md:space-y-12">
                        <div class="space-y-6">
                            <div class="space-y-4">
                                <h1 class="font-heading text-5xl font-bold uppercase tracking-tight wrap-break-word md:text-6xl"><?php the_title(); ?></h1>
                                <?php 
                                    if ( get_option( 'sms_contest_enabled' ) ) {
                                        get_template_part( 'templates/contest_entry/sms-vote-count', 'xl' ); 
                                    } else {
                                        get_template_part( 'templates/contest_entry/vote-count', 'xl' ); 
                                    }
                                ?>
                            </div>
                            <?php 
                                if ( ! get_option( 'sms_contest_enabled' ) ) {  
                                    get_template_part( 'templates/contest_entry/vote-button' );
                                } 
                             ?>
                        </div>
                        <?php 
                            if ( get_option( 'sms_contest_enabled' ) ) {
                                get_template_part( 'templates/contest_entry/sms-vote-instructions' );
                            } 
                        ?>
                        <div class="text-neutral-500 wrap-break-word">
                            <?php the_content(); ?>
                        </div>
                        <div class="detail-content-footer">
                            <div class="space-y-4">
                                <p class="text-primary-600 text-2xl font-bold">Zdieľaj a získaj viac hlasov</p>
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" class="button button-sm button-facebook" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M240 363.3L240 576L356 576L356 363.3L442.5 363.3L460.5 265.5L356 265.5L356 230.9C356 179.2 376.3 159.4 428.7 159.4C445 159.4 458.1 159.8 465.7 160.6L465.7 71.9C451.4 68 416.4 64 396.2 64C289.3 64 240 114.5 240 223.4L240 265.5L174 265.5L174 363.3L240 363.3z"/></svg>
                                        Facebook
                                    </a>
                                    <a href="https://www.facebook.com/dialog/send?link=<?php the_permalink(); ?>&app_id=<?php echo FACEBOOK_APP_ID;  ?>&redirect_uri=<?php the_permalink(); ?>" class="button button-sm button-messenger" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M320.6 72C180.6 72 72 174.3 72 312.6C72 384.9 101.7 447.4 150.1 490.5C158.4 498 156.7 502.4 158.1 548.7C158.2 551.9 159.1 555.1 160.7 557.9C162.3 560.7 164.6 563.1 167.4 564.8C170.2 566.5 173.3 567.6 176.5 567.8C179.7 568 183 567.5 186 566.2C238.9 543 239.6 541.2 248.6 543.6C401.8 585.8 568 487.7 568 312.6C568 174.3 460.6 72 320.6 72zM469.8 257.1L396.8 372.7C394 377 390.4 380.8 386.2 383.7C382 386.6 377.1 388.5 372.1 389.5C367.1 390.5 361.8 390.3 356.8 389.1C351.8 387.9 347.1 385.7 343 382.7L284.9 339.2C282.3 337.3 279.1 336.2 275.9 336.2C272.7 336.2 269.5 337.3 266.9 339.2L188.5 398.6C178 406.5 164.3 394 171.4 382.9L244.4 267.3C247.2 263 250.8 259.2 255 256.3C259.2 253.4 264.1 251.5 269.1 250.5C274.1 249.5 279.4 249.7 284.4 250.9C289.4 252.1 294.1 254.3 298.3 257.3L356.4 300.8C359 302.7 362.2 303.8 365.4 303.8C368.6 303.8 371.8 302.7 374.4 300.8L452.8 241.4C463.2 233.4 476.9 245.9 469.9 257z"/></svg>
                                        Messenger
                                    </a>
                                    <button onclick="copyToClipboard(window.location.href)" class="button button-sm" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M451.5 160C434.9 160 418.8 164.5 404.7 172.7C388.9 156.7 370.5 143.3 350.2 133.2C378.4 109.2 414.3 96 451.5 96C537.9 96 608 166 608 252.5C608 294 591.5 333.8 562.2 363.1L491.1 434.2C461.8 463.5 422 480 380.5 480C294.1 480 224 410 224 323.5C224 322 224 320.5 224.1 319C224.6 301.3 239.3 287.4 257 287.9C274.7 288.4 288.6 303.1 288.1 320.8C288.1 321.7 288.1 322.6 288.1 323.4C288.1 374.5 329.5 415.9 380.6 415.9C405.1 415.9 428.6 406.2 446 388.8L517.1 317.7C534.4 300.4 544.2 276.8 544.2 252.3C544.2 201.2 502.8 159.8 451.7 159.8zM307.2 237.3C305.3 236.5 303.4 235.4 301.7 234.2C289.1 227.7 274.7 224 259.6 224C235.1 224 211.6 233.7 194.2 251.1L123.1 322.2C105.8 339.5 96 363.1 96 387.6C96 438.7 137.4 480.1 188.5 480.1C205 480.1 221.1 475.7 235.2 467.5C251 483.5 269.4 496.9 289.8 507C261.6 530.9 225.8 544.2 188.5 544.2C102.1 544.2 32 474.2 32 387.7C32 346.2 48.5 306.4 77.8 277.1L148.9 206C178.2 176.7 218 160.2 259.5 160.2C346.1 160.2 416 230.8 416 317.1C416 318.4 416 319.7 416 321C415.6 338.7 400.9 352.6 383.2 352.2C365.5 351.8 351.6 337.1 352 319.4C352 318.6 352 317.9 352 317.1C352 283.4 334 253.8 307.2 237.5z"/></svg>
                                        Kopírovať odkaz
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php 
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
                <section class="space-y-8 md:space-y-12">
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <h2 class="font-heading text-4xl font-bold uppercase tracking-tight">Mohlo by vás zaujímať</h2>
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
                        <a href="<?php echo esc_url( 'https://www.cas.sk/' ); ?>" class="inline-flex align-top items-center justify-center gap-2.5 text-primary-600 font-bold text-center uppercase outline-black hover:underline" target="_blank">
                            Viac článkov
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="flex-none size-4" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    </div>
                </section>
            </main>
        <?php
    }
    
    get_footer();