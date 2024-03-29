"use strict";

export const helpers = {
    scrollToElement($e, speed = 500) {
        if (!$e.length) return
        jQuery([document.documentElement, document.body]).animate({
            scrollTop: $e.offset().top - 50
        }, speed);
    },

    __addAjaxLoading($wrap, classString) {
        $wrap.find(classString).prepend('<div class="lds-ripple"><div></div><div></div></div>')
        $wrap.find(classString).addClass('__is-inprogress')
    },
    __removeAjaxLoading($wrap, classString) {
        $wrap.find(classString).removeClass('__is-inprogress')
        $wrap.find(`${classString} .lds-ripple`).remove()
    },
    isValidRequired(e, valid) {
        jQuery(e).find('.__err').remove()
        const $span = jQuery(e).find('>span:nth-child(1)').length ? jQuery(e).find('>span:nth-child(1)') : jQuery(e).find('>label>span:nth-child(1)');
        if (!valid) {
            helpers.scrollToElement(jQuery(e));
            $span.after("<p class='__err'>This field is required</p>")
            return false
        }
        return true
    },
    isValidMinMax(e, valid, {min, max}) {
        const el = jQuery(e).parents('.__lt-input')
        jQuery(el).find('.__err').remove()
        const $span = jQuery(el).find('>span:nth-child(1)').length ? jQuery(el).find('>span:nth-child(1)') : jQuery(el).find('>label>span:nth-child(1)');
        if (!valid) {
            helpers.scrollToElement(jQuery(el));
            if (min) $span.after(`<p class='__err'>Please upload at least ${min} files</p>`)
            if (max) $span.after(`<p class='__err'>Please upload at most ${max} files</p>`)
            return false
        }
        return true
    }
}
