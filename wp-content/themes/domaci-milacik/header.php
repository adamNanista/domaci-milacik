<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class( 'flex flex-col min-h-[100dvh] text-black font-sans antialiased' ); ?>>
        
        <?php wp_body_open(); ?>

        <header class="flex-none relative z-100" role="banner">
            <div class="px-4 relative z-10 text-white bg-primary-600 md:px-6">
                <div class="max-w-6xl mx-auto">
                    <div class="flex items-center gap-4 md:gap-6">
                        <a href="<?php echo esc_url( 'https://www.cas.sk/' ); ?>" class="flex-none block focus:-outline-offset-2" target="_blank">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/novycas.svg" alt="Logo Nový Čas" class="h-12" width="49" height="48" />
                        </a>
                        <nav class="order-last lg:order-0" id="subheader-nav" aria-label="Navigácia na portál Čas.sk">
                            <button class="group/toggle cursor-pointer inline-flex align-top items-center justify-center w-4 h-6 rounded-sm lg:hidden" id="subheader-toggle" type="button">
                                <span class="sr-only">Menu</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="size-4 group-[.open]/toggle:hidden" aria-hidden="true"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="hidden size-4 group-[.open]/toggle:block" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                            <?php 
                                wp_nav_menu( array(
                                    'menu'          => 'Nový Čas menu',
                                    'menu_class'    => 'hidden h-[calc(100dvh-3rem)] p-6 overflow-auto absolute inset-x-0 top-full space-y-4 text-black font-bold uppercase bg-white border-t-2 border-t-neutral-200 [&_a]:hover:underline lg:flex! lg:items-center lg:gap-4 lg:h-auto lg:p-0 lg:overflow-visible lg:static lg:inset-auto lg:space-y-0 lg:text-inherit lg:text-sm lg:bg-transparent lg:border-t-0 lg:[&_li]:flex-none',
                                    'menu_id'       => 'subheader-menu',
                                    'container'     => false,
                                ) );
                            ?>
                        </nav>
                        <a href="<?php echo esc_url( 'https://estanok.sk/magazin/novy-cas/' ); ?>" class="flex-none ml-auto outline-accent-300 button button-xs button-accent" target="_blank">Predplatné</a>
                    </div>
                </div>
            </div>
            <div class="p-4 relative md:px-6">
                <div class="max-w-6xl mx-auto">
                    <div class="flex items-center gap-4 md:gap-6">
                        <a href="<?php echo home_url(); ?>" class="flex-none block focus:-outline-offset-2">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/domacimilacik.svg" alt="Logo Domáci Miláčik" class="w-30 h-10" width="120" height="40" />
                        </a>
                        <nav class="order-last lg:order-0 lg:ml-auto" id="header-nav" aria-label="Navigácia webu">
                            <button class="group/toggle cursor-pointer inline-flex align-top items-center justify-center size-6 rounded-sm lg:hidden" id="header-toggle" type="button">
                                <span class="sr-only">Menu</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="size-6 group-[.open]/toggle:hidden" aria-hidden="true"><path d="M3 5h18"/><path d="M3 12h18"/><path d="M3 19h18"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="hidden size-6 group-[.open]/toggle:block" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                            <?php 
                                wp_nav_menu( array(
                                    'menu'          => 'Hlavné menu',
                                    'menu_class'    => 'hidden h-[calc(100dvh-7.5rem)] p-6 overflow-auto absolute inset-x-0 top-full space-y-4 text-black font-bold uppercase bg-white border-t-2 border-t-neutral-200 [&_a]:hover:underline lg:flex! lg:items-center lg:gap-4 lg:h-auto lg:p-0 lg:overflow-visible lg:static lg:inset-auto lg:space-y-0 lg:text-inherit lg:text-sm lg:bg-transparent lg:border-t-0 lg:[&_li]:flex-none',
                                    'menu_id'       => 'header-menu',
                                    'container'     => false,
                                ) );
                            ?>
                        </nav>
                        <a href="<?php echo home_url( '/prihlasit-milacika/' ); ?>" class="flex-none ml-auto button button-sm button-primary lg:ml-0">Prihlásiť miláčika</a>
                    </div>
                </div>
            </div>
        </header>