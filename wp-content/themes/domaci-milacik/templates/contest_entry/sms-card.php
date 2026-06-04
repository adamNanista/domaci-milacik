<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    $index = $args['index'] ?? 0;
    $sms_code = get_field( 'sms_code' ) ?? '';

?>

<article class="flex flex-col h-full relative border-2 border-neutral-200 rounded-xl md:flex-row">
    <span class="inline-flex align-top items-center justify-center w-8 h-8 absolute -left-2 -top-2 z-10 text-white font-bold text-center bg-primary-600 rounded-full">
        <?php echo esc_html( $index ); ?>
    </span>
    <a href="<?php the_permalink(); ?>" class="flex-none block overflow-hidden rounded-t-[0.625rem] focus:-outline-offset-2 hover:[&_img]:scale-105 md:w-24 md:rounded-t-none md:rounded-l-[0.625rem]">
        <?php the_post_thumbnail( 'contest-entry-card', array( 'class' => 'w-full aspect-4/3 object-cover object-center transition-transform duration-300 md:aspect-none md:h-full' ) ); ?>
    </a>
    <div class="flex-1 flex flex-col gap-4 p-6 md:flex-row md:items-center md:gap-6">
        <div class="flex-1 space-y-2">
            <h2 class="text-2xl font-bold">
                <a href="<?php the_permalink(); ?>" class="hover:underline"><?php the_title(); ?></a>
            </h2>
            <?php get_template_part( 'templates/contest_entry/sms-vote-count', 'md' ); ?>
        </div>
        <div class="flex-none">
            <a href="<?php the_permalink(); ?>" class="button button-md button-primary w-full">Hlasovať</a>
        </div>
    </div>
</article>