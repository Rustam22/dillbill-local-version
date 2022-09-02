<?php

use yii\helpers\Url;

?>

<style>
    @media screen and (max-width: 576px) {
        .about-h1 {
            font-size: 29px !important;
            line-height: 39px !important;
        }

        .about-p {
            font-size: 16px !important;
            line-height: 22px !important;
        }
    }
</style>

<div class="container-fluid body_color padding-lr-15px" style="margin-top: 50px">
    <div class="container max-width-1080 padding-navbar">
        <h1 class="about-h1" style="text-align: center; color: #212327;font-weight: 600;font-size: 48px;line-height: 65px;"><?= Yii::$app->devSet->getTranslate('aboutDillBill') ?></h1>
        <h2 class="about-p" style="text-align: center; color: #212327;font-weight: 600;font-size: 24px;line-height: 33px; margin-top: 24px"><?= Yii::$app->devSet->getTranslate('helloAndWelcome') ?>!  👋 </h2>
        <p class="about-p" style="font-weight: 400;font-size: 24px;line-height: 33px;text-align: center; max-width: 714px; width: 100%; margin: 0 auto">
            <?= Yii::$app->devSet->getTranslate('dillbillWasFoundedBy') ?>
        </p>
        <h2 class="about-h1" style="text-align: center; color: #212327;font-weight: 600;font-size: 48px;line-height: 65px; margin-top: 50px"><?= Yii::$app->devSet->getTranslate('team') ?></h2>
        <div class="row" style="margin-top: 48px; margin-bottom: 40px">
            <div class="col-6 col-md-4 col-lg-3 d-flex justify-content-center">
                <a href="https://www.linkedin.com/in/sanan-ibrahimov-744780106/" target="_blank" style="width: 100%;display: contents;">
                    <div style="width: 60%; margin-bottom: 60px; position: relative;">
                        <div style="padding-bottom: 100%;  position: relative">
                            <img src="/img/landing/sanan_photo.jpg" alt="profile image" style="object-fit: cover;height: 100%;width: 100%;position: absolute;border-radius: 50%; overflow: hidden;filter: drop-shadow(0px 3px 4px rgba(0, 0, 0, 0.1));">
                            <div style="position: absolute; width: 33%; height: 33%; bottom: 0; right: 0; overflow: hidden; border-radius: 50%;">
                                <img src="/img/landing/Linkedin.svg"  alt="linkedin icon" width="100%">
                            </div>
                        </div>
                        <p style="font-weight: 600;font-size: 16px;line-height: 16px;text-align: center;color: #202734;margin-top: 16px">Sanan Ibrahimov</p>
                        <p style="font-weight: 600;font-size: 12px;line-height: 12px;text-align: center;text-transform: uppercase;color: #848FA3; margin-top: 4px">CEO</p>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3 d-flex justify-content-center">
                <a href="https://www.linkedin.com/in/rustam-atakishiyev-12783a202/" target="_blank" style="width: 100%;display: contents;">
                    <div style="width: 60%; margin-bottom: 60px; position: relative;">
                        <div style="padding-bottom: 100%;  position: relative">
                            <img src="/img/landing/n_rustam.png" alt="profile image" style="object-fit: cover;height: 100%;width: 100%;position: absolute;border-radius: 50%; overflow: hidden;filter: drop-shadow(0px 3px 4px rgba(0, 0, 0, 0.1));">
                            <div style="position: absolute; width: 33%; height: 33%; bottom: 0; right: 0; overflow: hidden; border-radius: 50%;">
                                <img src="/img/landing/Linkedin.svg"  alt="linkedin icon" width="100%">
                            </div>
                        </div>
                        <p style="font-weight: 600;font-size: 16px;line-height: 16px;text-align: center;color: #202734;margin-top: 16px">Rustam Atakishiyev</p>
                        <p style="font-weight: 600;font-size: 12px;line-height: 12px;text-align: center;text-transform: uppercase;color: #848FA3; margin-top: 4px">CTO</p>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3 d-flex justify-content-center">
                <a href="https://www.linkedin.com/in/leyla-khanahmad-93594317b/" target="_blank" style="width: 100%;display: contents;">
                    <div style="width: 60%; margin-bottom: 60px; position: relative;">
                        <div style="padding-bottom: 100%;  position: relative">
                            <img src="/img/landing/leyla_photo_5_11zon.png" alt="profile image" style="object-fit: cover;height: 100%;width: 100%;position: absolute;border-radius: 50%; overflow: hidden;filter: drop-shadow(0px 3px 4px rgba(0, 0, 0, 0.1));">
                            <div style="position: absolute; width: 33%; height: 33%; bottom: 0; right: 0; overflow: hidden; border-radius: 50%;">
                                <img src="/img/landing/Linkedin.svg"  alt="linkedin icon" width="100%">
                            </div>
                        </div>
                        <p style="font-weight: 600;font-size: 16px;line-height: 16px;text-align: center;color: #202734;margin-top: 16px">Leyla Khanahmad</p>
                        <p style="font-weight: 600;font-size: 12px;line-height: 12px;text-align: center;text-transform: uppercase;color: #848FA3; margin-top: 4px">BDO</p>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3 d-flex justify-content-center">
                <a href="https://www.linkedin.com/in/fkhasiyev/" target="_blank" style="width: 100%;display: contents;">
                    <div style="width: 60%; margin-bottom: 60px; position: relative;">
                        <div style="padding-bottom: 100%;  position: relative">
                            <img src="/img/landing/farid_photo_3_11zon.jpg" alt="profile image" style="object-fit: cover;height: 100%;width: 100%;position: absolute;border-radius: 50%; overflow: hidden;filter: drop-shadow(0px 3px 4px rgba(0, 0, 0, 0.1));">
                            <div style="position: absolute; width: 33%; height: 33%; bottom: 0; right: 0; overflow: hidden; border-radius: 50%;">
                                <img src="/img/landing/Linkedin.svg"  alt="linkedin icon" width="100%">
                            </div>
                        </div>
                        <p style="font-weight: 600;font-size: 16px;line-height: 16px;text-align: center;color: #202734;margin-top: 16px">Farid Khasiyev</p>
                        <p style="font-weight: 600;font-size: 12px;line-height: 12px;text-align: center;text-transform: uppercase;color: #848FA3; margin-top: 4px">Growth Engineer</p>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3 d-flex justify-content-center">
                <a href="https://www.linkedin.com/in/emilhasanli/" target="_blank" style="width: 100%;display: contents;">
                    <div style="width: 60%; margin-bottom: 60px; position: relative;">
                        <div style="padding-bottom: 100%;  position: relative">
                            <img src="/img/landing/emil_photo_2_11zon.png" alt="profile image" style="object-fit: cover;height: 100%;width: 100%;position: absolute;border-radius: 50%; overflow: hidden;filter: drop-shadow(0px 3px 4px rgba(0, 0, 0, 0.1));">
                            <div style="position: absolute; width: 33%; height: 33%; bottom: 0; right: 0; overflow: hidden; border-radius: 50%;">
                                <img src="/img/landing/Linkedin.svg"  alt="linkedin icon" width="100%">
                            </div>
                        </div>
                        <p style="font-weight: 600;font-size: 16px;line-height: 16px;text-align: center;color: #202734;margin-top: 16px">Emil Hasanli</p>
                        <p style="font-weight: 600;font-size: 12px;line-height: 12px;text-align: center;text-transform: uppercase;color: #848FA3; margin-top: 4px">CFO</p>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3 d-flex justify-content-center">
                <a href="https://www.linkedin.com/in/fidan-jafarzadeh-7b0411123/" target="_blank" style="width: 100%;display: contents;">
                    <div style="width: 60%; margin-bottom: 60px; position: relative;">
                        <div style="padding-bottom: 100%;  position: relative">
                            <img src="/img/landing/fidan_photo_4_11zon.png" alt="profile image" style="object-fit: cover;height: 100%;width: 100%;position: absolute;border-radius: 50%; overflow: hidden;filter: drop-shadow(0px 3px 4px rgba(0, 0, 0, 0.1));">
                            <div style="position: absolute; width: 33%; height: 33%; bottom: 0; right: 0; overflow: hidden; border-radius: 50%;">
                                <img src="/img/landing/Linkedin.svg"  alt="linkedin icon" width="100%">
                            </div>
                        </div>
                        <p style="font-weight: 600;font-size: 16px;line-height: 16px;text-align: center;color: #202734;margin-top: 16px">Fidan Jafarzada</p>
                        <p style="font-weight: 600;font-size: 12px;line-height: 12px;text-align: center;text-transform: uppercase;color: #848FA3; margin-top: 4px">Operations Manager</p>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3 d-flex justify-content-center">
                <a href="https://www.linkedin.com/in/sabuhi-ibrahimov-180b021b7" target="_blank" style="width: 100%;display: contents;">
                    <div style="width: 60%; margin-bottom: 60px; position: relative;">
                        <div style="padding-bottom: 100%;  position: relative">
                            <img src="/img/landing/sabuhi_photo_7_11zon.jpg" alt="profile image" style="object-fit: cover;height: 100%;width: 100%;position: absolute;border-radius: 50%; overflow: hidden;filter: drop-shadow(0px 3px 4px rgba(0, 0, 0, 0.1));">
                            <div style="position: absolute; width: 33%; height: 33%; bottom: 0; right: 0; overflow: hidden; border-radius: 50%;">
                                <img src="/img/landing/Linkedin.svg"  alt="linkedin icon" width="100%">
                            </div>
                        </div>
                        <p style="font-weight: 600;font-size: 16px;line-height: 16px;text-align: center;color: #202734;margin-top: 16px">Sabuhi Ibrahimov</p>
                        <p style="font-weight: 600;font-size: 12px;line-height: 12px;text-align: center;text-transform: uppercase;color: #848FA3; margin-top: 4px">Front End Developer</p>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3 d-flex justify-content-center">
                <a href="https://www.linkedin.com/in/jala-abubakirova-1942511a3/" target="_blank" style="width: 100%;display: contents;">
                    <div style="width: 60%; margin-bottom: 60px; position: relative;">
                        <div style="padding-bottom: 100%;  position: relative">
                            <img src="/img/landing/jale_photo.png" alt="profile image" style="object-fit: cover;height: 100%;width: 100%;position: absolute;border-radius: 50%; overflow: hidden;filter: drop-shadow(0px 3px 4px rgba(0, 0, 0, 0.1));">
                            <div style="position: absolute; width: 33%; height: 33%; bottom: 0; right: 0; overflow: hidden; border-radius: 50%;">
                                <img src="/img/landing/Linkedin.svg"  alt="linkedin icon" width="100%">
                            </div>
                        </div>
                        <p style="font-weight: 600;font-size: 16px;line-height: 16px;text-align: center;color: #202734;margin-top: 16px">Jala Abubakirova</p>
                        <p style="font-weight: 600;font-size: 12px;line-height: 12px;text-align: center;text-transform: uppercase;color: #848FA3; margin-top: 4px">HR manager</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>