<?php

use backend\models\Grammar;

$levels = [
        'beginner' =>           ['name' => 'Beginner', 'segment' => 'A1', 'color' => '#FF3838'],
        'elementary' =>         ['name' => 'Elementary', 'segment' => 'A12', 'color' => '#F7B036'],
        'pre-intermediate' =>   ['name' => 'Pre-Intermediate', 'segment' => 'B1.1', 'color' => '#00B67A'],
        'intermediate' =>       ['name' => 'Intermediate', 'segment' => 'B1.2', 'color' => '#1877F2']
];

$userLevel = Yii::$app->user->identity->userParameters->currentLevel;
$grammarLevel = '';


$lock = false;
$mLock = false;
$accessibleLevels = [];

foreach ($levels as $key => $value) {
    array_push($accessibleLevels, $key);
    if($key == $userLevel) {
        break;
    }
}


$materials = [];
if($userLevel != 'empty') {
    $materials = Grammar::find()
                        ->select(['description', 'url', 'level'])
                        ->distinct()
                        ->where(['level' => $accessibleLevels, 'active' => 'yes', 'type' => 'Grammar'])
                        ->orderBy(['orderNumber' => SORT_DESC])
                        ->orderBy(['level' => SORT_ASC])
                        ->asArray()
                        ->all();
    //debug($materials);
} else {
    $materials = Grammar::find()
                        ->select(['description', 'url', 'level'])
                        ->distinct()
                        ->where(['active' => 'yes', 'type' => 'Grammar'])
                        ->orderBy(['orderNumber' => SORT_ASC])
                        ->orderBy(['level' => SORT_ASC])
                        ->asArray()
                        ->all();
}

?>

<link href="<?=Yii::getAlias('@web');?>/css/dashboard/grammar.css" rel="stylesheet">
<script src="<?=Yii::getAlias('@web');?>/js/dashboard/grammar.js"></script>

<style>
    .material {
        display: block;
    }
</style>

