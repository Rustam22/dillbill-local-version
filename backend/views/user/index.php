<?php

use common\models\UserParameters;
use richardfan\widget\JSRegister;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use hoaaah\ajaxcrud\CrudAsset;
use hoaaah\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
$dataProvider->pagination->pageSize = 100;

/*$users = User::find()->all();
ini_set('memory_limit', '1024M');

foreach ($users as $user) {
    $userProfile = new UserProfile();
    $userParameters = new UserParameters();

    $userProfile->userId = $user->id;
    $userProfile->name = $user->name;
    $userProfile->surname = $user->surname;
    $userProfile->color = $user->color;
    $userProfile->timezone = $user->userTimeZone;

    $userParameters->userId = $user->id;
    $userParameters->confirmed = $user->confirmed;
    $userParameters->availability = $user->availability;
    $userParameters->availabilityLCD  = $user->availabilityLCD;
    $userParameters->proficiency = ($user->proficiency != null) ? (($user->proficiency == 'yes') ? 'no' : $user->proficiency) : 'no';
    $userParameters->startDate = $user->startDate;
    $userParameters->currentLevel = ($user->currentLevel != null) ? $user->currentLevel : 'empty';
    $userParameters->currentPacketId = $user->currentPacketId;
    $userParameters->promoCode = $user->promoCode;
    $userParameters->cp = $user->cp;
    $userParameters->cpBalance = $user->cpBalance;
    $userParameters->lpd = $user->lpd;
    $userParameters->googleCalendar = $user->googleCalendar;
    $userParameters->calendarGmail = $user->calendarGmail;

    $userProfile->save(false);
    $userParameters->save(false);
}*/


/*SELECT `id`, `email`, `confirmed`, `currentLevel`, `currentSchedule`, `availability`, `lpd`,  (`cpBalance` - DATEDIFF(NOW(), `lpd`)) AS `balance`
FROM `user`
WHERE (`cp` > 0)
ORDER BY `currentLevel`, `currentSchedule`, `availability`, `balance`;*/

const EXCLUDED_USERS = [
    'rustam.atakisiev@gmail.com',
    'emil.hasanli.91@gmail.com',
    'sanan@dillbill.net',
    'khasiyev.farid@gmail.com',
    'fidan.jafarzadeh@gmail.com',
    'jalyaabbakirova@gmail.com'
];

if (!Yii::$app->devSet->isLocal()) {
    $activeUsers = UserParameters::find()->
    select(['`user`.`id`, `user`.`email`', '`userParameters`.`confirmed`', '`userParameters`.`confirmed`', '`userParameters`.`currentLevel`',
        '`userParameters`.`currentSchedule`', '`userParameters`.`availability`', '`userParameters`.`lpd`',
        '`userParameters`.`cp`', '(`userParameters`.`cpBalance` - DATEDIFF(NOW(), `userParameters`.`lpd`)) AS balance'])->
    innerJoin('user', '`userParameters`.`userId` = `user`.`id`')->
    andWhere(['>', '`userParameters`.`cp`', 0])->
    andWhere(['>', '`userParameters`.`lpd`', 0])->
    andWhere(['!=', '`userParameters`.`currentLevel`', ''])->
    andWhere(['not in', '`user`.`email`', EXCLUDED_USERS])->
    andWhere('NOW() >= `userParameters`.`startDate`')->
    having(['>=', 'balance', 0])->
    orderBy(['`userParameters`.`currentLevel`' => SORT_ASC, '`userParameters`.`currentSchedule`' => SORT_ASC, '`userParameters`.availability' => SORT_ASC])->
    asArray()->
    all();

    //debug($activeUsers);

    foreach ($activeUsers as $activeUser) {
        Yii::$app->devSet->segmentData(
            $activeUser['id'],
            [
                "email" => $activeUser['email'],
                "confirmed" => $activeUser['confirmed'],
                "Current Level" => $activeUser['currentLevel'],
                "Current Schedule" => $activeUser['currentSchedule'],
                "Cp" => $activeUser['cp'],
                "Lpd at" => $activeUser['lpd'],
                "Cp Balance" => ($activeUser['balance'] < 0) ? 0 : $activeUser['balance'],
                "Current Time Range" => ($activeUser['availability'] == null) ? 'null' : $activeUser['availability'],
                "action" => 'User Table',
                "server" => (Yii::$app->devSet->isLocal()) ? 'local' : 'global'
            ]
        );
    }
}

$dateDifference = (Yii::$app->getRequest()->getQueryParam('lastDay') != null) ? Yii::$app->getRequest()->getQueryParam('lastDay') : 100;

$initialDate = new DateTime();
$initialDate->modify("-".$dateDifference." day");
$xValues = '[';
$beginnerValues = '[';
$elementaryValues = '[';
$preIntermediateValues = '[';
$intermediateValues = '[';
$upperIntermediateValues = '[';
$advancedValues = '[';

$total = '[';

