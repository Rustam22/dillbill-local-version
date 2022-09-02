<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use kartik\grid\GridView;
use hoaaah\ajaxcrud\CrudAsset; 
use hoaaah\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PromoActionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


/* @var $searchModelPremiumCode backend\models\PremiumCodeSearch */
/* @var $dataProviderPremiumCode yii\data\ActiveDataProvider */


$this->title = 'Promo Actions';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<style>
    .container {
        max-width: 1550px;
        width: 100%;
    }
</style>
<script>
    <?php
    $this->registerJs("
            $(document).ready(function () {
            document.styleSheets[0].disabled = true;            
            $(document).on('pjax:end', function(e) {
               // $.pjax.reload({container:'#today-crud-datatable-pjax'})
            });
        })
        ");
    ?>
</script>

<div class="promo-actions-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],
                    ['role'=>'modal-remote','title'=> 'Create new Promo Actions','class'=>'btn btn-default']).
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
                'heading' => '<i class="glyphicon glyphicon-list"></i> &nbsp; Promo Actions',
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
    </div>
</div>



<br><br>
<div class="premium-code-index">
    <div id="ajaxCrudPremiumCode">
        <?=GridView::widget([
            'id'=>'premium-code',
            'dataProvider' => $dataProviderPremiumCode,
            'filterModel' => $searchModelPremiumCode,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_premium_columns.php'),
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', Url::to(['premium-code/create'], true),
                        ['role'=>'modal-remote','title'=> 'Create new Premium Codes','class'=>'btn btn-default']).
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                        ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                    '{toggleData}'.
                    '{export}'
                ],
            ],
            'striped' => false,
            'condensed' => false,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i> &nbsp; Premium Codes',
                'before'=>'',
                'viewOptions' => ['url' => 'test'],
            ],
            'pjaxSettings'=>[
                'refreshGrid' => true,
                'id' => 'premium-code',
                'neverTimeout'=>true,
                //'beforeGrid'=>'My fancy content before.',
                //'afterGrid'=>'My fancy content after.',
            ]
        ])?>
    </div>
</div>


<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>



