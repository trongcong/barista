"use strict";
import {helpers} from "./_helpers";

export const FilterJobs = () => {
    const $wrap = jQuery('.__job-filter-wrap');
    if (!$wrap.length) return;
    const $form = $wrap.find('form'),
        classAddLoading = '.__job-filter-content-side',
        filterLink = $form.attr(`action`),
        jobTitle = $form.find(`input[name="filter-title"]`),
        jobLocation = $form.find(`select[name="filter-location"]`),
        jobType = $form.find(`select[name="job-type"]`),
        jobCompensationTypes = $form.find(`select[name="job-compensation-types"]`),
        jobExperience = $form.find(`select[name="job-experience"]`),
        btnSubmit = $form.find('.btn-submit-filter'),
        jobOrderBy = $wrap.find('.wrapper-fillter select[name="order-by"]'),
        itemsWrap = $wrap.find('.__job-filter-items-wrap'),
        resultsFilterWrap = $wrap.find('.results-filter-wrapper'),
        resultsFilter = $wrap.find('.results-filter-wrapper .results-filter'),
        resultsCount = $wrap.find('.wrapper-fillter .results-count');

    const __query = () => {
        const query = $form.serializeArray();
        const orderBy = jobOrderBy.val();

        const q = query.reduce((accumulator, e) => {
            if (!!e.value) accumulator[e.name] = e.value

            return accumulator
        }, {})
        if (!!orderBy) q['order-by'] = orderBy
        return q;
    }
    const __buildQuery = () => {
        const params = new URLSearchParams(__query());
        const url = new URL(`${window.location.origin}${window.location.pathname}`);

        url.search = params.toString()
        window.history.pushState({}, '', url);
    }

    const __ajax = async () => {
        helpers.__addAjaxLoading($wrap, classAddLoading)
        helpers.scrollToElement(itemsWrap)
        try {
            const {items, found_posts, selected_filter} = await jQuery.ajax({
                type: "post",
                url: ajax_data.ajax_url,
                dataType: 'json',
                data: {
                    action: 'lt_ajax_filter_jobs',
                    security: ajax_data._ajax_nonce,
                    filter_link: filterLink,
                    ...__query()
                },
            });
            resultsCount.text(`Showing ${found_posts} results`)
            itemsWrap.html(items)
            resultsFilterWrap.css('display', !!selected_filter ? 'block' : 'none')
            resultsFilter.html(selected_filter)

            helpers.__removeAjaxLoading($wrap, classAddLoading);
        } catch (e) {
            console.error(e)
            helpers.__removeAjaxLoading($wrap, classAddLoading)
            alert("Have something error!")
        }
    }

    $form.on("submit", (e) => {
        e.preventDefault()
        __ajax();
        __buildQuery()
    })
    $form.add(jobOrderBy).on("change", (e) => {
        e.preventDefault()
        $form.trigger('submit')
    })
    btnSubmit.on("click", (e) => {
        e.preventDefault()
        $form.trigger('submit')
    })
};
