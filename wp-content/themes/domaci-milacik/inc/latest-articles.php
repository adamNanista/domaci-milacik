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
            
            wp_enqueue_style(
                'latest-articles',
                get_stylesheet_directory_uri() . '/assets/css/latest-articles.css',
                array(),
                filemtime( get_stylesheet_directory() . '/assets/css/latest-articles.css' ),
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

        $rss = fetch_feed( 'https://www.cas.sk/temporary-rss.xml' );

        if ( is_wp_error( $rss ) ) {
            return;
        }

        $max_items = $rss->get_item_quantity( 6 );
        $rss_items = $rss->get_items( 0, $max_items );

        ob_start();

        ?>
            <div id="latest-articles-slider" class="swiper">
                <div class="swiper-wrapper">
                    <?php 
                        foreach ( $rss_items as $item ) {
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