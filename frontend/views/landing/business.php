<style>
    @media screen and (max-width: 576px) {
        .vave {
            top: calc(100% - 24px) !important;
        }
        .logo {
            height: 25px!important;
        }

        .padding-bottom-1 {
            padding-top: 20px!important;
        }
        .text-style-9 {
            font-size: 18px;
        }
        .text-style-10 {
            font-size: 16px;
        }
        .container-1 {
            padding: 70px 0!important;
        }

    }
    .text-style-9 {
        font-weight: 600;
        font-size: 24px;
        line-height: 33px;
        margin-top: 20px;
    }
    .text-style-10 {
        font-weight: 400!important;
        font-size: 18px;
        line-height: 25px;
        color: #000000;
        margin-top: 12px;
    }
</style>

<div class="container-fluid my-container-fluid body_color padding-lr-15px" style="z-index: 1000;">
    <div class="container max-width-1080 padding-tb-32 padding-navbar">
        <div class="row justify-content-between margin-lr--15px">
            <div class="col-auto padding-lr-15px d-flex align-items-center" style="padding-right: 0;">
                <a href="<?= Yii::$app->request->hostInfo ?>">
                    <img class="logo" src="/img/landing//DillBill_Logo.svg" alt="dillbill logo" height="32">
                </a>
                <div class="col-auto padding-lr-15px d-flex align-items-center business" style="padding-left: 10px; border-left: 1px solid #DBDBDB; margin-left: 10px;font-weight: 400;font-size: 16px;line-height: 22px;text-transform: uppercase;color: #212327;">
                    <?= Yii::$app->devSet->getTranslate('business') ?>
                </div>
            </div>
            <div class="col-auto padding-lr-15px d-flex align-items-center" style="padding-left: 0;">
                <a href="https://airtable.com/shr1kB04b8WCwPbVw" target="_blank">
                    <button type="button" class="btn btn-outline-primary border-radius-5px d-flex align-items-center signUp nav-button"  style=" font-size: 16px; font-weight: 500; ">
                        <?= Yii::$app->devSet->getTranslate('getOffer') ?>
                        <img src="/img/landing/chevron_right.svg" alt="chevron icon" style="margin-left: 10px">
                    </button>
                </a>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid padding-lr-15px" style="position: relative; overflow: hidden">
    <img src="/img/landing/vave.svg" class="vave" style="position: absolute; width: 100%; top: calc(100% - 57px); left: 0;">
    <div class="container max-width-1080 padding-0 padding-bottom-1" style="padding-bottom: 140px">
        <div class="row justify-content-between margin-lr--15px align-items-center margin-top-58px justify-content-around justify-content-sm-between">
            <div class=" col-md-7 col-lg-6 col-12 order-2 order-md-1 padding-lr-15px d-flex justify-content-between flex-column">
                <h1 class="my-h1" style="font-weight: 600">
                    <?= Yii::$app->devSet->getTranslate('businessTitle') ?>
                </h1>
                <div class="h1-p" style="margin-top: 18px;">
                    <p class="my-p d-flex align-items-center" style="margin-bottom: 16px">
                        <img src="/img/landing/Clock.svg" alt="clock" style="margin-right: 16px"><?= Yii::$app->devSet->getTranslate('point2') ?>
                    </p>
                    <p class="my-p d-flex align-items-center" style="margin-bottom: 16px">
                        <img src="/img/landing/People.svg" alt="people" style="margin-right: 16px"><?= Yii::$app->devSet->getTranslate('point1') ?>
                    </p>
                    <p class="my-p d-flex align-items-center" style="margin-bottom: 16px">
                        <img src="/img/landing/Communication.svg" alt="communication" style="margin-right: 16px"><?= Yii::$app->devSet->getTranslate('point3') ?>
                    </p>
                </div>
                <a href="https://airtable.com/shr1kB04b8WCwPbVw" target="_blank">
                    <button type="button" class="btn btn-primary btn-lg my-btn-1 d-flex align-items-center head-button" style="--smooth-corners: 4; border-radius: 15px; padding: 13px 25px;">
                        <img src="/img/landing/Rocket.svg" alt="rocket icon">
                        <?= Yii::$app->devSet->getTranslate('getOffer') ?>
                    </button>
                </a>
            </div>
            <div class="col-md-5 col-9 padding-lr-15px d-flex align-items-center order-1 order-md-2 justify-content-center">
                <img src="/img/landing/business_img.png" alt="employees" width="100%">
            </div>
        </div>
    </div>
</div>


