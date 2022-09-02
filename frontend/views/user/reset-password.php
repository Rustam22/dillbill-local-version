<?php

use yii\helpers\Url;

?>


<div class="teacher-container reset-container" style="height: 70%; margin-top: -50px;">
    <div class="container" style="max-width: 352px; padding: 0 15px;">
        <div style="padding-top: 172px; padding-bottom: 96px">
            <div class="h4-500 grays_900" style="text-align: center">
                Change your password
            </div>
            <form class="needs-validation" action="<?= Url::to(['user/reset-password'], true).'?token='.Yii::$app->request->queryParams['token']; ?>" method="post" novalidate>
                <input type="hidden" name="_csrf-frontend" value="<?= Yii::$app->request->csrfToken ?>">
                <div class="mb-3" style="margin-top: 24px">
                    <div id="show_hide_password" style="position: relative">
                        <input class="form-control form-control-lg"
                               id="InputPassword2"
                               type="password"
                               name="ResetPasswordForm[password]"
                               minlength="4"
                               placeholder="New password"
                               style="border-radius: 8px; padding: 12px 16px; font-size: 1rem; line-height: 24px"
                               required
                        >
                        <div class="input-group-addon" style="position: absolute; right: 25px; top: 12px">
                            <a >
                                <i class="fa-eye-slash cursor-pointer" aria-hidden="true">
                                    <img class="Eye" src="/img/landing/Eye.svg" alt="password eye show">
                                    <img class="Slash" src="/img/landing/Hide.svg" alt="password eye close">
                                </i>
                            </a>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg my-btn-1" style=" border-radius: 8px; margin-top: 0; width: 100%; text-align: center">
                    Change Password
                </button>
            </form>
        </div>
    </div>
</div>



