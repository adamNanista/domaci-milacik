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

        <header class="flex-none px-4 sticky top-0 z-100 text-white bg-primary-600 md:px-6" role="banner">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-center gap-4 md:gap-6">
                    <a href="<?php echo home_url(); ?>" class="flex-none block focus:-outline-offset-2">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/novycas.svg" alt="Logo Nový Čas" class="h-16" width="65" height="64" />
                    </a>
                    <nav class="order-last lg:order-0" id="header-nav">
                        <button class="group/toggle cursor-pointer inline-flex align-top items-center justify-center w-6 h-6 rounded-sm lg:hidden" id="header-toggle" type="button">
                            <span class="sr-only">Menu</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="group-[.open]/toggle:hidden" aria-hidden="true"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="hidden group-[.open]/toggle:block" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                        <?php 
                            wp_nav_menu( array(
                                'menu'          => 'Hlavné menu',
                                'menu_class'    => 'hidden w-screen px-4 py-5 overflow-auto fixed inset-x-0 top-16 bottom-0 space-y-4 text-black font-bold uppercase bg-white [&_a]:hover:underline md:px-6 md:py-7 md:space-y-6 lg:flex! lg:gap-4 lg:w-auto lg:p-0 lg:overflow-visible lg:static lg:inset-auto lg:space-y-0 lg:text-inherit lg:text-sm lg:bg-transparent lg:[&_li]:flex-none',
                                'menu_id'       => 'header-menu',
                                'container'     => false,
                            ) );
                        ?>
                    </nav>
                    <a href="<?php echo home_url( '/prihlasit-milacika/' ); ?>" class="flex-none ml-auto outline-accent-300 button button-sm button-accent">Prihlásiť miláčika</a>
                </div>
            </div>
        </header>