<div class="container-fluid padding-lr-15px padding-slide" style="background: white; padding: 60px 0; overflow: hidden">
    <div class="container slide brand-carousel section-padding owl-carousel max-width-1080">
        <div class="single-logo"><img src="/img/landing/bolt.svg" alt="bolt logo"></div>
        <div class="single-logo"><img src="/img/landing/techAcademy.svg" alt="techacademy logo"></div>
        <div class="single-logo"><img src="/img/landing/itu.svg" alt="itu uuniversity logo"></div>
        <div class="single-logo"><img src="/img/landing/paymes.svg" alt="paymes logo"></div>
    </div>
</div>


<div class="container-fluid " style="padding: 0 15px">
    <div class="container max-width-1080 container-1" style="padding: 120px 0">
        <div class="row" style="row-gap: 55px">
            <div class="col-md-4  col-lg-3 col-6">
                <img src="/img/landing/Chat2.svg" alt="practical english">
                <h3 class="text-style-9">
                    <?= Yii::$app->devSet->getTranslate('practicalEnglish') ?>
                </h3>
                <p class="text-style-10">
                    <?= Yii::$app->devSet->getTranslate('practicalEnglishDescription') ?>
                </p>
            </div>
            <div class="col-md-4  col-lg-3 col-6">
                <img src="/img/landing/People%20Working%20Together.svg" alt="small groups">
                <h3 class="text-style-9">
                    <?= Yii::$app->devSet->getTranslate('smallGroups') ?>
                </h3>
                <p class="text-style-10">
                    <?= Yii::$app->devSet->getTranslate('smallGroupsDescription') ?>
                </p>
            </div>
            <div class="col-md-4  col-lg-3 col-6">
                <img src="/img/landing/Schedule.svg" alt="calendar icon">
                <h3 class="text-style-9">
                    <?= Yii::$app->devSet->getTranslate('flexibleSyllabus') ?>
                </h3>
                <p class="text-style-10">
                    <?= Yii::$app->devSet->getTranslate('flexibleSyllabusDescription') ?>
                </p>
            </div>
            <div class="col-md-4  col-lg-3 col-6">
                <img src="/img/landing/Report%20Card.svg" alt="report icon">
                <h3 class="text-style-9">
                    <?= Yii::$app->devSet->getTranslate('reporting') ?>
                </h3>
                <p class="text-style-10">
                    <?= Yii::$app->devSet->getTranslate('reportingDescription') ?>
                </p>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid padding-lr-15px p-tb-120px" style="background: linear-gradient(180deg, rgba(210, 244, 255, 0) 0%, rgba(210, 244, 255, 0.5) 100%);">
    <div class="container max-width-1080 padding-0">
        <div class="row align-items-center justify-content-between" style="column-gap: 50px">
            <div class="col-lg-6 col-12 hiw-mobile">
                <h2 class="text-style-1">
                    <?= Yii::$app->devSet->getTranslate('experiencedTeachers') ?>
                </h2>
                <p class="text-style-2">
                    <?= Yii::$app->devSet->getTranslate('experiencedTeachersText') ?>
                </p>
                <a href="https://airtable.com/shr1kB04b8WCwPbVw" target="_blank">
                    <button type="button" class="btn btn-primary btn-lg my-btn-1 d-flex align-items-center head-button" style="--smooth-corners: 4; border-radius: 15px; padding: 13px 25px;">
                        <img src="/img/landing/Rocket.svg" alt="rocket icon"><?= Yii::$app->devSet->getTranslate('getOffer') ?>
                    </button>
                </a>
            </div>
            <div class="d-flex" style="flex: 1; margin-top: 50px">
                <img src="/img/landing/teachers.png" alt="teachers card" width="100%">
            </div>
        </div>
    </div>
</div>


