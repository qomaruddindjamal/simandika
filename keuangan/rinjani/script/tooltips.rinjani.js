$(function () {

    const $tooltip = $('#tooltip');
    const $content = $tooltip.find('.tooltip-content');

    // state to prevent immediately hiding the tooltip when we are in the process of showing it
    let suppressHide = false;

    // prevent clicks inside tooltip from bubbling to document (which hides it)
    $tooltip.on('click', function (e) {
        e.stopPropagation();
    });

    // global click -> hide tooltip (unless suppressed)
    $(document).on('click', function () {
        if (!suppressHide) hideTooltip();
    });

    function hideTooltip() {
        $tooltip.removeClass('visible').addClass('hidden').attr('aria-hidden', 'true');
    }

    $('.tooltip-close').on('click', function (e) {
        e.stopPropagation();   // prevent document click close race
        hideTooltip();         // call your hide handler
    });

    // exported function
    window.showTooltip = function (elementOrId, url, position = 'auto', width = null) {
        // accept either '#id', 'id' or element/jQuery
        let $el;
        if (typeof elementOrId === 'string') {
            // accept both "id" and "#id"
            if (elementOrId.startsWith('#')) $el = $(elementOrId);
            else $el = $('#' + elementOrId);
        } else {
            $el = $(elementOrId);
        }

        if ($el.length === 0) {
            console.warn('showTooltip: source element not found:', elementOrId);
            return;
        }

        // Prevent document click from hiding while we load & position
        suppressHide = true;

        // load content async
        $content.load(url, function (responseText, statusText, xhr) {
            if (statusText === 'error') {
                $content.html('<div style="color:#900">Failed to load content</div>');
            }

            // apply optional width before measuring
            if (width !== null && !isNaN(Number(width))) {
                $tooltip.css('width', Number(width) + 'px');
            } else {
                // let CSS (default) control width; reset inline if any
                $tooltip.css('width', '');
            }

            // show while invisible to measure (we keep pointer-events disabled)
            $tooltip.removeClass('hidden').addClass('visible').attr('aria-hidden', 'false');

            // small timeout to ensure browser has applied layout (helps when loaded content includes images/fonts)
            // Usually immediate measurement after .load() is OK, but this prevents race conditions.
            setTimeout(() => {
                positionAndClamp($el, position);
                // allow hiding after a short delay to avoid immediate hide due to click event propagation
                setTimeout(() => { suppressHide = false; }, 120);
            }, 10);

        });
    };

    function positionAndClamp($el, positionPref) {
        const offset = $el.offset();
        const elW = $el.outerWidth();
        const elH = $el.outerHeight();

        const tooltipW = $tooltip.outerWidth();
        const tooltipH = $tooltip.outerHeight();

        // window viewport note: use $(window).height() for viewport height (scroll relative to document)
        const viewportH = $(window).height();
        const scrollTop = $(window).scrollTop();

        // determine top or bottom
        let showTop;
        if (positionPref === 'top') showTop = true;
        else if (positionPref === 'bottom') showTop = false;
        else { // auto
            // space above relative to document top (offset.top - scrollTop)
            const availableAbove = offset.top - scrollTop;
            const availableBelow = viewportH - (availableAbove + elH);
            showTop = (availableAbove > tooltipH + 12) && (availableAbove >= availableBelow);
            if (!showTop && availableBelow > tooltipH + 12) showTop = false;
            // fallback: if neither fully fits, prefer below
        }

        if (showTop) {
            $tooltip.removeClass('bottom').addClass('top');
        } else {
            $tooltip.removeClass('top').addClass('bottom');
        }

        // compute left so center aligns with element center
        let left = offset.left + (elW / 2) - (tooltipW / 2);
        // clamp horizontally with 10px margin
        const minLeft = 10;
        const maxLeft = $(window).width() - tooltipW - 10;
        left = Math.max(minLeft, Math.min(left, maxLeft));

        // compute top
        let top;
        if (showTop) {
            top = offset.top - tooltipH - 12; // small gap
        } else {
            top = offset.top + elH + 12;
        }

        // ensure tooltip doesn't go above document top or below document end
        top = Math.max(6 + $(document).scrollTop(), top);

        $tooltip.css({ top: Math.round(top) + 'px', left: Math.round(left) + 'px' });
    }

});