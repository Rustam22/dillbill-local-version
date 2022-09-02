<?php

use backend\models\ConversationUsers;
use yii\helpers\Url;

$classes = ConversationUsers::find()->
                            where(['userId' => Yii::$app->user->id, 'action' => 'reserve'])->
                            orderBy(['conversationDate' => SORT_DESC])->
                            andWhere(['>=', 'conversationDate', '2021-12-27'])->
                            all();
$index = sizeof($classes);
$counter = -1;

?>

<style>
    tbody, td, tfoot, th, thead, tr {
        border-style: none;
    }
    .class-history thead th {
        font-weight: 500;
        font-size: 10px;
        color: #646E82;
    }
    .class-history thead th svg {
        margin-top: -3px;
        margin-right: 5px;
    }
    .class-history {
        min-width: 800px;
    }

    thead {
        height: 48px;
        background: #F5F5F5;
    }
    thead th {
        vertical-align: middle;
    }
    thead tr, tbody tr {
        border-bottom: 1px solid #E6E8F0;
    }

    tbody tr {
        height: 64px;
        vertical-align: middle;
    }
    .table-striped > tbody > tr:nth-of-type(2n+1) {
        background: #FFFFFF;
        --bs-table-accent-bg: none;
    }
    .table-striped > tbody > tr:nth-of-type(2n) {
        background: #FFFFFF;
        --bs-table-accent-bg: none;
    }

    tbody tr th {
        min-width: 27px;
        text-align: right;
    }
    tbody tr th span {
        color: #101840;
        font-size: 15px;
        font-weight: 300;
    }
    tbody tr td span {
        color: #101840;
        font-size: 15px;
        font-weight: 500;
    }

    tbody tr td:nth-of-type(1) img {
        border-radius: 50%;
        margin-right: 8px;
        margin-left: 5px;
    }
    tbody tr td:nth-of-type(1) span {
        font-weight: 600;
    }
    tbody tr td:nth-of-type(5) a {
        color: #3366FF;
        font-weight: 500;
        font-size: 15px;
    }
</style>