for ($day = $dateDifference; $day >= 0; $day--) {
    $xValues .= "'".$initialDate->format('Y-m-d')."'".', ';

    $activeUsersByLevel = UserParameters::find()->
    select(['`userParameters`.`currentLevel`', 'COUNT(`userParameters`.`currentLevel`) AS c'])->
    innerJoin('user', '`userParameters`.`userId` = `user`.`id`')->
    rightJoin('paymentActions', '`userParameters`.`userId` = `paymentActions`.`userId`')->
    where('DATEDIFF((NOW() - INTERVAL '.$day.' DAY), `paymentActions`.`dateTime`) > 0')->
    andWhere(['>', '`userParameters`.`cp`', 0])->
    andWhere(['>', '`userParameters`.`lpd`', 0])->
    andWhere(['not in', '`user`.`email`', EXCLUDED_USERS])->
    andWhere(['<>', '`userParameters`.`currentLevel`', 'empty'])->
    andWhere('((`userParameters`.`cpBalance` - DATEDIFF((NOW() - INTERVAL '.$day.' DAY), `paymentActions`.`dateTime`)) > 0)')->
    groupBy(['`userParameters`.`currentLevel`'])->
    orderBy(['`userParameters`.`currentLevel`' => SORT_ASC])->
    asArray()->
    all();

    //debug($activeUsersByLevel);

    $beginner = (empty($activeUsersByLevel[0]['c'])) ? 0 : $activeUsersByLevel[0]['c'];
    $elementary = (empty($activeUsersByLevel[1]['c'])) ? 0 : $activeUsersByLevel[1]['c'];
    $preIntermediate = (empty($activeUsersByLevel[2]['c'])) ? 0 : $activeUsersByLevel[2]['c'];
    $intermediate = (empty($activeUsersByLevel[3]['c'])) ? 0 : $activeUsersByLevel[3]['c'];
    $upperIntermediate = (empty($activeUsersByLevel[4]['c'])) ? 0 : $activeUsersByLevel[4]['c'];
    $advanced = (empty($activeUsersByLevel[5]['c'])) ? 0 : $activeUsersByLevel[5]['c'];

    $beginnerValues .= "'".$beginner."', ";
    $elementaryValues .= "'".$elementary."', ";
    $preIntermediateValues .= "'".$preIntermediate."', ";
    $intermediateValues .= "'".$intermediate."', ";
    $upperIntermediateValues .= "'".$upperIntermediate."', ";
    $advancedValues .= "'".$advanced."', ";

    $total .= "'" . ($beginner + $elementary + $preIntermediate + $intermediate + $upperIntermediate + $advanced) ."', ";

    $initialDate->modify('+1 day');
}

$xValues .= ']';
$beginnerValues .= ']';
$elementaryValues .= ']';
$preIntermediateValues .= ']';
$intermediateValues .= ']';
$upperIntermediateValues .= ']';
$advancedValues .= ']';
$total .= ']';

//debug($xValues);
//debug($beginnerValues);

/*$activeUsersByLevelCurrent = UserParameters::find()->
select(['`userParameters`.`currentLevel`', 'COUNT(`userParameters`.`currentLevel`) AS c'])->
innerJoin('user', '`userParameters`.`userId` = `user`.`id`')->
where('DATEDIFF(NOW(), `userParameters`.`lpd`) > 0')->
andWhere(['>', '`userParameters`.`cp`', 0])->
andWhere(['>', '`userParameters`.`lpd`', 0])->
andWhere(['not in', '`user`.`email`', EXCLUDED_USERS])->
andWhere(['<>', '`userParameters`.`currentLevel`', 'empty'])->
andWhere('((`userParameters`.`cpBalance` - DATEDIFF(NOW(), `userParameters`.`lpd`)) > 0)')->
groupBy(['`userParameters`.`currentLevel`'])->
orderBy(['`userParameters`.`currentLevel`' => SORT_ASC])->
asArray()->
all();

//debug($activeUsersByLevelCurrent);

$beginner = (empty($activeUsersByLevelCurrent[0]['c'])) ? 0 : $activeUsersByLevelCurrent[0]['c'];
$elementary = (empty($activeUsersByLevelCurrent[1]['c'])) ? 0 : $activeUsersByLevelCurrent[1]['c'];
$preIntermediate = (empty($activeUsersByLevelCurrent[2]['c'])) ? 0 : $activeUsersByLevelCurrent[2]['c'];
$intermediate = (empty($activeUsersByLevelCurrent[3]['c'])) ? 0 : $activeUsersByLevelCurrent[3]['c'];
$upperIntermediate = (empty($activeUsersByLevelCurrent[4]['c'])) ? 0 : $activeUsersByLevelCurrent[4]['c'];
$advanced = (empty($activeUsersByLevelCurrent[5]['c'])) ? 0 : $activeUsersByLevelCurrent[5]['c'];*/

