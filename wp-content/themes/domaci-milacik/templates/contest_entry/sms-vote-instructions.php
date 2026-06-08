<?php 

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

    $sms_code = get_field( 'sms_code' ) ?? '';
    $sms_contest_number = get_option( 'sms_contest_number' ) ?? '';
    $sms_contest_price = get_option( 'sms_contest_price' ) ?? '';

    if ( $sms_code ) {
        ?>
            <div class="flex items-center gap-6 p-6 border-2 border-neutral-200 rounded-xl">
                <div class="flex-none w-12">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-auto text-primary-600" aria-hidden="true"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
                </div>
                <div class="space-y-2">
                    <h2 class="text-2xl font-bold">Ako hlasovať?</h2>
                    <p class="text-neutral-500">
                        <?php echo "Pošli SMS v tvare {$sms_code} na číslo {$sms_contest_number}. Cena SMS je {$sms_contest_price} s DPH." ?>
                    </p>
                </div>
            </div>
        <?php
    } else {
        ?>
            <div class="flex items-center gap-6 p-6 border-2 border-neutral-200 rounded-xl">
                <div class="flex-none w-12">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-auto text-primary-600" aria-hidden="true"><circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="20" cy="16" r="2"/><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"/></svg>
                </div>
                <div class="space-y-2">
                    <h2 class="text-2xl font-bold">Ďakujeme za účasť!</h2>
                    <p class="text-neutral-500">Váš miláčik síce tentoraz nepostúpil medzi 10 finalistov SMS hlasovania, no veľmi si vážime, že ste sa do súťaže zapojili.</p>
                </div>
            </div>
        <?php
    }
?>