<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    get_header();

    while ( have_posts() ) {
        the_post();

        ?>
            <main <?php post_class( 'flex-1 px-6 *:max-w-6xl *:mx-auto *:mb-12 *:first:mt-12 [&>.alignfull]:max-w-none [&>.alignfull]:-mx-6 md:px-12 md:*:mb-24 md:*:first:mt-24 md:[&>.alignfull]:-mx-12' ); ?> role="main">
                <?php 
                    if ( ! is_front_page() ) {
                        ?>
                            <section class="mt-0! px-4 py-3 bg-neutral-100 md:px-6 alignfull">
                                <div class="max-w-6xl mx-auto">
                                    <a href="<?php echo home_url(); ?>" class="inline-flex align-top items-center justify-center gap-2.5 text-neutral-500 font-bold text-center uppercase outline-black hover:underline">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="flex-none size-4" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                                        Späť na domovskú stránku
                                    </a>    
                                </div>
                            </section>
                        <?php
                    }

                    the_content();
                ?>
            </main>
        <?php
    }

    get_footer();