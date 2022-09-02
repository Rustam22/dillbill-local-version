<?php
?>


<div class="container-fluid body_color padding-lr-15px" style="margin-top: 50px; margin-bottom: 141px">
    <div class="container padding-navbar" style="max-width: 815px">
        <h1 class="about-h1" style="text-align: center; color: #212327;font-weight: 600;font-size: 48px;line-height: 65px;">
            <?= Yii::$app->devSet->getTranslate('contactToUs') ?>
        </h1>
        <div class="row" style="margin-top: 31px; margin-bottom: 141px">
            <div class="col-12 col-sm-6" style="margin-bottom: 24px">
                <div style="border-radius: 20px; padding: 24px; border: 1px solid #DFDFDF;">
                    <p style="font-weight: 600;font-size: 24px;line-height: 40px;text-align: center;color: #000000; margin-bottom: 16px">
                        <?= Yii::$app->devSet->getTranslate('forStudents') ?>
                    </p>
                    <a href="mailto:hello@dillbill.net">
                        <p style="font-weight: 600;font-size: 16px;line-height: 34px;text-align: center;color: #1E9AF1;">
                            hello@dillbill.net
                        </p>
                    </a>
                </div>
            </div>
            <div class="col-12 col-sm-6" style="margin-bottom: 24px">
                <div style="border-radius: 20px; padding: 24px; border: 1px solid #DFDFDF;">
                    <p style="font-weight: 600;font-size: 24px;line-height: 40px;text-align: center;color: #000000; margin-bottom: 16px">
                        <?= Yii::$app->devSet->getTranslate('forTeachers') ?>
                    </p>
                    <a href="mailto:hr@dillbill.net">
                        <p style="font-weight: 600;font-size: 16px;line-height: 34px;text-align: center;color: #1E9AF1;">
                            hr@dillbill.net
                        </p>
                    </a>
                </div>
            </div>
            <div class="col-12 col-sm-6" style="margin-bottom: 24px">
                <div style="border-radius: 20px; padding: 24px; border: 1px solid #DFDFDF;">
                    <p style="font-weight: 600;font-size: 24px;line-height: 40px;text-align: center;color: #000000; margin-bottom: 16px">
                        <?= Yii::$app->devSet->getTranslate('whatsappSupport') ?>
                    </p>
                    <a href="https://wa.me/994775791000" target="_blank">
                        <p style="font-weight: 600;font-size: 16px;line-height: 34px;text-align: center;color: #1E9AF1;">
                            +994 77 579 10 00
                        </p>
                    </a>
                </div>
            </div>
            <div class="col-12 col-sm-6" style="margin-bottom: 24px">
                <div class="d-flex align-items-center" style="border-radius: 20px; padding: 24px; border: 1px solid #DFDFDF; flex-direction: column">
                    <p style="font-weight: 600;font-size: 24px;line-height: 40px;text-align: center;color: #000000; margin-bottom: 16px">
                        <?= Yii::$app->devSet->getTranslate('liveSupport') ?>
                    </p>
                    <button type="button" class="live-chat btn btn-primary  my-btn-1 d-flex align-items-center" style=" border-radius: 15px; height: 34px; font-weight: 700;font-size: 14px;line-height: 22px;">
                        <img src="/img/landing/chat.svg" alt="chat online" style="margin-right: 5px">
                        <?= Yii::$app->devSet->getTranslate('writeToUs') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="container d-flex flex-column align-items-center" style="max-width: 896px;">
        <h2 style="font-weight: 600;font-size: 34px;line-height: 46px;text-align: center;letter-spacing: -0.4px;color: #212327;">
            <?= Yii::$app->devSet->getTranslate('questionsAboutDillBill') ?>
        </h2>
        <p style="font-weight: 400;font-size: 18px;line-height: 25px;text-align: center;color: #212327; margin-top: 16px">
            <?= Yii::$app->devSet->getTranslate('ifYouWishOurContact') ?>
        </p>
        <button type="button" class="live-chat btn btn-dark  my-btn-1 d-flex align-items-center " style=" border-radius: 15px; font-weight: 400;font-size: 18px;line-height: 25px; padding: 16px 32px; background: black; border: unset; width: max-content; margin-top: 48px">
            <img src="/img/landing/headphone2.svg" alt="headphone icon" style="margin-right: 5px">
            <?= Yii::$app->devSet->getTranslate('writeToUs') ?>
        </button>
    </div>
</div>
