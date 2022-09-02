<?php


/* @var $this yii\web\View */
/* @var $searchModel backend\models\SocketUsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Control Panel';
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="control-panel-index">


    <form action="<?= Yii::$app->request->hostInfo ?>/backend/web/index.php?r=control-panel%2Fcreate" method="post">
        <input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">

        <div class="form-group">
            <label for="levels">Levels</label>
            <input type="text" class="form-control" id="levels" name="levels" placeholder="Enter Levels" value="beginner, elementary, pre-intermediate, intermediate, upper-intermediate">
        </div>

        <div class="form-group">
            <label for="timeRanges">Time Ranges</label>
            <input type="text" class="form-control" id="timeRanges" name="timeRanges" placeholder="Time Ranges" value="09:00-12:00, 15:00-18:00, 18:00-21:00, 21:00-00:00, 21:00-23:59, 21:00-24:00">
        </div>

        <div class="form-group">
            <label for="exceptionalAccounts">Exceptional accounts</label>
            <textarea class="form-control" id="exceptionalAccounts" name="exceptionalAccounts" rows="3">rustam.atakisiev@gmail.com, emil.hasanli.91@gmail.com, sanan@dillbill.net, khasiyev.farid@gmail.com, fidan.jafarzadeh@gmail.com, jalyaabbakirova@gmail.com</textarea>
        </div>

        <?php if($executionTime) { ?>
            <code>Execution time: <?= $executionTime ?></code><br><br>
        <?php } ?>

        <button type="submit" class="btn btn-primary">Submit ACC</button>
    </form>


</div>
