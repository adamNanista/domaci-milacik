<?php

    if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'entry-form relative' ) ); ?>>
    <form class="peer/form grid gap-6 md:grid-cols-2 [&.loading]:opacity-50 [&.loading]:blur-sm" id="contest-entry-form" enctype="multipart/form-data" novalidate="novalidate">
        <p class="messages hidden md:col-span-full">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="messages-icon success" fill="currentColor" aria-hidden="true"><path d="M320 576C178.6 576 64 461.4 64 320C64 178.6 178.6 64 320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576zM438 209.7C427.3 201.9 412.3 204.3 404.5 215L285.1 379.2L233 327.1C223.6 317.7 208.4 317.7 199.1 327.1C189.8 336.5 189.7 351.7 199.1 361L271.1 433C276.1 438 282.9 440.5 289.9 440C296.9 439.5 303.3 435.9 307.4 430.2L443.3 243.2C451.1 232.5 448.7 217.5 438 209.7z"/></svg>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="messages-icon error" fill="currentColor" aria-hidden="true"><path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM231 231C240.4 221.6 255.6 221.6 264.9 231L319.9 286L374.9 231C384.3 221.6 399.5 221.6 408.8 231C418.1 240.4 418.2 255.6 408.8 264.9L353.8 319.9L408.8 374.9C418.2 384.3 418.2 399.5 408.8 408.8C399.4 418.1 384.2 418.2 374.9 408.8L319.9 353.8L264.9 408.8C255.5 418.2 240.3 418.2 231 408.8C221.7 399.4 221.6 384.2 231 374.9L286 319.9L231 264.9C221.6 255.5 221.6 240.3 231 231z"/></svg>
            <span id="contest-entry-form-messages"></span>
        </p>
        <div class="space-y-6">
            <label class="field">
                <span class="field-label">Meno <abbr class="required" title="Povinné">*</abbr></span>
                <input class="field-input" type="text" id="contest-entry-form-owner-name" name="contest-entry-form-owner-name" required placeholder="Zadajte meno" />
            </label>
            <label class="field">
                <span class="field-label">Email <abbr class="required" title="Povinné">*</abbr></span>
                <input class="field-input" type="email" id="contest-entry-form-owner-email" name="contest-entry-form-owner-email" required placeholder="Zadajte email" />
            </label>
            <label class="field">
                <span class="field-label">Meno miláčika <abbr class="required" title="Povinné">*</abbr></span>
                <input class="field-input" type="text" id="contest-entry-form-pet-name" name="contest-entry-form-pet-name" required placeholder="Zadajte meno miláčika" />
            </label>
            <label class="field">
                <span class="field-label">Popis miláčika <abbr class="required" title="Povinné">*</abbr></span>
                <textarea class="field-textarea" id="contest-entry-form-pet-description" name="contest-entry-form-pet-description" required placeholder="Napíšte niečo o svojom miláčikovi" rows="4"></textarea>
            </label>
        </div>
        <div class="space-y-6">
            <fieldset class="dropzone">
                <legend class="dropzone-legend">Fotografia miláčika <abbr class="required" title="Povinné">*</abbr></legend>
                <label class="dropzone-area" id="contest-entry-form-photo-panel">
                    <input class="sr-only" type="file" id="contest-entry-form-photo" name="contest-entry-form-photo" accept="image/jpeg,image/png" required />
                    <span class="dropzone-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </span>
                    <span class="dropzone-label">Pretiahnite fotku vášho miláčika sem</span>
                    <span class="dropzone-hint">alebo kliknite a vyberte zo zariadenia · JPG / PNG · max 5 MB</span>
                    <span class="dropzone-button button button-sm button-primary">Vybrať súbor</span>
                </label>
            </fieldset>
            <fieldset class="dropzone">
                <legend class="dropzone-legend">Video miláčika (voliteľné)</legend>
                <div class="flex">
                    <label class="cursor-pointer flex-1 inline-flex align-top items-center justify-center h-10 gap-2 px-4 text-neutral-500 text-sm font-bold text-center bg-neutral-100 border-2 border-neutral-100 rounded-t-sm transition-colors hover:bg-neutral-200 hover:border-neutral-200 has-focus:relative has-focus:outline-2 has-focus:outline-black has-focus:outline-offset-2 has-checked:text-white has-checked:bg-neutral-500 has-checked:border-neutral-500">
                        <input class="sr-only" type="radio" name="contest-entry-form-video-type" value="url" checked /> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="flex-none size-3.5" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                        Vložiť URL
                    </label>
                    <label class="cursor-pointer flex-1 inline-flex align-top items-center justify-center h-10 gap-2 px-4 text-neutral-500 text-sm font-bold text-center bg-neutral-100 border-2 border-neutral-100 rounded-t-sm transition-colors hover:bg-neutral-200 hover:border-neutral-200 has-focus:relative has-focus:outline-2 has-focus:outline-black has-focus:outline-offset-2 has-checked:text-white has-checked:bg-neutral-500 has-checked:border-neutral-500">
                        <input class="sr-only" type="radio" name="contest-entry-form-video-type" value="upload" />
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="flex-none size-3.5" aria-hidden="true"><path d="M12 3v12"/><path d="m17 8-5-5-5 5"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/></svg>
                        Nahrať video
                    </label>
                </div>
                <label class="field" id="contest-entry-form-video-url-panel">
                    <span class="field-label screen-reader-text">Vložiť URL</span>
                    <input class="field-input rounded-tl-none" type="url" id="contest-entry-form-video-url" name="contest-entry-form-video-url" placeholder="https://youtube.com/watch?v=..." />
                </label>
                <label class="dropzone-area rounded-tr-none hidden" id="contest-entry-form-video-upload-panel">
                    <input class="sr-only" type="file" id="contest-entry-form-video-upload" name="contest-entry-form-video-upload" accept="video/mp4" />
                    <span class="dropzone-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"/><rect x="2" y="6" width="14" height="12" rx="2"/></svg>
                    </span>
                    <span class="dropzone-label">Pretiahnite video vášho miláčika sem</span>
                    <span class="dropzone-hint">alebo kliknite a vyberte zo zariadenia · MP4 · max 30 MB</span>
                    <span class="dropzone-button button button-sm button-primary">Vybrať súbor</span>
                </label>
            </fieldset>
        </div>
        <div class="md:col-span-full">
            <label class="field hidden">
                <span class="field-label">Webstránka</span>
                <input class="input" type="text" id="contest-entry-form-website" name="contest-entry-form-website" autocomplete="off" />
            </label>
            <div>
                <label class="field checkbox">
                    <input class="field-checkbox" type="checkbox" id="contest-entry-form-consent-combined" name="contest-entry-form-consent-combined" required /> 
                    <span class="field-label">Súhlasím s <a href="#">pravidlami súťaže</a> a so spracovaním osobných údajov. <abbr class="required" title="Povinné">*</abbr></span>
                </label>
            </div>
            <div class="cf-turnstile empty:hidden" id="contest-entry-form-turnstile" data-sitekey="<?php echo CLOUDFLARE_TURNSTILE_SITE_KEY; ?>"></div>
        </div>
        <div class="md:col-span-full">
            <button id="contest-entry-form-submit" class="button button-lg button-primary w-full" type="submit">Odoslať prihlášku</button>
        </div>
    </form>
    <div class="hidden items-center justify-center absolute inset-0 text-neutral-500 peer-[.loading]/form:flex">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="size-6 animate-spin" aria-hidden="true"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
    </div>
</section>