<div class="container w-100" style="max-width: 944px;">
    <br>
    <h2><?= Yii::$app->devSet->getTranslate('grammarLearningMaterials') ?></h2>
    <p class="header-p"><span style="color: #FF3838;">*</span> <?= Yii::$app->devSet->getTranslate('youCanViewOrDownloadGrammar') ?></p>
    <br>

    <div class="selectCustom position-relative display-none">
        <div class="selectCustom-trigger inter-18 border-radius-10 outline-inactive cursor-pointer">
            <span class="selected-text">
                <?php if($userLevel == 'empty') { ?>
                    <?php foreach ($levels as $key => $value) { ?>
                        <?= $value['name'] ?> - <?= $value['segment'] ?>
                        <?php break; ?>
                    <?php } ?>
                <?php } ?>

                <?php if($userLevel != 'empty') { ?>
                    <?= $levels[$userLevel]['name'] ?> - <?= $levels[$userLevel]['segment'] ?>
                <?php } ?>
            </span>
        </div>
        <div class="selectCustom-options display-none border-radius-10 outline-inactive cursor-pointer">
            <?php foreach ($levels as $key => $value) { ?>
                <?php if(($userLevel == 'empty')) { ?>
                    <div class="selectCustom-option" data-level="<?= $key ?>" style="border-bottom: 1px solid <?= $value['color'] ?>">
                        <?= $value['name'] ?> - <?= $value['segment'] ?>
                    </div>
                <?php $lock = true; } ?>

                <?php if(($key != $userLevel) and ($userLevel != 'empty') and ($mLock != true)) { ?>
                    <div class="selectCustom-option" data-level="<?= $key ?>" style="border-bottom: 1px solid <?= $value['color'] ?>">
                        <?= $value['name'] ?> - <?= $value['segment'] ?>
                    </div>
                <?php } ?>

                <?php if(($key == $userLevel) and ($userLevel != 'empty') and ($mLock != true)) { $mLock = true; ?>
                    <div class="selectCustom-option" data-level="<?= $key ?>" style="border-bottom: 1px solid <?= $value['color'] ?>">
                        <?= $value['name'] ?> - <?= $value['segment'] ?>
                    </div>
                <?php } ?>

                <?php if(($key != $userLevel) and ($userLevel != 'empty') and ($mLock == true)) { ?>
                    <div class="selectCustom-option disabled" data-level="<?= $key ?>" style="border-bottom: 1px solid <?= $value['color'] ?>">
                        <?= $value['name'] ?> - <?= $value['segment'] ?>&nbsp;
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;">
                            <path d="M3.33331 14.6667H12.6666C13.1971 14.6667 13.7058 14.456 14.0809 14.0809C14.4559 13.7058 14.6666 13.1971 14.6666 12.6667V6.66669C14.6666 6.13625 14.4559 5.62755 14.0809 5.25247C13.7058 4.8774 13.1971 4.66669 12.6666 4.66669H11.3333V4.00002C11.3333 3.11597 10.9821 2.26812 10.357 1.643C9.73188 1.01788 8.88403 0.666687 7.99998 0.666687C7.11592 0.666687 6.26808 1.01788 5.64296 1.643C5.01784 2.26812 4.66665 3.11597 4.66665 4.00002V4.66669H3.33331C2.80288 4.66669 2.29417 4.8774 1.9191 5.25247C1.54403 5.62755 1.33331 6.13625 1.33331 6.66669V12.6667C1.33331 13.1971 1.54403 13.7058 1.9191 14.0809C2.29417 14.456 2.80288 14.6667 3.33331 14.6667ZM5.99998 4.00002C5.99998 3.46959 6.21069 2.96088 6.58577 2.58581C6.96084 2.21073 7.46955 2.00002 7.99998 2.00002C8.53041 2.00002 9.03912 2.21073 9.41419 2.58581C9.78927 2.96088 9.99998 3.46959 9.99998 4.00002V4.66669H5.99998V4.00002ZM2.66665 6.66669C2.66665 6.48988 2.73688 6.32031 2.86191 6.19528C2.98693 6.07026 3.1565 6.00002 3.33331 6.00002H12.6666C12.8435 6.00002 13.013 6.07026 13.1381 6.19528C13.2631 6.32031 13.3333 6.48988 13.3333 6.66669V12.6667C13.3333 12.8435 13.2631 13.0131 13.1381 13.1381C13.013 13.2631 12.8435 13.3334 12.6666 13.3334H3.33331C3.1565 13.3334 2.98693 13.2631 2.86191 13.1381C2.73688 13.0131 2.66665 12.8435 2.66665 12.6667V6.66669ZM6.66665 9.66669C6.66665 9.40298 6.74485 9.14519 6.89135 8.92593C7.03786 8.70666 7.2461 8.53576 7.48974 8.43485C7.73337 8.33393 8.00146 8.30753 8.2601 8.35897C8.51874 8.41042 8.75632 8.53741 8.94279 8.72388C9.12926 8.91035 9.25625 9.14793 9.30769 9.40657C9.35914 9.66521 9.33274 9.9333 9.23182 10.1769C9.1309 10.4206 8.96001 10.6288 8.74074 10.7753C8.52147 10.9218 8.26369 11 7.99998 11C7.64636 11 7.30722 10.8595 7.05717 10.6095C6.80712 10.3594 6.66665 10.0203 6.66665 9.66669Z" fill="#B1B8C1"/>
                        </svg>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

    <div class="t-body w-100 display-flex">
        <?php foreach ($levels as $key => $value) { ?>
            <?php if(($userLevel == 'empty')) { ?>
                <div class="t-head w-100 <?php if($lock == true) { ?> active <?php } ?>" data-level="<?= $key ?>" style="border-top: 1px solid <?= $value['color'] ?>;">
                    <?= $value['name'] ?> - <?= $value['segment'] ?>
                </div>
            <?php $lock = false; } ?>

            <?php if(($key != $userLevel) and ($userLevel != 'empty') and ($lock != true)) { ?>
                <div class="t-head w-100" data-level="<?= $key ?>" style="border-top: 1px solid <?= $value['color'] ?>;">
                    <?= $value['name'] ?> - <?= $value['segment'] ?>
                </div>
            <?php } ?>

            <?php if(($key == $userLevel) and ($userLevel != 'empty') and ($lock != true)) { $lock = true; ?>
                <div class="t-head w-100 active" data-level="<?= $key ?>" style="border-top: 1px solid <?= $value['color'] ?>">
                    <?= $value['name'] ?> - <?= $value['segment'] ?>
                </div>
            <?php } ?>

            <?php if(($key != $userLevel) and ($userLevel != 'empty') and ($lock == true)) { ?>
                <div class="t-head w-100 disabled" data-level="<?= $key ?>" style="border-top: 1px solid <?= $value['color'] ?>">
                    <?= $value['name'] ?> - <?= $value['segment'] ?> &nbsp;
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.3335 14.6666H12.6668C13.1973 14.6666 13.706 14.4559 14.081 14.0808C14.4561 13.7058 14.6668 13.1971 14.6668 12.6666V6.66663C14.6668 6.13619 14.4561 5.62749 14.081 5.25241C13.706 4.87734 13.1973 4.66663 12.6668 4.66663H11.3335V3.99996C11.3335 3.1159 10.9823 2.26806 10.3572 1.64294C9.73206 1.01782 8.88422 0.666626 8.00016 0.666626C7.11611 0.666626 6.26826 1.01782 5.64314 1.64294C5.01802 2.26806 4.66683 3.1159 4.66683 3.99996V4.66663H3.3335C2.80306 4.66663 2.29436 4.87734 1.91928 5.25241C1.54421 5.62749 1.3335 6.13619 1.3335 6.66663V12.6666C1.3335 13.1971 1.54421 13.7058 1.91928 14.0808C2.29436 14.4559 2.80306 14.6666 3.3335 14.6666ZM6.00016 3.99996C6.00016 3.46953 6.21088 2.96082 6.58595 2.58575C6.96102 2.21067 7.46973 1.99996 8.00016 1.99996C8.5306 1.99996 9.0393 2.21067 9.41438 2.58575C9.78945 2.96082 10.0002 3.46953 10.0002 3.99996V4.66663H6.00016V3.99996ZM2.66683 6.66663C2.66683 6.48982 2.73707 6.32025 2.86209 6.19522C2.98712 6.0702 3.15669 5.99996 3.3335 5.99996H12.6668C12.8436 5.99996 13.0132 6.0702 13.1382 6.19522C13.2633 6.32025 13.3335 6.48982 13.3335 6.66663V12.6666C13.3335 12.8434 13.2633 13.013 13.1382 13.138C13.0132 13.2631 12.8436 13.3333 12.6668 13.3333H3.3335C3.15669 13.3333 2.98712 13.2631 2.86209 13.138C2.73707 13.013 2.66683 12.8434 2.66683 12.6666V6.66663ZM6.66683 9.66663C6.66683 9.40292 6.74503 9.14513 6.89154 8.92587C7.03805 8.7066 7.24628 8.5357 7.48992 8.43479C7.73355 8.33387 8.00164 8.30747 8.26028 8.35891C8.51892 8.41036 8.7565 8.53735 8.94297 8.72382C9.12944 8.91029 9.25643 9.14786 9.30788 9.40651C9.35932 9.66515 9.33292 9.93324 9.232 10.1769C9.13109 10.4205 8.96019 10.6287 8.74092 10.7753C8.52166 10.9218 8.26387 11 8.00016 11C7.64654 11 7.3074 10.8595 7.05735 10.6094C6.80731 10.3594 6.66683 10.0202 6.66683 9.66663Z" fill="#B1B8C1"/>
                    </svg>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <?php if($userLevel != 'empty') { ?>
        <?php foreach ($materials as $key => $value) { ?>
            <a class="material"
                <?php if($value['level'] != $userLevel) { ?> style="display: none;" <?php } ?>
               data-material-level="<?= $value['level'] ?>"
               href="<?= $value['url'] ?>" target="_blank">
                <div class="row-material w-100 display-flex">
                    <div style="margin-right: 10px;">
                        <span><?= $value['description'] ?></span>
                    </div>
                    <div>
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="32" height="32" rx="8" fill="#D2E4FF"/>
                            <path d="M10.6667 21.3334H21.3333C21.5101 21.3334 21.6797 21.4036 21.8047 21.5286C21.9298 21.6537 22 21.8232 22 22C22 22.1769 21.9298 22.3464 21.8047 22.4714C21.6797 22.5965 21.5101 22.6667 21.3333 22.6667H10.6667C10.4899 22.6667 10.3203 22.5965 10.1953 22.4714C10.0702 22.3464 10 22.1769 10 22C10 21.8232 10.0702 21.6537 10.1953 21.5286C10.3203 21.4036 10.4899 21.3334 10.6667 21.3334ZM16 9.33337C15.8232 9.33337 15.6536 9.40361 15.5286 9.52864C15.4036 9.65366 15.3333 9.82323 15.3333 10V17.724L13.8047 16.1954C13.7432 16.1317 13.6696 16.0809 13.5883 16.046C13.5069 16.011 13.4195 15.9926 13.3309 15.9919C13.2424 15.9911 13.1546 16.008 13.0727 16.0415C12.9908 16.075 12.9163 16.1245 12.8537 16.1871C12.7911 16.2497 12.7416 16.3241 12.7081 16.4061C12.6746 16.488 12.6577 16.5758 12.6585 16.6643C12.6593 16.7528 12.6777 16.8403 12.7126 16.9216C12.7475 17.003 12.7983 17.0765 12.862 17.138L15.5287 19.8047C15.5907 19.8665 15.6644 19.9154 15.7453 19.9487C15.826 19.9826 15.9125 20 16 20C16.0875 20 16.174 19.9826 16.2547 19.9487C16.3356 19.9154 16.4093 19.8665 16.4713 19.8047L19.138 17.138C19.2594 17.0123 19.3266 16.8439 19.3251 16.6691C19.3236 16.4943 19.2535 16.3271 19.1299 16.2035C19.0063 16.0799 18.8391 16.0098 18.6643 16.0083C18.4895 16.0067 18.3211 16.0739 18.1953 16.1954L16.6667 17.724V10C16.6667 9.82323 16.5964 9.65366 16.4714 9.52864C16.3464 9.40361 16.1768 9.33337 16 9.33337Z" fill="#1877F2"/>
                        </svg>
                    </div>
                </div>
            </a>
        <?php } ?>
    <?php } ?>

    <?php if($userLevel == 'empty') { $counter = 1; $currentLevel = 'beginner'; ?>
        <?php foreach ($materials as $key => $value) { ?>
            <?php if($currentLevel != $value['level']) { $currentLevel = $value['level']; $counter = 1; } ?>
            <a class="material" <?php if($value['level'] != 'beginner') { ?> style="display: none;" <?php } ?>
               data-material-level="<?= $value['level'] ?>"
               <?php if($counter <= 3) { ?> href="<?= $value['url'] ?>" <?php } ?> <?php if($counter > 3) { ?> style="text-decoration: none;" <?php } ?>
                    target="_blank"
            >
                <div class="row-material w-100 display-flex">
                    <div style="margin-right: 10px;">
                        <span><?= $value['description'] ?></span>
                    </div>
                    <div class="display-flex">
                        <?php if($counter <= 3) { ?>
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="32" height="32" rx="8" fill="#D2E4FF"/>
                                <path d="M10.6667 21.3334H21.3333C21.5101 21.3334 21.6797 21.4036 21.8047 21.5286C21.9298 21.6537 22 21.8232 22 22C22 22.1769 21.9298 22.3464 21.8047 22.4714C21.6797 22.5965 21.5101 22.6667 21.3333 22.6667H10.6667C10.4899 22.6667 10.3203 22.5965 10.1953 22.4714C10.0702 22.3464 10 22.1769 10 22C10 21.8232 10.0702 21.6537 10.1953 21.5286C10.3203 21.4036 10.4899 21.3334 10.6667 21.3334ZM16 9.33337C15.8232 9.33337 15.6536 9.40361 15.5286 9.52864C15.4036 9.65366 15.3333 9.82323 15.3333 10V17.724L13.8047 16.1954C13.7432 16.1317 13.6696 16.0809 13.5883 16.046C13.5069 16.011 13.4195 15.9926 13.3309 15.9919C13.2424 15.9911 13.1546 16.008 13.0727 16.0415C12.9908 16.075 12.9163 16.1245 12.8537 16.1871C12.7911 16.2497 12.7416 16.3241 12.7081 16.4061C12.6746 16.488 12.6577 16.5758 12.6585 16.6643C12.6593 16.7528 12.6777 16.8403 12.7126 16.9216C12.7475 17.003 12.7983 17.0765 12.862 17.138L15.5287 19.8047C15.5907 19.8665 15.6644 19.9154 15.7453 19.9487C15.826 19.9826 15.9125 20 16 20C16.0875 20 16.174 19.9826 16.2547 19.9487C16.3356 19.9154 16.4093 19.8665 16.4713 19.8047L19.138 17.138C19.2594 17.0123 19.3266 16.8439 19.3251 16.6691C19.3236 16.4943 19.2535 16.3271 19.1299 16.2035C19.0063 16.0799 18.8391 16.0098 18.6643 16.0083C18.4895 16.0067 18.3211 16.0739 18.1953 16.1954L16.6667 17.724V10C16.6667 9.82323 16.5964 9.65366 16.4714 9.52864C16.3464 9.40361 16.1768 9.33337 16 9.33337Z" fill="#1877F2"/>
                            </svg>
                        <?php } ?>
                        <?php if($counter > 3) { ?>
                            <button class="btn subscribe-to-download disabled">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00015 3.33496C8.25204 3.33496 8.4823 3.47727 8.59495 3.70256L10.6309 7.77444L13.3694 6.40517C13.6053 6.28723 13.8886 6.31982 14.0915 6.48824C14.2944 6.65666 14.3787 6.92908 14.3062 7.18265L12.3062 14.1827C12.2247 14.4681 11.9637 14.665 11.6668 14.665H4.33349C4.03658 14.665 3.77564 14.4681 3.69407 14.1827L1.69407 7.18265C1.62162 6.92908 1.70586 6.65666 1.90879 6.48824C2.11172 6.31982 2.39501 6.28723 2.63088 6.40517L5.36942 7.77444L7.40536 3.70256C7.518 3.47727 7.74827 3.33496 8.00015 3.33496ZM8.00015 5.48695L6.26161 8.96402C6.09736 9.29252 5.69792 9.42567 5.36942 9.26142L3.38819 8.27081L4.8351 13.335H11.1652L12.6121 8.27081L10.6309 9.26142C10.3024 9.42567 9.90294 9.29252 9.73869 8.96402L8.00015 5.48695Z" fill="#FBBC04"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.33346 5.66496C2.14844 5.66496 1.99846 5.81495 1.99846 5.99996C1.99846 6.18498 2.14844 6.33496 2.33346 6.33496C2.51847 6.33496 2.66846 6.18498 2.66846 5.99996C2.66846 5.81495 2.51847 5.66496 2.33346 5.66496ZM0.668457 5.99996C0.668457 5.08041 1.4139 4.33496 2.33346 4.33496C3.25301 4.33496 3.99846 5.08041 3.99846 5.99996C3.99846 6.91952 3.25301 7.66496 2.33346 7.66496C1.4139 7.66496 0.668457 6.91952 0.668457 5.99996Z" fill="#FBBC04"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.99996 2.66496C7.81495 2.66496 7.66496 2.81495 7.66496 2.99996C7.66496 3.18498 7.81495 3.33496 7.99996 3.33496C8.18498 3.33496 8.33496 3.18498 8.33496 2.99996C8.33496 2.81495 8.18498 2.66496 7.99996 2.66496ZM6.33496 2.99996C6.33496 2.08041 7.08041 1.33496 7.99996 1.33496C8.91952 1.33496 9.66496 2.08041 9.66496 2.99996C9.66496 3.91952 8.91952 4.66496 7.99996 4.66496C7.08041 4.66496 6.33496 3.91952 6.33496 2.99996Z" fill="#FBBC04"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.6665 5.66496C13.4814 5.66496 13.3315 5.81495 13.3315 5.99996C13.3315 6.18498 13.4814 6.33496 13.6665 6.33496C13.8515 6.33496 14.0015 6.18498 14.0015 5.99996C14.0015 5.81495 13.8515 5.66496 13.6665 5.66496ZM12.0015 5.99996C12.0015 5.08041 12.7469 4.33496 13.6665 4.33496C14.586 4.33496 15.3315 5.08041 15.3315 5.99996C15.3315 6.91952 14.586 7.66496 13.6665 7.66496C12.7469 7.66496 12.0015 6.91952 12.0015 5.99996Z" fill="#FBBC04"/>
                                </svg>
                                &nbsp;<?= Yii::$app->devSet->getTranslate('subscribeToUnlock') ?>
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </a>
        <?php $counter++; } ?>
    <?php } ?>

    <br><br><br>
</div>