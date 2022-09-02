<?php

/* @var $this yii\web\View */

$this->title = 'My DillBill Application';

use yii\helpers\Url;

?>

<script src="https://kit.fontawesome.com/53d8e59e16.js" crossorigin="anonymous"></script>

<style>
    .cat-grid {
        width: 100%;
        background-color: #fff;
        border: 1px solid #e3e3e3;
        border-radius: 3px;
        margin-bottom: 15px;
        min-height: 20px;
        padding: 19px;
        text-align: center;
    }
    .cat-grid:hover {
        background-color: #e3e3e3;
    }
    .feature-icon {
        font-size: 50px;
    }
    .cat-grid h3 {
        margin-bottom: 2px;
        margin-top: 2px;
        font-size: 16px;
        font-weight: 500;
    }
    .container {
        max-width: 1550px;
        width: 100% !important;
    }
    .row a:hover {
        text-decoration: none !important;
    }
</style>

<br><br><br>
<div class="container" style="max-width: 1550px; width: 100%">
    <div class="row justify-content-center">
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=user">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fas fa-user-graduate"></i></span>
                    <h3>Users</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=trial-conversation">
                <div class="cat-grid">
                    <span class="feature-icon">
                        <i class="fas fa-user-astronaut"></i>
                    </span>
                    <h3>Trial Conversation</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=conversation">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fa fa-users"></i></span>
                    <h3>Conversation Class</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=conversation-users">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fab fa-bity"></i></span>
                    <h3>Conversation Users</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=feedback">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="far fa-comment-dots"></i></span>
                    <h3>Feedback</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=payment-actions">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fas fa-cash-register"></i></span>
                    <h3>Payment Actions</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=control-panel">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fas fa-gamepad"></i></span>
                    <h3>Control Panel</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=packets">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fab fa-app-store"></i></span>
                    <h3>Packets</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=promo-actions">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fa fa-rocket"></i></span>
                    <h3>Promo Actions</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=translate">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fas fa-language"></i></span>
                    <h3>Translate</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=socket-users">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fab fa-battle-net"></i></span>
                    <h3>Socket Users</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=developer-settings">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fa fa-wrench"></i></span>
                    <h3>Developer Settings</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=teachers">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fab fa-linode"></i></span>
                    <h3>Landing</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=grammar">
                <div class="cat-grid">
                    <span class="feature-icon"><i class="fas fa-spell-check"></i></span>
                    <h3>Grammar</h3>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= Url::base() ?>?r=statistics">
                <div class="cat-grid">
                    <span class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <h3>Statistics</h3>
                </div>
            </a>
        </div>
    </div>
</div>