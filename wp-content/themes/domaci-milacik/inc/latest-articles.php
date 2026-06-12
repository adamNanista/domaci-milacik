<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    add_action( 'rest_api_init', function() {
        register_rest_route( 'latest-articles/v1', '/articles', array(
            'methods'               => 'GET',
            'callback'              => 'latest_articles_proxy',
            'permission_callback'   => '__return_true',
        ) );
    } );

    function latest_articles_proxy( WP_REST_Request $request ) {
        $url    = defined('NOVYCAS_API_URL') ? NOVYCAS_API_URL : '';
        $token  = defined('NOVYCAS_API_JWT') ? NOVYCAS_API_JWT : '';

        if ( ! $url || ! $token ) {
            return new WP_Error( 'misconfigured', 'API URL alebo JWT nie je definované vo wp-config.php', array( 'status' => 500 ) );
        }

        $response = wp_remote_get( $url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ),
            'timeout' => 30,
        ) );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'fetch_failed', $response->get_error_message(), array( 'status' => 502 ) );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        return new WP_REST_Response( $body, $code );
    }

    function render_latest_articles( $data ) {
        $articles = $data['results'] ?? [];

        if ( empty( $articles ) ) {
            ?>
                <p class="text-neutral-500">Nenašli sa žiadne články.</p>
            <?php
            return;
        }
        ?>
            <div id="latest-articles-slider" class="swiper -m-[4px]! p-[4px]!">
                <div class="swiper-wrapper">
                    <?php 
                        foreach ( $articles as $article ) {
                            $title      = $article['title'] ?? '';
                            $url        = $article['mainRoute']['path'] ?? $article['externalUrl'] ?? '#';
                            $domain     = $article['site']['domain'] ?? 'https://www.cas.sk';
                            $full_url   = $url !== '#' ? rtrim( $domain, '/' ) . $url : '#';
                            $image      = get_image_url( $article['heroImage'] ?? [], 640, 360 );
                            ?>
                                <div class="swiper-slide">
                                    <article class="space-y-4">
                                        <a href="<?php echo esc_url( $full_url ); ?>" class="block overflow-hidden rounded-xl focus:-outline-offset-2 hover:[&_img]:scale-105">
                                            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="w-full transition-transform duration-300" width="640" height="360" />
                                        </a>
                                        <h3 class="font-bold">
                                            <a href="<?php echo esc_url( $full_url ); ?>" class="hover:underline" target="_blank">
                                                <?php echo esc_html( $title ); ?>
                                            </a>
                                        </h3>
                                    </article>
                                </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
        <?php
    }

    function get_image_url( $heroImage, $width = 640, $height = 360 ) {
        $cdn      = defined('NOVYCAS_CDN_URL') ? rtrim(NOVYCAS_CDN_URL, '/') : '';
        $basename = $heroImage['baseName'] ?? '';

        if ( ! $cdn || ! $basename ) return '';

        return $cdn . '/api/image/' . $width . 'x' . $height . '/' . $basename;
    };