"use strict";
import {FilterJobs} from "./_filter-jobs";
import {helpers} from "./_helpers";

jQuery(function ($) {
    console.log('lt ready');

    $.fn.serializeFiles = function () {
        let form = $(this),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function (i, tag) {
            $.each($(tag)[0].files, function (i, file) {
                formData.append(tag.name, file);
            });
        });

        $.each(formParams, function (i, val) {
            formData.append(val.name, val.value);
        });

        return formData;
    };

    const countFilter = (data) => {
        const {
            training_certification,
            barista_skills,
            volumes, hospitality_skills,
            year_exp_min,
            year_exp_max,
            year_exp_aus_min,
            year_exp_aus_max
        } = data
        //console.log(data)
        let counter = 0;
        if (training_certification?.length) counter += 1;
        if (barista_skills?.length) counter += 1;
        if (volumes?.length) counter += 1;
        if (hospitality_skills?.length) counter += 1;
        if (year_exp_min > 0.5 || year_exp_max < 10) counter += 1;
        if (year_exp_aus_min > 0.5 || year_exp_aus_max < 10) {
            counter += 1
        }
        return counter;
    };

    const filterDataMapBarista = async (data) => {
        const baristaFiltering = new CustomEvent('baristaFiltering', {detail: {data}});
        document.dispatchEvent(baristaFiltering);
    }

    const __ajaxFilterBarista = async ({...object}, $wrap, classAddLoading) => {
        const {
            training_certification,
            barista_skills,
            volumes,
            hospitality_skills,
            year_exp_min,
            year_exp_max,
            year_exp_aus_min,
            year_exp_aus_max,
        } = object;
        $wrap.data("object-filter", JSON.stringify(object))

        helpers.__addAjaxLoading($wrap, classAddLoading)
        helpers.scrollToElement($wrap)
        try {
            const layout = new URLSearchParams(location.search)?.get('layout') || 'grid'
            const {items, count} = await $.ajax({
                type: "post",
                url: ajax_data.ajax_url,
                dataType: 'json',
                data: {
                    action: 'lt_ajax_filter_barista',
                    security: ajax_data._ajax_nonce,
                    layout,
                    training_certification, barista_skills, volumes,
                    hospitality_skills,
                    year_exp_min: year_exp_min > year_exp_max ? year_exp_max : year_exp_min,
                    year_exp_max: year_exp_min > year_exp_max ? year_exp_min : year_exp_max,
                    year_exp_aus_min: year_exp_aus_min > year_exp_aus_max ? year_exp_aus_max : year_exp_aus_min,
                    year_exp_aus_max: year_exp_aus_min > year_exp_aus_max ? year_exp_aus_min : year_exp_aus_max,
                },
            });

            if (layout === 'map') $wrap.data('barista-filtered', items)
            await filterDataMapBarista(items)
            $wrap.find(".__lt-items-wrap").html(items)
            $(".__counter-result").text(`(${count} barista${count > 1 ? "s" : ""})`)
            helpers.__removeAjaxLoading($wrap, classAddLoading)
        } catch (e) {
            console.error(e)
            helpers.__removeAjaxLoading($wrap, classAddLoading)
        }
    }

    const __getDataFilter = ($wrap) => {
        const $checkBoxCer = $wrap.find(`.__lt-checkbox input[name="training_certification[]"]:checked`),
            $checkBoxBaristaSkills = $wrap.find(`.__lt-checkbox input[name="barista_skills[]"]:checked`),
            $checkBoxVolumes = $wrap.find(`.__lt-checkbox input[name="volumes[]"]:checked`),
            $checkBoxHospitalitySkills = $wrap.find(`.__lt-checkbox input[name="hospitality_skills[]"]:checked`),
            $yearExpMin = $wrap.find(`input[name="year_exp_min"]`),
            $yearExpMax = $wrap.find(`input[name="year_exp_max"]`),
            $yearExpAusMin = $wrap.find(`input[name="year_exp_aus_min"]`),
            $yearExpAusMax = $wrap.find(`input[name="year_exp_aus_max"]`)
        ;
        const training_certification = $checkBoxCer.map((_, el) => $(el).val().trim()).get();
        const barista_skills = $checkBoxBaristaSkills.map((_, el) => $(el).val()).get();
        const volumes = $checkBoxVolumes.map((_, el) => $(el).val()).get();
        const hospitality_skills = $checkBoxHospitalitySkills.map((_, el) => $(el).val()).get();
        const year_exp_min = +$yearExpMin.val() || 0;
        const year_exp_max = +$yearExpMax.val() || 10;
        const year_exp_aus_min = +$yearExpAusMin.val() || 0;
        const year_exp_aus_max = +$yearExpAusMax.val() || 10;

        return {
            training_certification,
            barista_skills,
            volumes, hospitality_skills,
            year_exp_min,
            year_exp_max,
            year_exp_aus_min,
            year_exp_aus_max,
        }
    }

    const setCounterButtonFilter = ($wrap, $open) => {
        const object_filter = $wrap.data("object-filter");
        if (object_filter) {
            const parseObjectFilter = JSON.parse(object_filter);
            const counter = countFilter(parseObjectFilter);

            $open.find(">span:nth-child(1)").html(counter ? `<span class="filters-counter">${counter}</span>` : "")
        }
    };

    const FilterBarista = () => {
        const $wrap = $(".__listings-wrap"),
            $wrapModal = $(".__lt-filter-modal"),
            $open = $wrap.find(".__show-filter-popup"),
            $checkBox = $wrap.find(`.__lt-checkbox input[type="checkbox"]`),
            $inputsNumber = $wrap.find(`.__lt-input input[type="number"]`),
            $inputsRange = $wrap.find(`.__lt-range-slider input[type="range"]`),

            $checkBoxModal = $wrapModal.find(`.__lt-checkbox input[type="checkbox"]`),
            $inputsNumberModal = $wrapModal.find(`.__lt-input input[type="number"]`),
            $inputsRangeModal = $wrapModal.find(`.__lt-range-slider input[type="range"]`),
            classAddLoading = ".__lt-inner-wrap"
        ;

        $checkBox.add($inputsNumber).add($inputsRange).on("change", function (e) {
            __ajaxFilterBarista(__getDataFilter($wrap), $wrap, classAddLoading);
            setCounterButtonFilter($wrap, $open);
            renderDataFilter();
        })

        $checkBoxModal.add($inputsNumberModal).add($inputsRangeModal).on("change", function (e) {
            __ajaxFilterBarista(__getDataFilter($wrapModal), $wrap, classAddLoading);
            setCounterButtonFilter($wrap, $open);
            renderDataFilter();
        })
    }

    const renderDataFilter = () => {
        const $wrap = $(".__listings-wrap");
        const object_filter = $wrap.data("object-filter");
        if (!object_filter) return;

        const parseObjectFilter = JSON.parse(object_filter)
        //console.log(parseObjectFilter)
        for (const prop in parseObjectFilter) {
            if (parseObjectFilter.hasOwnProperty(prop)) {
                const values = parseObjectFilter[prop]
                if (typeof values === "number" || typeof values === "string") {
                    const $element = $(`[name="${prop}"]`);
                    $element.val(parseObjectFilter[prop])
                    if ($element.attr("type") === "range") {
                        $element.trigger("input")
                    }
                }
                if (Array.isArray(values)) {
                    if (values.length) {
                        for (const v of values) {
                            $(`[name="${prop}[]"][value='${v}']`).prop('checked', true)
                        }
                    } else {
                        $(`[name="${prop}[]"]`).prop('checked', false)
                    }
                }
            }
        }
    }

    const HandleModal = () => {
        const $wrap = $(".__lt-filter-modal"),
            $content = $wrap.find(".__filter-popup-content"),
            $open = $(".__show-filter-popup"),
            $close = $wrap.find(".__lt-modal-close");

        $close.on('click', function (e) {
            $(this).parents(".__lt-filter-modal").hide()
        })
        $content.on('click', function (e) {
            if ($(e.target).hasClass('__filter-popup-content')) $(this).parents(".__lt-filter-modal").hide()
        })
        $open.on('click', function (e) {
            $(document).find(".__lt-filter-modal").show()
        })
    }

    const RegisterBarista = () => {
        const $wrap = $(".__lt-register-barista"),
            $btnRegister = $wrap.find('.__lt-btn-register'),
            $required = $wrap.find('.__required'),
            $activeCode = $wrap.find('input[name="active_code"]'),
            classAddLoading = ".__lt-inner-register",
            $form = $wrap.find('form');

        const __ajax = async () => {
            helpers.__addAjaxLoading($wrap, classAddLoading)
            helpers.scrollToElement($form)
            try {
                const formData = $form.serializeFiles()
                formData.append("action", 'lt_ajax_create_new_barista')
                formData.append("security", ajax_data._ajax_nonce)
                formData.append("type", "create")
                //for (let [name, value] of formData) {
                //    console.log(`${name} = ${value}`); // key1 = value1, then key2 = value2
                //}

                const {items, ...data} = await $.ajax({
                    type: "post",
                    url: ajax_data.ajax_url,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                });
                //console.log(data)

                $form.trigger("reset");
                helpers.__removeAjaxLoading($wrap, classAddLoading);
                $btnRegister.attr('disabled', true);
                window.location.href = data.url;
            } catch (e) {
                console.error(e)
                helpers.__removeAjaxLoading($wrap, classAddLoading)
                if (ajax_data.barista_profile_url) {
                    if (confirm(e.responseJSON.data) === true) window.location.href = ajax_data.barista_profile_url
                } else {
                    alert(e.responseJSON.data)
                }
            }
        }

        $activeCode.on("change input", function (e) {
            if ($(this).val().length) {
                $btnRegister.attr('disabled', false)
            } else {
                $btnRegister.attr('disabled', true)
            }
        })
        $btnRegister.on("click", (e) => {
            //e.preventDefault();
            const $files = $wrap.find('input[type="file"]')
            for (let i = 0; i < $required.length; i++) {
                const e = $required[i]
                let valid = false;
                if ($(e).hasClass('__lt-input')) {
                    valid = !!($(e).find('input').val()?.trim() || $(e).find('textarea').val()?.trim());
                } else if ($(e).hasClass('__lt-input-select')) {
                    valid = !!$(e).find('select').val();
                } else if ($(e).hasClass('__lt-checkbox-group')) {
                    valid = $(e).find('input[type="checkbox"]:checked').length > 0;
                }
                if (!helpers.isValidRequired(e, valid)) return
            }
            for (let i = 0; i < $files.length; i++) {
                const e = $files[i]
                const minFile = $(e).attr('min')
                let valid = false;
                valid = !minFile || minFile && e.files.length >= +minFile;
                if (!helpers.isValidMinMax(e, valid, {min: minFile})) return
            }

            __ajax();
        })
    }

    const CreateJob = () => {
        const $wrap = $(".__lt-create-job"),
            $btnCreateJob = $wrap.find('.__lt-btn-create'),
            $required = $wrap.find('.__required'),
            classAddLoading = ".__lt-inner-create-job",
            $form = $wrap.find('form');

        const __ajax = async () => {
            helpers.__addAjaxLoading($wrap, classAddLoading)
            helpers.scrollToElement($form)
            try {
                const formData = $form.serializeFiles()
                formData.append("action", 'lt_ajax_create_new_job')
                formData.append("security", ajax_data._ajax_nonce)
                formData.append("type", "create")
                const {items, ...data} = await $.ajax({
                    type: "post",
                    url: ajax_data.ajax_url,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                });
                //console.log(data)

                $form.trigger("reset");
                helpers.__removeAjaxLoading($wrap, classAddLoading);
                window.location.href = data.url;
            } catch (e) {
                console.error(e)
                helpers.__removeAjaxLoading($wrap, classAddLoading)
                alert("Have something error!")
            }
        }

        $btnCreateJob.on("click", (e) => {
            //e.preventDefault();
            for (let i = 0; i < $required.length; i++) {
                const e = $required[i]
                let valid = false;
                if ($(e).hasClass('__lt-input')) {
                    valid = !!($(e).find('input').val()?.trim() || $(e).find('textarea').val()?.trim());
                } else if ($(e).hasClass('__lt-input-select')) {
                    valid = !!$(e).find('select').val();
                } else if ($(e).hasClass('__lt-checkbox-group')) {
                    valid = $(e).find('input[type="checkbox"]:checked').length > 0;
                }
                if (!helpers.isValidRequired(e, valid)) return
            }

            __ajax();
        })
    }

    const ContactAction = () => {
        const $wrap = $(".__contact-item"),
            $action = $wrap.find("a");

        const __ajax = async () => {
            try {
                await $.ajax({
                    type: "post",
                    url: ajax_data.ajax_url,
                    dataType: 'json',
                    data: {
                        action: 'lt_ajax_contact_action',
                        security: ajax_data._ajax_nonce,
                        id: ajax_data.post_id
                    },
                });
            } catch (e) {
                console.error(e)
            }
        }

        $action.on("click", (e) => {
            __ajax();
        })
    }

    const onInput = (parent, e) => {
        const slides = parent.querySelectorAll('input');
        const min = parseFloat(slides[0].min);
        const max = parseFloat(slides[0].max);

        let slide1 = parseFloat(slides[0].value);
        let slide2 = parseFloat(slides[1].value);

        const percentageMin = (slide1 / (max - min)) * 100 - ((min / max)) * 100 - min;
        const percentageMax = (slide2 / (max - min)) * 100 - ((min / max)) * 100 - min;

        parent.style.setProperty('--range-slider-value-low', percentageMin);
        parent.style.setProperty('--range-slider-value-high', percentageMax > slide2 * (100 / max) ? slide2 * (100 / max) : percentageMax);

        if (slide1 > slide2) {
            const tmp = slide2;
            slide2 = slide1;
            slide1 = tmp;

            if (e?.currentTarget === slides[0]) {
                slides[0].insertAdjacentElement('beforebegin', slides[1]);
            } else {
                slides[1].insertAdjacentElement('afterend', slides[0]);
            }
        }

        parent.querySelector('.__lt-range-slider__display').setAttribute('data-low', slide1);
        parent.querySelector('.__lt-range-slider__display').setAttribute('data-high', slide2);
    }

    const GotoBaristaProfile = () => {
        const tab_profile = '.um-account-side [data-tab="profile_barista"]';
        if (!!ajax_data.barista_profile_url) {
            const profile_barista_url = ajax_data.barista_profile_url
            $(document).find(tab_profile).attr('href', profile_barista_url)
            $(document).find(tab_profile).on('click', function () {
                window.location.href = profile_barista_url
            })
        }
    }
    const BaristaAction = () => {
        const makeAjaxRequest = async (data) => {
            try {
                await $.ajax({
                    type: "post",
                    url: ajax_data.ajax_url,
                    dataType: 'json',
                    data: data,
                });
                window.location.reload();
            } catch (e) {
                console.error(e)
            }
        };
        const handleInputChange = async (name, value) => {
            const data = {
                action: 'lt_ajax_barista_action',
                security: ajax_data._ajax_nonce,
                id: ajax_data.post_id,
                [name]: value,
            };
            await makeAjaxRequest(data);
        };
        $('input[name="had_a_job"]').on('change', async function (e) {
            const checked = $(this).is(':checked');
            await handleInputChange('had_a_job', checked);
        });

        $('input[name="hide_profile"]').on('change', async function (e) {
            const checked = $(this).is(':checked');
            await handleInputChange('hide_profile', checked);
        });
        // const hadAJob = $('input[name="had_a_job"]');
        // const hideProfile = $('input[name="hide_profile"]');
        // hadAJob.on('change', async function (e) {
        //     const checked = $(this).is(':checked')
        //     try {
        //         await $.ajax({
        //             type: "post",
        //             url: ajax_data.ajax_url,
        //             dataType: 'json',
        //             data: {
        //                 action: 'lt_ajax_barista_action',
        //                 security: ajax_data._ajax_nonce,
        //                 id: ajax_data.post_id,
        //                 had_a_job: checked,
        //             },
        //         });
        //         window.location.reload();
        //     } catch (e) {
        //         console.error(e)
        //     }
        // })
        // hideProfile.on('change', async function (e) {
        //     const checked = $(this).is(':checked')
        //     try {
        //         await $.ajax({
        //             type: "post",
        //             url: ajax_data.ajax_url,
        //             dataType: 'json',
        //             data: {
        //                 action: 'lt_ajax_barista_action',
        //                 security: ajax_data._ajax_nonce,
        //                 id: ajax_data.post_id,
        //                 hide_profile: checked,
        //             },
        //         });
        //         window.location.reload();
        //     } catch (e) {
        //         console.error(e)
        //     }
        // })
    }

    const SaveListBaristaViewed = () => {
        const BARISTA_VIEWED_KEY = 'barista-viewed'
        const post_id = +ajax_data.post_id
        const baristaViewed = JSON.parse(localStorage.getItem(BARISTA_VIEWED_KEY)) || [];
        if (!baristaViewed?.includes(post_id)) {
            baristaViewed.push(post_id)
        }
        localStorage.setItem(BARISTA_VIEWED_KEY, JSON.stringify(baristaViewed));
    }
    $(document).ready(function () {
        document.querySelectorAll('.__lt-range-slider')
            .forEach(range => range.querySelectorAll('input')
                .forEach((input) => {
                    if (input.type === 'range') {
                        input.oninput = (e) => onInput(range, e);
                        onInput(range);
                    }
                }))
        FilterBarista();
        RegisterBarista();
        CreateJob();
        ContactAction();
        HandleModal();
        GotoBaristaProfile();
        FilterJobs();
        BaristaAction()
        SaveListBaristaViewed()
    });
    $(window).on('load', () => {
    });
    $(window).on('load resize ready', () => {
    });
    $(window).on('scroll', () => {

    });
});
