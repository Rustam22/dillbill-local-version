<?php


use backend\models\Packets;
use backend\models\TrialConversation;
use backend\models\TrialConversationUsers;
use common\models\User;
use common\models\UserParameters;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

//debug($token);
//debug(Yii::$app->user->identity->verification_token);

const TRIAL_PRICE_ID = 1;

$user = User::findOne([
    'status' => User::STATUS_ACTIVE,
    //'id' => Yii::$app->user->id,
    'verification_token' => $token
]);

if (!$user) {
    throw new BadRequestHttpException('Wrong user token');
}

if ($user->userParameters->selectedPriceId == null) {
    throw new BadRequestHttpException('Packet not selected');
}

$lang = ucfirst(Yii::$app->language);
$currency = 'usd';
$currencyIcon = 'USD';
$ipCountry = Yii::$app->devSet->ip_info("Visitor", "Country");

//$ipCountry = 'Turkey';
//echo $ipCountry;

if ($ipCountry == 'Azerbaijan') {
    $currency = 'azn';
    $currencyIcon = 'AZN';
} elseif ($ipCountry == 'Turkey') {
    $currency = 'try';
    $currencyIcon = 'TL';
} elseif ($ipCountry == 'Brazil') {
    $currency = 'brl';
    $currencyIcon = 'BRL';
}


$packet = Packets::findOne(['id' => $user->userParameters->selectedPriceId]);


/***----------------    Security   ----------------***/
if ($packet == null) {
    throw new BadRequestHttpException(Yii::$app->devSet->getTranslate('requiredElements'));
}

if (($packet->id == TRIAL_PRICE_ID) AND (TrialConversationUsers::find()->where(['userId' => $user->id])->exists())) {
    throw new BadRequestHttpException('User has already booked a trial lesson');
}

if (($packet->id == TRIAL_PRICE_ID) AND ($user->userParameters->currentLevel == 'empty')) {
    $trialConversation = TrialConversation::findOne(['id' => $user->userParameters->trialLessonId]);
    $attendeesAmount = $trialConversation->getTrialConversationUsers()->asArray()->count();

    if ($attendeesAmount == Yii::$app->devSet->getDevSet('conversationGroupSize')) {
        throw new BadRequestHttpException('This class is full');
    }
}

if (($user->userParameters->currentLevel == 'empty') AND ($packet->id == TRIAL_PRICE_ID) AND (TrialConversationUsers::find()->where(['userId' => $user->id])->exists())) {
    throw new BadRequestHttpException('User has already booked a trial lesson');
}

if (($user->userParameters->currentLevel == 'empty') AND ($packet->id != TRIAL_PRICE_ID)) {
    return Yii::$app->getResponse()->redirect(Url::to(['dashboard/my-classes'], true));
}



/***----------------    Preliminary Stripe Payment intent   ----------------***/
\Stripe\Stripe::setApiKey(Yii::$app->params[(Yii::$app->devSet->isLocal()) ? 'testStripeSecretKey' : 'stripeSecretKey']);

// Price Calculation
$AMOUNT = $packet[$currency];
$AMOUNT = intval($AMOUNT * 100);

$container = json_decode($user->userParameters->container);
//debug($container);

try {
    $customerId = $user->userParameters->stripeCustomerId;

    if ($customerId == null) {
        $customer = \Stripe\Customer::create([
            'name' => $user->username,
            'email' => $user->email
        ]);

        $userParameters = UserParameters::findOne(['userId' => Yii::$app->user->id]);
        $userParameters->stripeCustomerId = $customer->id;
        $userParameters->save(false);
        $customerId = $customer->id;
    }

    if ($container == null) {
        $paymentIntent = \Stripe\PaymentIntent::create([
            'customer' => $customerId,
            'amount' => $AMOUNT,
            'currency' => $currency,
            'metadata' => [
                'for' => 'dillbill',
                'name' => $user->userProfile->name,
                'surname' => $user->userProfile->surname,
                'email' => $user->email,
                'level' => $user->userParameters->currentLevel,
                'packetId' => $packet->id,
                'weekdays' => '_',
                'availability' => '_',
                'promoCode' => '_'
            ]
        ]);
    } else {
        $paymentIntent = \Stripe\PaymentIntent::create([
            'customer' => $customerId,
            'amount' => $AMOUNT,
            'currency' => $currency,
            'metadata' => [
                'for' => 'dillbill',
                'name' => $user->userProfile->name,
                'surname' => $user->userProfile->surname,
                'email' => $user->email,
                'level' => $user->userParameters->currentLevel,
                'packetId' => $packet->id,
                'weekdays' => ($container->weekdays == null) ? '_' : $container->weekdays,
                'availability' => ($container->availability == null) ? '_' : $container->availability,
                'promoCode' => '_'
            ]
        ]);
    }

} catch (\Stripe\Exception\ApiErrorException $e) {
    throw new BadRequestHttpException($e->getMessage());
}


