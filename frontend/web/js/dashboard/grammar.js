$(document).ready(function () {
    $('.t-head, .selectCustom-option').click(function () {
        $('.t-head').removeClass('active')
        $(this).addClass('active')
        $('.material').stop().hide(0)
        $('.material[data-material-level="' + $(this).data('level') + '"]').removeClass('display-none').fadeIn(500)
    })

    /***---------------- Dropdown ----------------***/
    $(window).click(function (e) {
        let container = $(".selectCustom")

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.removeClass('isActive')
        }
    })

    $('.selectCustom').click(function () {
        ($(this).hasClass('isActive')) ? $(this).removeClass('isActive') : $(this).addClass('isActive')
    })

    $('.selectCustom .selectCustom-option').click(function () {
        $(this).closest('.selectCustom').find('.selectCustom-trigger span').html($(this).html().trim());
        $(this).closest('.selectCustom').find('.selectCustom-trigger span').attr("data-selected-value", $(this).data('value'))
    })
})