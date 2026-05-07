<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    add_shortcode( 'latest_articles', 'render_latest_articles' );

    function render_latest_articles() {
        include_once( ABSPATH . WPINC . '/feed.php' );

        $rss = fetch_feed( 'https://www.cas.sk/temporary-rss.xml' );

        if ( is_wp_error( $rss ) ) {
            return;
        }

        $max_items = $rss->get_item_quantity( 5 );
        $rss_items = $rss->get_items( 0, $max_items );

        ob_start();

        ?>
            <ul>
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
                            <li>
                                <a href="<?php echo esc_url( $item->get_link() ); ?>">
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
                            </li>
                        <?php
                    }
                ?>
            </ul>
        <?php
        

        return ob_get_clean();
    }