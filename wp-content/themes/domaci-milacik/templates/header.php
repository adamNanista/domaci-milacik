<header class="header" role="banner">
    <div class="container">
        <div class="flex items-center gap-4 md:gap-6">
            <a href="<?php echo home_url(); ?>" class="header-logo">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/novycas.svg" alt="Logo Nový Čas" width="65" height="64" />
            </a>
            <nav class="header-nav" id="header-nav">
                <button class="header-toggle" id="header-toggle" type="button">
                    <span class="visually-hidden">Menu</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="header-toggle-open lucide lucide-menu-icon lucide-menu"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="header-toggle-close lucide lucide-x-icon lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
                <?php 
                    wp_nav_menu( array(
                        'menu'          => 'Hlavné menu',
                        'menu_class'    => 'header-menu hidden',
                        'menu_id'       => 'header-menu',
                        'container'     => false,
                    ) );
                ?>
            </nav>
            <a href="<?php echo home_url( '/prihlasit-milacika/' ); ?>" class="header-button button button-sm button-accent">Prihlásiť miláčika</a>
        </div>
    </div>
</header>