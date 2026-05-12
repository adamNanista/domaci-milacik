<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    add_action( 'wp_enqueue_scripts', 'enqueue_latest_articles_assets' );

    function enqueue_latest_articles_assets() {
        if ( is_page( 60 ) ) {
            wp_enqueue_script(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js',
                array(),
                false,
                true,
            );

            wp_enqueue_style(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css',
            );

            wp_enqueue_script(
                'latest-articles',
                get_stylesheet_directory_uri() . '/assets/js/latest-articles.js',
                array( 'swiper' ),
                filemtime( get_stylesheet_directory() . '/assets/js/latest-articles.js' ),
                true,
            );
        }
    }

    add_shortcode( 'latest_articles', 'render_latest_articles' );

    function render_latest_articles() {
        include_once( ABSPATH . WPINC . '/feed.php' );

        add_filter( 'wp_feed_cache_transient_lifetime' , 'return_3600' );

        $feed = fetch_feed( 'https://www.cas.sk/temporary-rss.xml' );

        remove_filter( 'wp_feed_cache_transient_lifetime' , 'return_3600' );

        if ( is_wp_error( $feed ) ) {
            return;
        }

        $max_items = $feed->get_item_quantity( 6 );
        $feed_items = $feed->get_items( 0, $max_items );

        ob_start();

        ?>
            <div id="latest-articles-slider" class="swiper">
                <div class="swiper-wrapper">
                    <?php 
                        foreach ( $feed_items as $item ) {
                            $link = $item->get_link();
                            $title = $item->get_title();

                            $image = '';
                            $alt = '';

                            $enclosure = $item->get_enclosure();
                            if ( $enclosure && str_contains( $enclosure->get_type(), 'image' ) ) {
                                $image = $enclosure->get_link();
                                $alt = $enclosure->data['description'] ?? '';
                            }
                            
                            ?>
                                <div class="swiper-slide">
                                    <a href="<?php echo esc_url( $item->get_link() ); ?>" target="_blank">
                                        <?php 
                                            if ( $image ) {
                                                ?>
                                                    <img 
                                                        src="<?php echo esc_url( $image ); ?>" 
                                                        alt="<?php echo esc_attr( $alt ?: $title ); ?>" />
                                                <?php
                                            }
                                        ?>
                                        <?php echo esc_html( $item->get_title() ); ?>
                                    </a>
                                </div>
                            <?php
                        }
                    ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        <?php
        

        return ob_get_clean();
    }

    function return_3600( $seconds ) {
        return 3600;
    }