?>



<link href="<?=Yii::getAlias('@web');?>/css/payment/stripe.css" rel="stylesheet">
<script src="https://js.stripe.com/v3/"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
<script>
    let _csrf_frontend = '<?= Yii::$app->request->csrfToken; ?>'
    let _promoUrl = '<?= Url::to(['payment/promo'], true) ?>'
    let stripePublicKey = '<?= Yii::$app->params[(Yii::$app->devSet->isLocal()) ? 'testStripePublicKey' : 'stripePublicKey'] ?>'
    let paymentIntentUrl = '<?= Url::to(['payment/payment-intent'], true) ?>'
    let CLIENT_SECRET = '<?= $paymentIntent->client_secret ?>'
    let stripe = Stripe(stripePublicKey)
</script>
<script>
    $(document).ready(function () {
        $('#stripe-button').prop('disabled', false);

        function setPaymentIntent() {
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
                    //fontFamily: 'Inter, serif',
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

            // Show the customer the error from Stripe if their card fails to charge
            let showError = function(errorMsgText) {
                loading(false);
                let errorMsg = $("#card-error");
                errorMsg.html(errorMsgText);
                setTimeout(function() {
                    errorMsg.html('');
                }, 4000);
            };

            // Show a spinner on payment submission
            let loading = function(isLoading) {
                if (isLoading) {
                    // Disable the button and show a spinner
                    $('#stripe-button').prop('disabled', false);
                    $('#stripe-button .spinner-border').removeClass('display-none');
                } else {
                    // Enable the button and hide the spinner
                    $('#stripe-button').prop('disabled', false);
                    $('#stripe-button .spinner-border').addClass('display-none');
                }
            };

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

            // Shows a success message when the payment is complete
            let orderComplete = function(paymentIntentId) {
                $('button[data-bs-target="#cancel-confirm"]').click()
                loading(false);
            };
        }

        setTimeout(function () {
            setPaymentIntent();
            $('#stripe-button').prop('disabled', false);
        }, 500);


    })
