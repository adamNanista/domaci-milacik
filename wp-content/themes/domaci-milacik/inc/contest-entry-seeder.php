<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    use WP_CLI;
    use WP_CLI_Command;

    if ( ! class_exists( 'Contest_Seeder_Command' ) ) {
        class Contest_Seeder_Command extends WP_CLI_Command {

            /**
             * Generate contest entries.
             *
             * ## OPTIONS
             *
             * [--count=<count>]
             * : Number of entries to generate.
             *
             * ## EXAMPLES
             *
             * wp contest seed --count=100
             */
            public function seed( $args, $assoc_args ) {

                $count = isset( $assoc_args['count'] )
                    ? (int) $assoc_args['count']
                    : 20;

                $titles = [
                    'Golden Retriever',
                    'Labrador',
                    'German Shepherd',
                    'Border Collie',
                    'Beagle',
                    'Persian Cat',
                    'Maine Coon',
                    'British Shorthair',
                    'Ragdoll',
                    'Sphynx',
                ];
                $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
                $video_url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
                $image_ids = [ 204, 205, 206, 207 ];

                for ( $i = 1; $i <= $count; $i++ ) {

                    $title = $titles[ array_rand( $titles ) ] . ' #' . $i;

                    $post_id = wp_insert_post(
                        [
                            'post_type'     => 'contest_entry',
                            'post_status'   => 'publish',
                            'post_title'    => $title,
                            'post_content'  => $content,
                        ]
                    );

                    if ( is_wp_error( $post_id ) ) {
                        continue;
                    }

                    $image_id = $image_ids[ array_rand( $image_ids ) ];

                    set_post_thumbnail( $post_id, $image_id );

                    update_post_meta(
                        $post_id,
                        'owner_name',
                        'Test User ' . $i
                    );

                    update_post_meta(
                        $post_id,
                        'owner_email',
                        'test' . $i . '@test.sk'
                    );

                    update_post_meta(
                        $post_id,
                        'votes',
                        rand( 0, 1000 )
                    );

                    update_post_meta(
                        $post_id,
                        'video_url',
                        $video_url
                    );

                    WP_CLI::log( "Created entry {$post_id}" );
                }

                WP_CLI::success( "{$count} contest entries created." );
            }
        }
    }

    WP_CLI::add_command(
        'contest',
        'Contest_Seeder_Command'
    );