<div class="container" style="max-width: 944px;margin-top: 24px;">
    <div class="row">
        <div class="col-12">
            <table class="table table-striped class-history">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 0H0V12H12V0Z" fill="white" fill-opacity="0.01"/>
                                <path d="M6 5.5C7.24264 5.5 8.25 4.49264 8.25 3.25C8.25 2.00736 7.24264 1 6 1C4.75736 1 3.75 2.00736 3.75 3.25C3.75 4.49264 4.75736 5.5 6 5.5Z" stroke="#646E82" stroke-linejoin="round"/>
                                <path d="M1.25 11C1.25 8.89062 2.79375 6.92188 4.1 6.5C4.1 6.5 5.2875 7.76562 6 8.60938L7.9 6.5C8.96875 6.64062 10.75 8.89062 10.75 11" stroke="#646E82" stroke-linejoin="round"/>
                                <path d="M0.5 11H11.5" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            TUTOR
                        </th>
                        <th scope="col">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.25 1.75H4C5.10458 1.75 6 2.64542 6 3.75V10.5C6 9.67158 5.32842 9 4.5 9H1.25V1.75Z" stroke="#646E82" stroke-linejoin="round"/>
                                <path d="M10.75 1.75H8C6.89542 1.75 6 2.64542 6 3.75V10.5C6 9.67158 6.67158 9 7.5 9H10.75V1.75Z" stroke="#646E82" stroke-linejoin="round"/>
                            </svg>
                            TOPIC
                        </th>
                        <th scope="col">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 0H0V12H12V0Z" fill="white" fill-opacity="0.01"/>
                                <path d="M1.25 4.75H10.75V10C10.75 10.2761 10.5261 10.5 10.25 10.5H1.75C1.47386 10.5 1.25 10.2761 1.25 10V4.75Z" stroke="#646E82" stroke-linejoin="round"/>
                                <path d="M1.25 2.25C1.25 1.97386 1.47386 1.75 1.75 1.75H10.25C10.5261 1.75 10.75 1.97386 10.75 2.25V4.75H1.25V2.25Z" stroke="#646E82" stroke-linejoin="round"/>
                                <path d="M4 1V3" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 1V3" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 8.5H8.5" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.5 8.5H5" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 6.5H8.5" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.5 6.5H5" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            DATE
                        </th>
                        <th scope="col">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 0H0V12H12V0Z" fill="white" fill-opacity="0.01"/>
                                <path d="M6 11C8.76143 11 11 8.76143 11 6C11 3.23857 8.76143 1 6 1C3.23857 1 1 3.23857 1 6C1 8.76143 3.23857 11 6 11Z" stroke="#646E82" stroke-linejoin="round"/>
                                <path d="M6.00225 3L6.00195 6.0022L8.1218 8.12205" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            TIME
                        </th>
                        <th scope="col">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 0H0V12H12V0Z" fill="white" fill-opacity="0.01"/>
                                <path d="M10.1291 8.57903C10.9507 8.00125 11.303 6.95755 10.9997 6C10.6963 5.04245 9.76726 4.51785 8.76281 4.51863H8.18256C7.80323 3.04033 6.55168 1.94893 5.03551 1.7743C3.51931 1.59967 2.05244 2.37795 1.34696 3.73133C0.641486 5.0847 0.843496 6.73293 1.85492 7.87588" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.0021 10.25L6 5.75" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7.59118 8.65894L6.00018 10.2499L4.40918 8.65894" stroke="#646E82" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            SLIDES
                        </th>
                    </tr>
                </thead>

                <?php if (!empty($classes)) { ?>
                    <tbody>
                    <?php foreach ($classes as $class) { $counter++; ?>
                        <?php $topicByDate = Yii::$app->devSet->todayTopic(Yii::$app->user->identity->userParameters->currentLevel, $class->conversationDate); ?>
                        <tr>
                            <th scope="row">
                                <span><?= ($index - $counter) ?></span>
                            </th>
                            <td>
                                <?php try { ?>
                                    <img width="32" src="<?= Yii::$app->request->hostInfo ?>/backend/web/<?= $class->conversation->teacher->image ?>" alt="<?= $class->conversation->teacher->teacherName ?>">
                                    <span><?= $class->conversation->teacher->teacherName ?></span>
                                <?php } catch (Exception $exception) { ?>
                                    <img width="32" src="" alt="">
                                    <span>...</span>
                                <?php } ?>
                            </td>
                            <td>
                                <span><?= $topicByDate['description'] ?></span>
                            </td>
                            <td>
                                <?php $classDateTime = new DateTime($class->conversationDate.' '.$class->startsAT); ?>
                                <?php $adjustedClassDateTime = $classAlignedDate = Yii::$app->devSet->getAlignedDateTimeByUserTimeZone($classDateTime, Yii::$app->user->identity->userProfile->timezone); ?>
                                <span><?= $adjustedClassDateTime->format('d.m.Y') ?></span>
                            </td>
                            <td>
                                <span><?= $adjustedClassDateTime->format('H:i') ?>-<?= $adjustedClassDateTime->modify('+1 hour')->format('H:i') ?></span>
                            </td>
                            <td>
                                <a href="<?= $topicByDate['url'] ?>" target="_blank">Download</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                <?php } ?>
            </table>
        </div>
        <?php if (empty($classes)) { ?>
            <div class="col-12" align="center">
                <br>
                <div style="margin-top: 10px;"></div>
                <svg width="191" height="133" viewBox="0 0 191 133" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M155.986 85.2816C155.348 84.6317 154.577 84.1269 153.726 83.8016C152.578 83.3648 151.354 83.1608 150.126 83.2016C148.926 83.1964 147.729 83.3442 146.566 83.6416C145.494 83.9313 144.46 84.3476 143.486 84.8816L144.726 88.2216C145.496 87.7944 146.315 87.4652 147.166 87.2416C147.959 87.01 148.78 86.8888 149.606 86.8816C150.497 86.8104 151.38 87.0904 152.066 87.6616C152.622 88.1746 152.922 88.9066 152.886 89.6616C152.899 90.2149 152.761 90.7612 152.486 91.2416C152.21 91.7576 151.867 92.235 151.466 92.6616C151.046 93.1216 150.606 93.5616 150.146 94.0216C149.669 94.486 149.221 94.9804 148.806 95.5016C148.387 96.024 148.043 96.6031 147.786 97.2216C147.514 97.9018 147.378 98.629 147.386 99.3616C147.367 99.6146 147.367 99.8687 147.386 100.122C147.386 100.402 147.386 100.662 147.486 100.902H151.286C151.276 100.755 151.276 100.608 151.286 100.462V100.042C151.263 99.3463 151.422 98.657 151.746 98.0416C152.056 97.4495 152.439 96.8985 152.886 96.4016C153.346 95.8816 153.846 95.3816 154.386 94.8816C154.917 94.3917 155.418 93.8708 155.886 93.3216C156.34 92.7716 156.723 92.1669 157.026 91.5216C157.338 90.8304 157.495 90.0797 157.486 89.3216C157.466 88.644 157.359 87.9718 157.166 87.3216C156.938 86.5585 156.534 85.8597 155.986 85.2816V85.2816Z" fill="#CBD1D3"/>
                    <path d="M149.526 103.481C148.782 103.481 148.065 103.768 147.526 104.281C146.987 104.807 146.684 105.529 146.686 106.281C146.669 107.037 146.975 107.764 147.526 108.281C148.065 108.795 148.782 109.081 149.526 109.081C150.272 109.086 150.989 108.799 151.526 108.281C152.078 107.764 152.383 107.037 152.366 106.281C152.369 105.529 152.065 104.807 151.526 104.281C150.989 103.764 150.272 103.477 149.526 103.481V103.481Z" fill="#CBD1D3"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M175.286 110.882L188.506 122.602C189.583 123.643 190.2 125.072 190.219 126.571C190.237 128.069 189.657 129.513 188.606 130.582L188.006 131.182C186.95 132.252 185.51 132.854 184.006 132.854C182.503 132.854 181.062 132.252 180.006 131.182L166.966 119.622L165.846 118.622L163.666 116.622C162.248 117.541 160.732 118.299 159.146 118.882C156.383 119.913 153.456 120.442 150.506 120.442C144.82 120.445 139.308 118.481 134.906 114.882C132.518 112.939 130.516 110.565 129.006 107.882C128.269 106.609 127.653 105.27 127.166 103.882C124.15 95.2627 126.143 85.6823 132.346 78.9816C133.243 77.9884 134.227 77.0781 135.286 76.2616C144.162 69.3073 156.619 69.2403 165.569 76.0987C174.519 82.9572 177.693 95.0029 173.286 105.382C172.923 106.291 172.496 107.173 172.006 108.022L173.286 109.162L175.286 110.882ZM133.726 107.882C135.998 110.992 139.084 113.415 142.646 114.882C147.68 116.962 153.332 116.962 158.366 114.882C166.168 111.694 171.271 104.109 171.286 95.6816C171.289 92.6353 170.605 89.6275 169.286 86.8816C167.478 83.0688 164.547 79.9004 160.886 77.8016C159.512 76.9599 158.042 76.2883 156.506 75.8016C154.564 75.1942 152.541 74.884 150.506 74.8816C145.809 74.8867 141.255 76.5034 137.606 79.4616C136.551 80.2839 135.585 81.2158 134.726 82.2416C129.595 88.2398 128.329 96.6378 131.466 103.882C132.065 105.297 132.823 106.639 133.726 107.882ZM172.066 118.721L170.066 117.001L169.586 116.581L166.866 114.161C167.866 113.282 168.79 112.318 169.626 111.281L172.086 113.481L172.806 114.121L174.666 115.761L172.066 118.721ZM185.066 128.262L185.666 127.662C185.974 127.379 186.156 126.986 186.171 126.568C186.186 126.151 186.033 125.745 185.746 125.442L177.746 118.422L175.066 121.362L182.846 128.262C183.14 128.558 183.539 128.726 183.956 128.726C184.374 128.726 184.773 128.558 185.066 128.262Z" fill="#CBD1D3"/>
                    <path d="M150 40V14.8C150 10.3196 150 8.07937 149.128 6.36808C148.361 4.86278 147.137 3.63893 145.632 2.87195C143.921 2 141.68 2 137.2 2H14.8C10.3196 2 8.07937 2 6.36808 2.87195C4.86278 3.63893 3.63893 4.86278 2.87195 6.36808C2 8.07937 2 10.3196 2 14.8V79.2C2 83.6804 2 85.9206 2.87195 87.6319C3.63893 89.1372 4.86278 90.3611 6.36808 91.1281C8.07937 92 10.3196 92 14.8 92H106" stroke="#CBD1D3" stroke-width="4" stroke-linecap="round"/>
                    <circle cx="19" cy="20" r="4" fill="#CBD1D3"/>
                    <rect x="31" y="16" width="30" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="67" y="16" width="21" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="92" y="16" width="22" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="121" y="16" width="15" height="8" rx="2" fill="#CBD1D3"/>
                    <circle cx="19" cy="39" r="4" fill="#CBD1D3"/>
                    <circle cx="19" cy="58" r="4" fill="#CBD1D3"/>
                    <rect x="31" y="35" width="38" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="31" y="54" width="34" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="73" y="35" width="15" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="73" y="54" width="15" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="92" y="35" width="22" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="92" y="54" width="22" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="121" y="35" width="15" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="121" y="54" width="15" height="8" rx="2" fill="#CBD1D3"/>
                    <rect x="54" y="73" width="44" height="11" rx="2" fill="#CBD1D3"/>
                </svg>
                <br>
                <h5 style="color: #757575;font-weight: 600;font-size: 16px;">
                    <?= Yii::$app->devSet->getTranslate('noClassHistory') ?>
                </h5>
                <p style="color: #757575;font-weight: 400;font-size: 14px;line-height: 15px;">
                    <?= Yii::$app->devSet->getTranslate('goToAvailableClasses') ?>
                </p>
                <a href="<?= Url::to(['dashboard/my-classes'], true) ?>">
                    <button class="btn" style="color: #FFFFFF;font-weight: 500;font-size: 14px;background: #1877F2;border-radius: 4px;margin-top: 5px;">
                        <?= Yii::$app->devSet->getTranslate('goToAvailableClassesButton') ?>
                    </button>
                </a>
            </div>
        <?php } ?>
    </div>
</div>



<br><br><br><br>