<div class="container-fluid padding-lr-15px" style="position: relative; overflow: hidden">
    <img src="/img/landing/vave.svg" class="vave" style="position: absolute; width: 100%; top: calc(100% - 57px); left: 0;">
    <div class="container max-width-1080 padding-0 padding-bottom-1" style="padding: 150px 0">
        <div class="row justify-content-between margin-lr--15px align-items-center margin-top-58px justify-content-around justify-content-sm-between">
            <div class="col-md-5 col-9 padding-lr-15px d-flex align-items-center justify-content-center">
                <img src="/img/landing/Group_certificate.png" alt="digital certificate" width="100%">
            </div>
            <div class=" col-md-7 col-lg-6 col-12 order-2 order-md-1 padding-lr-15px d-flex justify-content-between flex-column">
                <h1 class="my-h1">
                    <?= Yii::$app->devSet->getTranslate('certificateDemonstratingTitle') ?>
                </h1>
                <div style="margin-top: 18px">
                    <p class="my-p d-flex align-items-center" style="margin-bottom: 16px; font-weight: 400;font-size: 24px;line-height: 33px;">
                        <?= Yii::$app->devSet->getTranslate('certificateDemonstratingDescription') ?>
                    </p>
                </div>
                <a href="https://airtable.com/shr1kB04b8WCwPbVw" target="_blank">
                    <button type="button" class="btn btn-primary btn-lg my-btn-1 d-flex align-items-center head-button" style="--smooth-corners: 4; border-radius: 15px; padding: 13px 25px;">
                        <img src="/img/landing/Rocket.svg" alt="rocket icon"><?= Yii::$app->devSet->getTranslate('getOffer') ?>
                    </button>
                </a>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid p-tb-100px resize" >
    <div class="container max-width-1080 padding-0" style="padding: 0 25px; max-width: 1026px;">
        <h2 style="text-align: center; font-weight: 600;font-size: 48px;line-height: 65px;color: #212327; margin-bottom: 48px">
            <?= Yii::$app->devSet->getTranslate('youNoLongerHaveExcuse') ?>
        </h2>
        <div class="row justify-content-start" style="margin: 0 -25px;">
            <div class="col-auto col-sm-4 d-grid" style="row-gap: 16px; margin-top: 16px; padding: 0 25px;">
                <div class="d-flex align-self-center">
                    <img src="/img/landing/Done.svg" alt="checked icon">
                    <span class="text-style-6">
                            <?= Yii::$app->devSet->getTranslate('c1') ?>
                        </span>
                </div>
                <div class="d-flex align-self-center">
                    <img src="/img/landing/Done.svg" alt="checked icon">
                    <span class="text-style-6">
                            <?= Yii::$app->devSet->getTranslate('c2') ?>
                        </span>
                </div>
                <div class="d-flex align-self-center">
                    <img src="/img/landing/Done.svg" alt="checked icon">
                    <span class="text-style-6">
                            <?= Yii::$app->devSet->getTranslate('c3') ?>
                        </span>
                </div>
            </div>
            <div class="col-auto col-sm-4 d-grid" style="row-gap: 16px; margin-top: 16px; padding: 0 25px;">
                <div class="d-flex align-self-center">
                    <img src="/img/landing/Done.svg" alt="checked icon">
                    <span class="text-style-6">
                            <?= Yii::$app->devSet->getTranslate('c4') ?>
                        </span>
                </div>
                <div class="d-flex align-self-center">
                    <img src="/img/landing/Done.svg" alt="checked icon">
                    <span class="text-style-6">
                            <?= Yii::$app->devSet->getTranslate('c5') ?>
                        </span>
                </div>
                <div class="d-flex align-self-center">
                    <img src="/img/landing/Done.svg" alt="checked icon">
                    <span class="text-style-6">
                            <?= Yii::$app->devSet->getTranslate('c6') ?>
                        </span>
                </div>
            </div>
            <div class="col-auto col-sm-4 d-grid" style="row-gap: 16px; margin-top: 16px; padding: 0 25px;">
                <div class="d-flex align-self-center">
                    <img src="/img/landing/Done.svg" alt="checked icon">
                    <span class="text-style-6">
                            <?= Yii::$app->devSet->getTranslate('c7') ?>
                        </span>
                </div>
                <div class="d-flex align-self-center">
                    <img src="/img/landing/Done.svg" alt="checked icon">
                    <span class="text-style-6">
                            <?= Yii::$app->devSet->getTranslate('c8') ?>
                        </span>
                </div>
                <div class="d-flex align-self-center">
                    <img src="/img/landing/Done.svg" alt="checked icon">
                    <span class="text-style-6" alt="checked icon">
                        <?= Yii::$app->devSet->getTranslate('c9') ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="justify-content-center d-flex">
            <a href="https://airtable.com/shr1kB04b8WCwPbVw" target="_blank">
                <button type="button" class="btn btn-primary btn-lg my-btn-1 d-flex align-items-center head-button" style="--smooth-corners: 4; border-radius: 15px; padding: 13px 25px;">
                    <img src="/img/landing/Rocket.svg" alt="rocket icon"><?= Yii::$app->devSet->getTranslate('getOffer') ?>
                </button>
            </a>
        </div>
    </div>
</div>
