<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    add_action( 'admin_footer', 'add_import_form_to_contest_entry_list' );

    function add_import_form_to_contest_entry_list() {
        global $typenow;

         if ($typenow !== 'contest_entry') return;

        ?>
            <form id="contest-entry-import-form" method="post" enctype="multipart/form-data" style="display: none;">
                <?php wp_nonce_field( 'contest_entry_import', 'contest_entry_import_nonce' ); ?>
                <input type="hidden" name="contest-entry-import" value="1" />
                <input type="file" name="contest-entry-import-file" id="contest-entry-import-file" accept=".csv" />
            </form>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const addNewBtn = document.querySelector(".page-title-action");

                    if (!addNewBtn) return;

                    const form = document.querySelector("#contest-entry-import-form");
                    const fileInput = document.querySelector("#contest-entry-import-file");

                    // Upload button
                    const uploadBtn = document.createElement("a");
                    uploadBtn.className = "page-title-action";
                    uploadBtn.textContent = "Nahrať SMS hlasy (CSV)";
                    uploadBtn.href = "#";

                    uploadBtn.addEventListener("click", function (event) {
                        event.preventDefault();
                        fileInput.click();
                    });

                    addNewBtn.after(uploadBtn);

                    // File label
                    const fileLabel = document.createElement("span");
                    fileLabel.style.display = "none";
                    fileLabel.style.verticalAlign = "top";
                    fileLabel.style.margin = "16px 4px 0 8px";

                    uploadBtn.after(fileLabel);

                    // Import button
                    const importBtn = document.createElement('a');
                    importBtn.className = "page-title-action";
                    importBtn.textContent = "Importovať SMS hlasy";
                    importBtn.href = "#";
                    importBtn.style.display = "none";

                    importBtn.addEventListener("click", function (event) {
                        event.preventDefault();

                        if (!fileInput.files.length) return;
                        if (!confirm("Spustiť import SMS hlasov s vybraným súborom?")) return;
                        form.submit();
                    });

                    fileLabel.after(importBtn);

                    // File selected
                    fileInput.addEventListener("change", function () {
                        if (fileInput.files.length > 0) {
                            const fileName = fileInput.files[0].name;
                            fileLabel.textContent = fileName;
                            fileLabel.style.display = "inline-block";
                            importBtn.style.display = "inline-block";
                        }
                    });
                });
            </script>
        <?php
    }

    add_action( 'admin_init', 'import_contest_entry_sms_votes' );

    function import_contest_entry_sms_votes() {
        if (
            isset( $_POST['contest-entry-import'] ) &&
            isset( $_POST['contest_entry_import_nonce'] ) &&
            wp_verify_nonce( $_POST['contest_entry_import_nonce'], 'contest_entry_import' ) &&
            ! empty( $_FILES['contest-entry-import-file']['tmp_name'] )
        ) {
            $result = run_contest_entry_sms_votes_import( $_FILES['contest-entry-import-file']['tmp_name'] );

            $args = [
                'contest_entry_sms_votes_import_status'     => 'success',
                'contest_entry_sms_votes_import_updated'    => $result['contest_entry_sms_votes_import_updated'],
            ];

            if ( ! empty( $result['contest_entry_sms_votes_import_not_found'] ) ) {
                $args['contest_entry_sms_votes_import_not_found'] = implode( ',', $result['contest_entry_sms_votes_import_not_found'] );
            }

            if ( ! empty( $result['contest_entry_sms_votes_import_invalid'] ) ) {
                $args['contest_entry_sms_votes_import_invalid'] = implode( ',', $result['contest_entry_sms_votes_import_invalid'] );
            }

            wp_redirect( add_query_arg( $args, wp_get_referer() ) );
            exit;
        }
    }

    function run_contest_entry_sms_votes_import( $file ) {
        $updated = 0;
        $not_found = [];
        $invalid = [];

        $counts = [];

        if ( ( $handle = fopen( $file, 'r' ) ) !== false ) {

            while ( ( $row = fgetcsv( $handle, 0, ';' ) ) !== false ) {

                $text = trim( $row[2] ?? '' );

                if ( preg_match( '/\b(milacik[1-9]|milacik10)\b/i', $text, $matches ) ) {

                    $key = strtolower( $matches[1] );

                    if ( ! isset( $counts[$key] ) ) {
                        $counts[$key] = 0;
                    }

                    $counts[$key]++;
                } else {
                    $invalid[] = $text;
                }
            }

            fclose( $handle );
        }

        foreach ( $counts as $sms_code => $votes ) {

            $posts = get_posts( array(
                'post_type'      => 'contest_entry',
                'posts_per_page' => 1,
                'meta_query'     => array(
                    array(
                        'key'   => 'sms_code',
                        'value' => $sms_code,
                    )
                )
            ));

            if ( empty( $posts ) ) {
                $not_found[] = $sms_code;
                continue;
            }

            $post_id = $posts[0]->ID;
            $current = (int) get_field('sms_votes', $post_id);
            update_field('sms_votes', $current + $votes, $post_id);
            $updated++;
        }

        return [
            'contest_entry_sms_votes_import_updated'   => $updated,
            'contest_entry_sms_votes_import_not_found' => $not_found,
            'contest_entry_sms_votes_import_invalid'   => $invalid,
        ];
    }

    /*function run_contest_entry_sms_votes_import( $file ) {
        $rows = array_map( 'str_getcsv', file( $file ) );

        $updated = 0;
        $not_found = [];
        $invalid = [];

        foreach ( $rows as $index => $row ) {
            if ( count( $row ) < 2 ) {
                $invalid[] = "Row " . ( $index + 1 );
                continue;
            }

            [$sms_code, $value] = $row;

            $sms_code = trim( $sms_code );
            $value = trim( $value );

            if ( empty( $sms_code ) || ! is_numeric( $value )) {
                $invalid[] = $sms_code ?: "Row " . ( $index + 1 );
                continue;
            }

            $post = get_posts( array(
                'post_type'         => 'contest_entry',
                'posts_per_page'    => 1,
                'meta_key'          => 'sms_code',
                'meta_value'        => $sms_code,
            ) );

            if ( ! $post ) {
                $not_found[] = $sms_code;
                continue;
            }

            update_field( 'sms_votes', (int) $value, $post[0]->ID );
            $updated++;
        }

        return [
            'contest_entry_sms_votes_import_updated'   => $updated,
            'contest_entry_sms_votes_import_not_found' => $not_found,
            'contest_entry_sms_votes_import_invalid'   => $invalid,
        ];
    }*/

    add_action( 'admin_notices', 'display_contest_entry_sms_votes_import_message' );

    function display_contest_entry_sms_votes_import_message() {
        if ( ! isset( $_GET['contest_entry_sms_votes_import_status'] ) ) return;

        $updated   = isset( $_GET['contest_entry_sms_votes_import_updated'] ) ? (int) $_GET['contest_entry_sms_votes_import_updated'] : 0;
        $not_found = isset( $_GET['contest_entry_sms_votes_import_not_found'] ) ? explode( ',', $_GET['contest_entry_sms_votes_import_not_found'] ) : [];
        $invalid   = isset( $_GET['contest_entry_sms_votes_import_invalid'] ) ? explode( ',', $_GET['contest_entry_sms_votes_import_invalid'] ) : [];

        ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>Import SMS hlasov prebehol úspešne.</strong></p>
                <p>Počet aktualizovaných príspevkov: <?php echo $updated; ?></p>
                <?php if ( ! empty( $not_found ) ) : ?>
                    <p>Nenájdené príspevky (<?php echo count( $not_found ); ?>): 
                        <?php echo implode( ', ', $not_found ); ?>
                    </p>
                <?php endif; ?>
                <?php if ( ! empty( $invalid ) ) : ?>
                    <p>Neplatné riadky (<?php echo count( $invalid ); ?>): 
                        <?php echo implode( ', ', $invalid ); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php
    }