?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php JSRegister::begin(); ?>
<script>
    let xValues = <?= $xValues ?>;
    $('#see-statistics').click(function () {
        $('.row').removeClass('display-none')
    })

    let totalChart = new Chart("totalChart" , {
        type: "line",
        data: {
            labels: xValues,
            datasets: [
                {
                    label: 'Total',
                    data: <?= $total ?>,
                    borderColor: "red",
                    fill: true,
                    tension: 0.3
                },
            ]
        },
        options: {
            legend: {display: false},
            scales: {
                xAxes: [{
                    type: 'date',
                    time: {
                        displayFormats: {

                        }
                    }
                }],
            }
        }
    });

    let chart = new Chart("myChart", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [
                {
                    label: 'Beginner',
                    data: <?= $beginnerValues ?>,
                    borderColor: "blue",
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Elementary',
                    data: <?= $elementaryValues ?>,
                    borderColor: "green",
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Pre-Intermediate',
                    data: <?= $preIntermediateValues ?>,
                    borderColor: "Purple",
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Intermediate',
                    data: <?= $intermediateValues ?>,
                    borderColor: "orange",
                    fill: false,
                    tension: 0.3
                },
                /*{
                    label: 'Upper-Intermediate',
                    data: <?= $upperIntermediateValues ?>,
                    borderColor: "brown",
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Advanced',
                    data: <?= $advancedValues ?>,
                    borderColor: "#ffce56",
                    fill: false,
                    tension: 0.3
                },*/
            ]
        },
        options: {
            legend: {display: false},
            scales: {
                xAxes: [{
                    type: 'date',
                    time: {
                        displayFormats: {

                        }
                    }
                }],
            }
        }
    });
</script>
<?php JSRegister::end(); ?>


<script>
    document.styleSheets[0].disabled = true;
</script>
<style>
    #p0 {
        width: fit-content;
        width: -moz-fit-content;
    }
    .container {
        width: 100%;
    }
    .colored-user {
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }
    .display-none {
        display: none;
    }
</style>


<div class="container" id="id">
    <div class="row">
        <div class="col">
            <form action="/backend/web/" method="get" role="form">
                <input type="hidden" name="r" value="user">
                <label>
                    <input type="text" name="lastDay" placeholder="type last N days">
                </label>
                <button type="submit">OK</button> <button type="button" class="btn-success" id="see-statistics">See Statistics</button><br>
            </form>
        </div>
    </div>

    <div class="row display-none">
        <div class="col-md-6">
            <div style="max-width: 100%;">
                <canvas id="totalChart"></canvas>
            </div>
        </div>

        <div class="col-md-6">
            <div style="max-width: 100%;">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row display-none">
        <div class="col">
            <div class="col-lg-2">
                <br>
                <h5>Beginner: <code><?= $beginner ?></code></h5>
                <h5>Elementary: <code><?= $elementary ?></code></h5>
                <h5>Pre-Intermediate: <code><?= $preIntermediate ?></code></h5>
                <h5>Intermediate: <code><?= $intermediate ?></code></h5>
                <h5>Upper-Intermediate: <code><?= $upperIntermediate ?></code></h5>
                <h5>Advanced: <code><?= $advanced ?></code></h5>
                <h5>Total: <code><?= $advanced + $upperIntermediate + $intermediate + $preIntermediate + $elementary + $beginner ?></code></h5>
            </div>
        </div>
    </div>
</div>


<br>

<div class="container-fluid" style="width: 100%;">
    <div class="row justify-content-center">
        <div class="col-12 w-100">
            <div id="ajaxCrudDatatable">
                <?php \yii\widgets\Pjax::begin(); ?>
                <?php $dataProvider->setSort(['defaultOrder' => ['created_at' => SORT_DESC]]); ?>
                <?=GridView::widget([
                    'id'=>'crud-datatable',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pjax'=>true,
                    'columns' => require(__DIR__.'/_columns.php'),
                    'toolbar'=> [
                        ['content'=>
                            Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],
                                ['role'=>'modal-remote','title'=> 'Create new Users','class'=>'btn btn-default']).
                            Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                                ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                            '{toggleData}'.
                            '{export}'
                        ],
                    ],
                    'striped' => true,
                    'condensed' => true,
                    'responsive' => true,
                    'panel' => [
                        'type' => 'primary',
                        'heading' => '<i class="glyphicon glyphicon-list"></i>&nbsp;&nbsp; Users',
                        //'before'=>'<em>* Resize table columns just like a spreadsheet by dragging the column edges.</em>',
                        'after'=>BulkButtonWidget::widget([
                                'buttons'=>Html::a('<i class="glyphicon glyphicon-trash"></i>&nbsp; Delete All',
                                    ["bulkdelete"] ,
                                    [
                                        "class"=>"btn btn-danger btn-xs",
                                        'role'=>'modal-remote-bulk',
                                        'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                                        'data-request-method'=>'post',
                                        'data-confirm-title'=>'Are you sure?',
                                        'data-confirm-message'=>'Are you sure want to delete this item'
                                    ]),
                            ]).
                            '<div class="clearfix"></div>',
                    ]
                ])?>
                <?php \yii\widgets\Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>


<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
