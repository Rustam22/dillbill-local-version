$(document).ready(function () {

    /***---------------- Dropdown ----------------***/
    $(window).click(function (e) {
        let container = $(".selectCustom");

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.removeClass('isActive');
        }
    });

    $('.selectCustom').click(function () {
        ($(this).hasClass('isActive')) ? $(this).removeClass('isActive') : $(this).addClass('isActive');
    });

    $('.selectCustom .selectCustom-option').click(function () {
        $(this).closest('.selectCustom').find('.selectCustom-trigger span').html($(this).html().trim());
        $(this).closest('.selectCustom').find('.selectCustom-trigger span').attr("data-selected-value", $(this).data('value'));
    });


    /***---------------- Currency Operations ----------------***/
    $('.currency').click(function () {
        selectedCurrency = $(this).closest('.selectCustom').find('.selectCustom-trigger span').attr("data-selected-value");
        currentCurrencyData = 'data-' + selectedCurrency.toLowerCase() + '-value';
        currentCurrency = selectedCurrency.toLowerCase();
        console.log(currentCurrencyData + ' - ' + selectedCurrency)

        if (!$(this).hasClass('isActive')) {
            if ($("currencyicon").is('.do-flip')) {
                $("currencyicon").html($("currencyicon").attr("data-flip")).removeClass("do-flip")

                $('price').each(function () {
                    $(this).removeClass("do-flip")
                })
            }

            setTimeout(function () {
                $('currencyicon').attr('data-flip', currencyIcons[selectedCurrency]).addClass('do-flip');

                $('price').each(function () {
                    let currentPrice = parseFloat($(this).attr(currentCurrencyData))

                    if ($(this).attr('data-discount-value') !== undefined) {
                        console.log(currentPrice)
                        $(this).html(Math.round((currentPrice - (currentPrice * parseFloat($(this).attr('data-discount-value'))) / 100)));
                    } else {
                        $(this).html(Math.round(currentPrice));
                    }

                    $(this).attr('data-flip', $(this).html()).addClass('do-flip');
                })
            }, 100);
        }
    });


    /***---------------- Select Packet, Plan, Price ----------------***/
    $('.packet-card').click(function () {
        //$('.plans-schedules').removeClass('strong-display-none');
        $('.description-text').html($(this).data('packet-description')).stop().hide(0).fadeIn(800);
        $('.packet-card').removeClass('outline-active').addClass('outline-inactive');
        $(this).removeClass('outline-inactive').addClass('outline-active');
        $(this).find('input[name="flexRadioDefault"]').prop('checked', true)
    });
    $('.plan-card').click(function () {
        $(this).closest('.plans').find('.plan-card').removeClass('outline-active').addClass('outline-inactive');
        $(this).removeClass('outline-inactive').addClass('outline-active');
    });
    $('.price-card').click(function () {
        $('.price-card').removeClass('outline-active').addClass('outline-inactive')
        $(this).removeClass('outline-inactive').addClass('outline-active')
        if($(window).width() < 768) {
            let that = $(this);
            $('html,body, .brick-button').animate({
                scrollTop: that.offset().top + 80
            }, 1000);
            //$('html, body').animate({ scrollTop: $('.brick-button').height() }, 1000);
        }
    });


    /***---------------- Selectivity of Packets, Plans, Prices and Order Summary ----------------***/
    $('.packet-card').click(function () {
        let packetId = $(this).attr('data-packet-id');
        $('div[data-related-packet-id]').slideUp(200);
        $('div[data-related-packet-id="' + packetId + '"]').slideDown(300);
        $('div[data-related-packet-id="' + packetId + '"] [data-value="' + $('div[data-related-packet-id="' + packetId + '"] .selectCustom-trigger span').attr('data-selected-value') +'"]').click();
    });

    $('.plan-dropdown .selectCustom-option').click(function () {
        if (!$(this).hasClass('isActive')) {
            let planId = $(this).attr('data-plan-id');

            /***---------------- Assign Current Schedule Id ----------------***/
            let currentPacketId = $('.packet-card.outline-active').attr('data-packet-id');
            let currentPlanId = $('.plans[data-related-packet-id="' + currentPacketId + '"] .selectCustom-trigger span').attr('data-selected-value');
            let currentScheduleId = $('.schedules[data-related-plan-id="' + currentPlanId + '"] .selectCustom-trigger span').attr('data-selected-value');
            SCHEDULE_ID = parseInt(currentScheduleId);

            $('div[data-related-plan-id]').stop().hide(0);
            $('div[data-related-plan-id="' + planId + '"]').fadeIn(800);

            /***---------------- Assign Current Schedule Description ----------------***/
            //$('.description-text').html($('div[data-schedule-id="' + SCHEDULE_ID +'"]').attr('data-description-ok')).stop().hide(0).fadeIn(800);

            $('.price-card').removeClass('outline-active').addClass('outline-inactive');
            $('.pricing-card .brick-button').stop().hide(0);
            $('.mimic-button').fadeIn();

            $('div[data-price-related-plan-id]').stop().hide(0);
            $('div[data-price-related-plan-id="' + planId + '"]').fadeIn(1400);
        }
    });

    $('.schedule .selectCustom-option').click(function () {
        $('.description-text').html($(this).attr('data-description-ok')).stop().hide(0).fadeIn(800);
    });


    $('.price-card').click(function () {
        PRICE_ID = $(this).attr('data-price-id');
        PROMO_CODE = null;
        PROMO_APPLIED = false;
        $('#promoCode').val('');

        let totalUSD = $(this).attr('data-price-total-' + currentCurrency);
        let discount = $(this).attr('data-price-discount');
        let total = Math.round((totalUSD - (totalUSD * discount) / 100));

        $('.pricing-card .brick-button currencyicon').html(currencyIcons[selectedCurrency]);
        $('.pricing-card .brick-button price')
            .html(total)
            .attr('data-flip', total)
            .attr('data-usd-value', $(this).attr('data-price-total-usd'))
            .attr('data-azn-value', $(this).attr('data-price-total-azn'))
            .attr('data-try-value', $(this).attr('data-price-total-try'))
            .attr('data-brl-value', $(this).attr('data-price-total-brl'))
            .attr('data-discount-value', discount);
        $('.mimic-button').stop().hide(0);
        $('.pricing-card .brick-button').removeClass('display-none').fadeIn(500);
    });

    $('.tooltip-my').click(function (event) {
        event.stopPropagation();
        $(this).click();
    });


    /***---------------- Defaults----------------***/
    $('div[data-packet-id="12"]').click();

});