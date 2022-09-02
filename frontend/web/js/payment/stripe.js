$(document).ready(function () {

    // A reference to Stripe.js initialized with your real test publishable API key.
    let stripe = Stripe(stripePublicKey);
    let IFRAME_GENERATED = false;
    //console.log(stripe);

    // Disable the button until we have Stripe set up on the page
    $('#stripe-button').prop('disabled', true);


    $('.pricing-card .brick-button').click(function () {
        //if(!IFRAME_GENERATED) {
            //IFRAME_GENERATED = true;

            $.ajax({
                url : _generateCheckout,
                type : 'POST',
                data : {
                    '_csrf-frontend': _csrf_frontend,
                    'PRICE_ID': PRICE_ID,
                    'LESSON_ID': null
                },
                async: false,
                beforeSend: function() {
                    $('.pricing-card .brick-button .spinner-border').removeClass('display-none')
                },
                success : function(data) {
                    data = JSON.parse(data)
                    console.log(data)

                    if(!data.success) {
                        //alert(data.error)
                    } else {
                        let redirectUrl = _checkOutUrl + data.token

                        window.location = redirectUrl
                        window.location.replace(redirectUrl)
                        window.location.href = redirectUrl

                        let testTimerID = window.setTimeout(function() {
                            window.location.href = redirectUrl
                        }, 3*250 )
                    }
                },
                error : function(request, error) {
                    console.log(error);
                    console.log(request);
                    //alert(request.statusText);
                },
                complete: function() {
                    $('.pricing-card .brick-button .spinner-border').addClass('display-none')
                }
            });

            /*$.ajax({
                url : paymentIntentUrl,
                type : 'POST',
                data : {
                    '_csrf-frontend': _csrf_frontend,
                    'PROMO_APPLIED': PROMO_APPLIED,
                    'PROMO_CODE': PROMO_CODE,
                    'SCHEDULE_ID': SCHEDULE_ID,
                    'PRICE_ID': PRICE_ID,
                    'CURRENCY': currentCurrency
                },
                async: true,
                beforeSend: function() {

                },
                success : function(data) {
                    data = JSON.parse(data);
                    console.log(data);

                    if(!data.success) {
                        showError(data.error);
                    } else {
                        CLIENT_SECRET = data.clientSecret;

                        let elements = stripe.elements();
                        let style = {
                            base: {
                                color: "#32325d",
                                //fontFamily: 'Inter, serif',
                                fontSmoothing: "antialiased",
                                fontSize: "17px",
                                "::placeholder": {
                                    color: "#32325d"
                                }
                            },
                            invalid: {
                                fontFamily: 'Inter, serif',
                                color: "#fa755a",
                                iconColor: "#fa755a"
                            }
                        };
                        let card = elements.create("card", { style: style });

                        // Stripe injects an iframe into the DOM
                        card.mount("#card-element");

                        card.on("change", function (event) {
                            // Disable the Pay button if there are no card details in the Element
                            //document.querySelector(".order-summary .brick-button").disabled = event.empty;
                            $('#stripe-button').prop('disabled', event.empty);
                            document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
                        });

                        let form = $('#payment-form');

                        // Submit Form
                        form.submit(function (event) {
                            event.preventDefault();
                            // Complete payment when the submit button is clicked
                            payWithCard(stripe, card, CLIENT_SECRET);
                        });

                        // Submit Stripe Form if Buy Now Clicked
                        $('#stripe-button').click(function () {
                            form.submit();
                        });
                    }
                },
                error : function(request, error) {
                    console.log(error);
                    console.log(request);
                    alert(request.statusText);
                },
                complete: function() {

                }
            });*/


        //}
    });




    let payWithCard = function(stripe, card, clientSecret) {
        loading(true);
        stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: card
            }
        }).then(function(result) {
            if (result.error) {
                console.log(result);
                showError(result.error.message);
            } else {
                orderComplete(result.paymentIntent.id);
            }
        });
    };


    // Show a spinner on payment submission
    let loading = function(isLoading) {
        if (isLoading) {
            // Disable the button and show a spinner
            $('#stripe-button').prop('disabled', true);
            $('#stripe-button .spinner-border').removeClass('display-none');
        } else {
            // Enable the button and hide the spinner
            $('#stripe-button').prop('disabled', false);
            $('#stripe-button .spinner-border').addClass('display-none');
        }
    };


    // Show the customer the error from Stripe if their card fails to charge
    let showError = function(errorMsgText) {
        loading(false);
        let errorMsg = $("#card-error");
        errorMsg.html(errorMsgText);
        setTimeout(function() {
            errorMsg.html('');
        }, 4000);
    };


    // Shows a success message when the payment is complete
    let orderComplete = function(paymentIntentId) {

        /***---------------- Backend Side Begins ----------------***/
        $.ajax({
            url : stripePayment,
            type : 'POST',
            data : {
                '_csrf-frontend': _csrf_frontend,
                'PROMO_APPLIED': PROMO_APPLIED,
                'PROMO_CODE': PROMO_CODE,
                'SCHEDULE_ID': SCHEDULE_ID,
                'PRICE_ID': PRICE_ID,
                'CURRENCY': currentCurrency
            },
            async: false,
            beforeSend: function() {

            },
            success : function(data) {
                data = JSON.parse(data)
                console.log(data);

                if(!data.success) {
                    $('#paymentModal .parse-error').html(data.error);
                    $('button[data-bs-target="#paymentModal"]').click();
                    showError(data.error);
                } else {
                    $('.success-result .packet-name').html(data.packetName);
                    $('.success-result .packet-price').html(data.price);
                    $('.success-result .applied-promo-code').html(data.promoCode);
                    $('.success-result .discount-amount').html(data.discountAmount);
                    $('.success-result .total-amount').html(data.totalPrice);
                    $('.pricing-block, .order-block').hide();
                    $('.success-result').fadeIn(800);
                }
            },
            error : function(request, error) {
                console.log(error);
                console.log(request);
                //alert(request.statusText);
            },
            complete: function() {

            }
        });
        /***---------------- Backend Side Ends ----------------***/

        loading(false);
        //document.querySelector(".result-message a").setAttribute("href", "https://dashboard.stripe.com/test/payments/" + paymentIntentId);
        //document.querySelector(".result-message").classList.remove("hidden");
        //document.querySelector("button").disabled = true;
    };


});