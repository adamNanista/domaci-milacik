<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    $sms_votes = (int) get_field( 'sms_votes' ) ?? 0;

    $unit = '';

    if ( $sms_votes === 1 ) {
        $unit = 'hlas';
    } elseif ( $sms_votes >= 2 && $sms_votes <= 4 ) {
        $unit = 'hlasy';
    } else {
        $unit = 'hlasov';
    }

?>

<p class="inline-flex align-top items-start gap-3.5 text-neutral-500 text-xl">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="flex-none w-5 h-lh text-primary-600" aria-hidden="true"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"/></svg>
    <span id="contest-vote-count">
        <?php echo esc_html( "{$sms_votes} {$unit}" ); ?>
    </span>
</p>