</script>
<script src="<?=Yii::getAlias('@web');?>/js/payment/stripe.js" defer></script>
<script>
$(document).ready(function () {
    $('#promoCode').on("keyup change", function(e) {
        if($(this).val().length > 1) {
            $('.apply-promo').removeClass('disabled-visually')
        }
    })

    $('.apply-promo').click(function () {
        if($('#promoCode').val().length < 2) {
            alert('Text is too short')
            return false
        }

        $.ajax({
            url : _promoUrl,
            type : 'POST',
            async: false,
            data : {
                '_csrf-frontend': _csrf_frontend,
                'PROMO_CODE': $('#promoCode').val(),
                'PRICE_ID': '<?= $packet->id ?>',
                'CURRENCY': '<?= $currency ?>'
            },
            beforeSend: function() {
                $('.apply-promo .spinner-border').removeClass('display-none')
                $('.apply-promo').prop('disabled', true)
            },
            success : function(data) {
                data = JSON.parse(data)
                console.log(data)

                if(!data.success) {
                    $('.show-promo-info, .if-discount').slideUp(500)
                    $('.show-promo-info span').hide()
                    $('.show-promo-info').fadeIn(400)
                    $('.show-promo-info span:last-child').fadeIn(400)
                    $('.apply-promo .spinner-border').addClass('display-none')
                    $('.apply-promo').prop('disabled', false)
                } else {
                    let promoDiscount = parseFloat(data.promoDiscount)
                    let originalPrice = '<?= round($packet[$currency] - ($packet[$currency] * $packet['discountPercent']) / 100) ?>'
                    let total = '<?= round($packet[$currency] - ($packet[$currency] * $packet['discountPercent']) / 100) ?>'
                    let discountAmount = Math.round((promoDiscount * originalPrice) / 100)

                    // Generate new payment intent
                    $.ajax({
                        url : paymentIntentUrl,
                        type : 'POST',
                        async: false,
                        data : {
                            '_csrf-frontend': _csrf_frontend,
                            'PROMO_APPLIED': true,
                            'PROMO_CODE': $('#promoCode').val(),

                            'PRICE_ID': '<?= $packet->id ?>',
                            'CURRENCY': '<?= $currency ?>'
                        },
                        success : function(data) {
                            data = JSON.parse(data)

                            if(!data.success) {
                                showError(data.error)
                            } else {
                                CLIENT_SECRET = data.clientSecret

                                $('percent').html(promoDiscount)
                                $('discountAmount').html(-discountAmount)
                                $('total').html(total - discountAmount)

                                $('.show-promo-info, .if-discount').slideDown(500)
                                $('.show-promo-info span').hide()
                                $('.show-promo-info span:first-child').fadeIn(400)
                                $('.apply-promo').addClass('disabled-visually')
                            }

                            console.log(data);
                        },
                        error : function(request, error) {
                            console.log(error);
                            console.log(request);
                            alert(request.statusText);
                        },
                        complete: function () {
                            $('.apply-promo .spinner-border').addClass('display-none')
                            $('.apply-promo').prop('disabled', false)
                        }
                    });
                }
            },
            error : function(request, error) {
                console.log(error);
                console.log(request);
                alert(request.statusText);
            },
            complete: function() {
                //$('.apply-promo .spinner-border').addClass('display-none')
                //$('.apply-promo').prop('disabled', false)
            }
        });

    })
})
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap');
</style>
<style>
    .navbar {
        height: 68px;
        background-color: white !important;
        border-bottom: 1px solid #E0E2E7;
    }
    body {
        background-color: #F4F8FB;
        font-family: "Open Sans", serif !important;
    }

    .display-none {
        display: none;
    }
    .display-block {
        display: block;
    }
    .disabled-visually {
        pointer-events: none;
        opacity: 0.3 !important;
    }
    .payment-information, .payment-card {
        border-radius: 8px;
        border: 1px solid #DFDFDF;
        background-color: white;
        padding: 20px;
        width: 100%;
        box-shadow: 0 5px 10px rgba(179, 189, 217, 0.2);
    }
    .payment-information {
        margin-bottom: 20px;
    }
    .display-flex {
        display: flex;
    }
    .summary-list {
        background: #DFDFDF;
        height: 28px;
        color: #333333;
        font-weight: 500;
        font-size: 13px;
        justify-content: center;
        padding: 4px 0;
        margin-left: -40px;
        transform: translateX(20px);
        margin-top: 5px;
    }
    .summary-list p {
        text-align: center;
    }
    .payment-details {
        justify-content: space-between;
        font-weight: 600;
        font-size: 15px;
        color: #3F3F3F;
        margin: 15px 0;
    }
    .discount {
        color: #00B67A !important;
        font-weight: 600 !important;
    }
    .apply-promo {
        color: white;
        text-transform: uppercase;
        border-radius: 5px;
        background: #00B67A;
        font-weight: 600;
        font-size: 15px;
        height: 40px;
    }
    .apply-promo:hover {
        color: white;
    }
    #promoCode {
        height: 40px;
        width: -moz-available;
        margin-right: 15px;
        border: 1px solid #DFDFDF;
        border-radius: 5px;
        padding: 8px 20px;
        color: #3F3F3F;
    }
    #card-element {
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border: 1px solid #E0E2E7;
        padding: 11px 12px;
    }
    .payment-preliminary {
        margin-top: 18px;
        color: #0570DE;
        font-weight: 400;
        font-size: 13px;
    }
    #stripe-button {
        margin-top: 18px;
        background: #0074D4;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        border-radius: 4px;
        height: 48px;
        color: #FFFFFF;
        font-weight: 600;
        font-size: 18px;
        line-height: 24px;
        text-align: center;
    }
    #stripe-button:hover {
        color: #FFFFFF;
    }
    #stripe-button .spinner-border {
        width: 21px;
        height: 21px;
        margin-left: 5px;
    }
    .response-modal .modal-dialog {
        max-width: 430px;
    }
    .response-modal .modal-dialog .modal-content {
        border-radius: 16px;
    }
    .response-modal .btn-close {
        background: none;
        opacity: 1;
        padding: 0;
    }
    .response-modal .close, .response-modal .back {
        border-radius: 8px;
        background: #EEF0F3;
        height: 48px;
        color: #646E82;
        font-weight: 500;
        font-size: 15px;
    }
    .response-modal .primary-button {
        font-weight: 600;
        font-size: 16px;
        height: 48px;
        border-radius: 8px;
        background: #00B67A;
        color: #FFFFFF;
    }
    .response-modal h4 {
        color: #202734;
        font-weight: 600;
        font-size: 22px;
        text-align: center;
    }
    .response-modal p {
        color: #646E82;
        font-size: 15px;
        text-align: center;
    }
    .response-modal .modal-footer, .response-modal .modal-header {
        border: none;
    }
