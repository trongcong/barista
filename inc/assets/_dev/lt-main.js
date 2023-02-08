import { wrap } from "../../../../../../wp-includes/js/dist/vendor/regenerator-runtime";

function _extends() {
    return (_extends = Object.assign || function (target) {
        for (let i = 1; i < arguments.length; i++) {
            let source = arguments[i];
            for (let key in source) Object.prototype.hasOwnProperty.call(source, key) && (target[key] = source[key]);
        }
        return target;
    }).apply(this, arguments);
}

jQuery(function ($) {
    "use strict";
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

    const scrollToElement = ($e, speed = 500) => {
        if (!$e.length) return
        $([document.documentElement, document.body]).animate({
            scrollTop: $e.offset().top - 50
        }, speed);
    };
    const __addAjaxLoading = ($wrap, classString) => {
        $wrap.find(classString).prepend('<div class="lds-ripple"><div></div><div></div></div>')
        $wrap.find(classString).addClass('__is-inprogress')
    }
    const __removeAjaxLoading = ($wrap, classString) => {
        $wrap.find(classString).removeClass('__is-inprogress')
        $wrap.find(`${classString} .lds-ripple`).remove()
    }

    const isValidRequired = (e, valid) => {
        $(e).find('.__err').remove()
        const $span = $(e).find('>span').length ? $(e).find('>span') : $(e).find('>label>span');
        if (!valid) {
            scrollToElement($(e));
            $span.after("<p class='__err'>This field is required</p>")
            return false
        }
        return true
    }

    const FilterBarista = () => {
        const $wrap = $(".__listings-wrap"),
            $open = $wrap.find(".__show-filter-popup"),
            $checkBox = $wrap.find(`.__lt-checkbox input[type="checkbox"]`),
            $inputsNumber = $wrap.find(`.__lt-input input[type="number"]`),
            $inputsRange = $wrap.find(`.__lt-range-slider input[type="range"]`),
            classAddLoading = ".__lt-inner-wrap"
        ;

        const countFilter = ({
                                 training_certification,
                                 barista_skills,
                                 volumes,
                                 hospitality_skills,
                                 year_exp_min,
                                 year_exp_max,
                                 year_exp_aus_min,
                                 year_exp_aus_max
                             }) => {
            let counter = 0;
            if (training_certification.length) counter += 1
            if (barista_skills.length) counter += 1
            if (volumes.length) counter += 1
            if (hospitality_skills.length) counter += 1
            if (year_exp_min > 0.5 || year_exp_max < 10) counter += 1
            if (year_exp_aus_min > 0.5 || year_exp_aus_max < 10) counter += 1
            return counter;
        };
        const getData = (element) => {
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
        const __ajax = async ({
                                  training_certification,
                                  barista_skills,
                                  volumes,
                                  hospitality_skills,
                                  year_exp_min,
                                  year_exp_max,
                                  year_exp_aus_min,
                                  year_exp_aus_max,
                              }) => {
            __addAjaxLoading($wrap, classAddLoading)
            scrollToElement($wrap)
            try {
                const {items, ...data} = await $.ajax({
                    type: "post",
                    url: ajax_data.ajax_url,
                    dataType: 'json',
                    data: {
                        action: 'lt_ajax_filter_barista',
                        security: ajax_data._ajax_nonce,
                        training_certification, barista_skills, volumes,
                        hospitality_skills,
                        year_exp_min: year_exp_min > year_exp_max ? year_exp_max : year_exp_min,
                        year_exp_max: year_exp_min > year_exp_max ? year_exp_min : year_exp_max,
                        year_exp_aus_min: year_exp_aus_min > year_exp_aus_max ? year_exp_aus_max : year_exp_aus_min,
                        year_exp_aus_max: year_exp_aus_min > year_exp_aus_max ? year_exp_aus_min : year_exp_aus_max,
                    },
                });
                //console.log(data)
                $wrap.find(".__lt-items-wrap").html(items)
                __removeAjaxLoading($wrap, classAddLoading)
            } catch (e) {
                console.error(e)
                __removeAjaxLoading($wrap, classAddLoading)
            }
        }
        let a = {};
        let b = []
        $checkBox.add($inputsNumber).add($inputsRange).on("change", function (e) {
            b.push((this.value));
            a[this.name] = b;
            console.log(this, a);

            __ajax(getData(this));

            $wrap.data("object-filter", JSON.stringify(getData(this)))

            const object_filter = $wrap.data("object-filter");
            if (object_filter) {
                const parseObjectFilter = JSON.parse(object_filter)
                //console.log(object_filter, parseObjectFilter, {...parseObjectFilter})

                const counter = countFilter({...parseObjectFilter});
                console.log(counter)
                $open.find("span:nth-child(1)").html(counter ? `<span class="filters-counter">${counter}</span>` : "")

                for (const prop in parseObjectFilter) {
                    if (parseObjectFilter.hasOwnProperty(prop)) {
                        console.log(prop, parseObjectFilter[prop])
                        if (typeof parseObjectFilter[prop] === "number" || typeof parseObjectFilter[prop] === "string") {
                            $(prop).val(parseObjectFilter[prop])
                        }
                        if (typeof parseObjectFilter[prop] === "object") {

                        }
                    }
                }
            }
        })

        $open.on('click', function (e) {
            $(this).parents(".__listings-wrap").find(".__lt-filter-modal").show()
        })
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
    }

    const RegisterBarista = () => {
        const $wrap = $(".__lt-register-barista"),
            $btnRegister = $wrap.find('.__lt-btn-register'),
            $required = $wrap.find('.__required'),
            $activeCode = $wrap.find('input[name="active_code"]'),
            classAddLoading = ".__lt-inner-register",
            $form = $wrap.find('form');

        const __ajax = async () => {
            __addAjaxLoading($wrap, classAddLoading)
            scrollToElement($form)
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
                __removeAjaxLoading($wrap, classAddLoading);
                $btnRegister.attr('disabled', true);
                window.location.href = data.url;
            } catch (e) {
                console.error(e)
                __removeAjaxLoading($wrap, classAddLoading)
                alert(e.responseJSON.data)
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
                if (!isValidRequired(e, valid)) return
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
            __addAjaxLoading($wrap, classAddLoading)
            scrollToElement($form)
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
                __removeAjaxLoading($wrap, classAddLoading);
                //window.location.href = data.url;
            } catch (e) {
                console.error(e)
                __removeAjaxLoading($wrap, classAddLoading)
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
                if (!isValidRequired(e, valid)) return
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

        const percentageMin = (slide1 / (max - min)) * 100 - 5;
        const percentageMax = (slide2 / (max - min)) * 100;

        parent.style.setProperty('--range-slider-value-low', percentageMin);
        parent.style.setProperty('--range-slider-value-high', percentageMax > slide2 * 10 ? slide2 * 10 : percentageMax);

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
    });
    $(window).on('load', () => {
    });
    $(window).on('load resize ready', () => {
    });
    $(window).on('scroll', () => {

    });
});
