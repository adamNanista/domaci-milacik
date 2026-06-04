<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    $votes = (int) get_field( 'votes' ) ?? 0;

    $unit = '';

    if ( $votes === 1 ) {
        $unit = 'hlas';
    } elseif ( $votes >= 2 && $votes <= 4 ) {
        $unit = 'hlasy';
    } else {
        $unit = 'hlasov';
    }

?>

<p class="inline-flex align-top items-start gap-3.5 text-neutral-500 text-xl">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="flex-none w-5 h-lh text-primary-600" aria-hidden="true"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/></svg>
    <span id="contest-vote-count">
        <?php echo esc_html( "{$votes} {$unit}" ); ?>
    </span>
</p>