</style>


<nav class="navbar navbar-light bg-light">
    <div class="container" style="max-width: 440px;">
        <div class="col-6" align="left">
            <a href="<?= Yii::$app->request->hostInfo ?>">
                <img width="102" src="/img/landing/DillBill_Logo.svg" alt="DillBill Logo">
            </a>
        </div>
        <div class="col-6" align="right">
            <img width="120" src="/img/payment/powered-by-stripe_gray.svg" alt="stripe logo">
        </div>
    </div>
</nav>


<body>
    <br>
    <div class="container" style="max-width: 440px;">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="payment-information position-relative">
                    <div class="display-flex" style="justify-content: space-between;margin-bottom: 10px;">
                        <h1 style="color: #0F52A9;font-size: 24px; font-weight: 600;">
                            <?= Yii::$app->devSet->getTranslate($packet['nameKeyword']) ?>
                        </h1>
                        <a href="<?= Yii::$app->request->referrer ?>">
                            <?= Yii::$app->devSet->getTranslate('back') ?>
                        </a>
                    </div>

                    <?php if($packet->id != TRIAL_PRICE_ID) { ?>
                        <div class="if-monthly">
                            <div class="summary-list display-flex">
                                <p><?= Yii::$app->devSet->getTranslate('courseBriefing24') ?></p>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if($packet->id != TRIAL_PRICE_ID) { ?>
                        <div class="if-discount display-none">
                            <div class="payment-details display-flex">
                                <span><?= Yii::$app->devSet->getTranslate('originalPrice') ?></span>
                                <span><original><?= round($packet[$currency] - ($packet[$currency] * $packet['discountPercent']) / 100) ?></original><?= $currencyIcon ?></span>
                            </div>

                            <div class="payment-details discount display-flex">
                                <span>%<percent>0</percent> <?= Yii::$app->devSet->getTranslate('discount') ?></span>
                                <span><discountAmount>-0</discountAmount> <?= $currencyIcon ?></span>
                            </div>

                            <div style="border-top: 1px solid rgba(223, 223, 223, 0.5);"></div>
                        </div>
                    <?php } ?>

                    <div class="payment-details display-flex">
                        <span><?= Yii::$app->devSet->getTranslate('totalFee') ?></span>
                        <span><total><?= round($packet[$currency] - ($packet[$currency] * $packet['discountPercent']) / 100) ?></total> <?= $currencyIcon ?></span>
                    </div>

                    <?php if($packet->id != TRIAL_PRICE_ID) { ?>
                        <div class="if-monthly">
                            <div style="border-top: 1px solid rgba(223, 223, 223, 0.5);"></div>

                            <label for="promoCode" style="font-weight: 600;font-size: 15px;color: #3F3F3F;margin-top: 16px;margin-bottom: 6px;">
                                <?= Yii::$app->devSet->getTranslate('discountCode') ?>
                            </label>
                            <div class="promo-code display-flex" style="justify-content: space-between;">
                                <input type="text" id="promoCode" placeholder="Enter code">
                                <button class="btn apply-promo disabled-visually position-relative">
                                    <?= Yii::$app->devSet->getTranslate('apply') ?>
                                    <div class="spinner-border text-light display-none position-absolute" role="status" style="width: 20px;height: 20px;right: 7px;top: 7px;"></div>
                                </button>
                            </div>
                            <div class="show-promo-info display-none">
                                <span class="display-none" style="color: #00B67A;font-weight: 400;font-size: 15px;">
                                     &nbsp;<?= Yii::$app->devSet->getTranslate('promoCodeApplied') ?>
                                </span>
                                <span class="display-none" style="color: #d63384;font-weight: 400;font-size: 15px;">
                                    &nbsp;<?= Yii::$app->devSet->getTranslate('invalidPromo') ?>
                                </span>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>


            <div class="col-12">
                <div class="payment-card">
                    <div class="payment-details display-flex" style="margin-top: 0;">
                        <span><?= Yii::$app->devSet->getTranslate('cardDetails') ?></span>
                        <img src="/img/payment/checkout-cards.svg" alt="payment cards">
                    </div>

                    <!--------------------------    Stripe Begins    -------------------------->
                    <form id="payment-form">
                        <div id="card-element">
                            <!--Stripe.js injects the Card Element-->
                        </div>
                        <code id="card-error" role="alert"></code>
                        <p class="result-message hidden inter-16" style="color: #24BB00;font-weight: 500;">
                            Payment succeeded, see the result in your
                            <a href="" target="_blank">Stripe dashboard.</a> Refresh the page to pay again.
                        </p>
                    </form>
                    <!--------------------------    Stripe Ends    -------------------------->

                    <div class="display-flex payment-preliminary">
                        <img src="/img/payment/card-circle.svg" alt="payment cards"> &nbsp; &nbsp;
                        <span>
                            <?= Yii::$app->devSet->getTranslate('oneTimePayment') ?>
                        </span>
                    </div>

                    <button id="stripe-button" class="btn payment-button w-100">
                        <?= Yii::$app->devSet->getTranslate('buyNow') ?>
                        <div class="spinner-border text-light display-none" role="status"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

<br><br><br>
</body>


<button type="button" class="display-none" data-bs-toggle="modal" data-bs-target="#cancel-confirm"></button>
<div class="modal fade response-modal" id="cancel-confirm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!--<div class="modal-header" style="padding: 20px 25px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.77778 5.9979L11.6352 2.14181C12.1216 1.65561 12.1216 0.850856 11.6352 0.364652C11.1488 -0.121551 10.3438 -0.121551 9.85744 0.364652L6 4.22075L2.14256 0.364652C1.65618 -0.121551 0.851153 -0.121551 0.36478 0.364652C-0.121593 0.850856 -0.121593 1.65561 0.36478 2.14181L4.22222 5.9979L0.36478 9.854C-0.121593 10.3402 -0.121593 11.145 0.36478 11.6312C0.616352 11.8826 0.93501 12 1.25367 12C1.57233 12 1.89098 11.8826 2.14256 11.6312L6 7.77506L9.85744 11.6312C10.109 11.8826 10.4277 12 10.7463 12C11.065 12 11.3836 11.8826 11.6352 11.6312C12.1216 11.145 12.1216 10.3402 11.6352 9.854L7.77778 5.9979Z" fill="#646E82"/>
                    </svg>
                </button>
            </div>-->
            <div class="modal-body" style="padding: 15px 10px 0;margin-top: 10px;">
                <div class="row justify-content-center">
                    <div class="col-12" align="center">
                        <img alt="successfulPayment" src="<?=Yii::getAlias('@web');?>/img/payment/successfulPayment.svg">
                    </div>
                    <div class="col-12" align="center">
                        <br>
                        <h5 style="font-weight: 600; font-size: 24px;">
                            <?= Yii::$app->devSet->getTranslate("tanksForYourSubscription") ?>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 20px 14px;">
                <div class="row w-100">
                    <div class="col-12" align="center">
                        <span style="color: #777777;font-size: 14px;font-weight: 400;">
                            <?= Yii::$app->devSet->getTranslate("paymentDate") ?> <?= date('d.m.Y') ?>
                        </span>
                        <div style="margin-top: 8px;"></div>
                        <a href="<?= Url::to(['dashboard/my-classes'], true) ?>">
                            <button type="button" class="btn primary-button w-100">
                                <?= Yii::$app->devSet->getTranslate("goToDashboard") ?>
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





