"use strict";
jQuery(function ($) {
    console.log('admin ready');

    const BaristaAction = () => {
        const makeAjaxRequest = async (data) => {
            try {
                await $.ajax({
                    type: "post",
                    url: ajax_data.ajax_url,
                    dataType: 'json',
                    data: data,
                });
                // window.location.reload();
            } catch (e) {
                console.error(e)
            }
        };
        const handleInputChange = async (name, value, post_id) => {
            const data = {
                action: 'lt_ajax_barista_action',
                security: ajax_data._ajax_nonce,
                id: post_id,
                [name]: value,
            };
            await makeAjaxRequest(data);
        };
        $('input[name="had_a_job"]').on('change', async function (e) {
            e.preventDefault()
            const id = $(this).parents('.action-profile').data('post-id');
            const checked = $(this).is(':checked');
            await handleInputChange('had_a_job', checked, id);
        });

        $('input[name="hide_profile"]').on('change', async function (e) {
            e.preventDefault()
            const id = $(this).parents('.action-profile').data('post-id');
            const checked = $(this).is(':checked');
            await handleInputChange('hide_profile', checked, id);
        });
    }
    $(document).ready(function () {
        BaristaAction()
    });
    $(window).on('load', () => {
    });
    $(window).on('load resize ready', () => {
    });
    $(window).on('scroll', () => {

    });
});
