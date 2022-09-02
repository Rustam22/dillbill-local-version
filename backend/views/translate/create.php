<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Translate */

?>

<script>
    $(document).ready(function () {
        $('.note-editable').html('');
    })
</script>

<div class="translate-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
