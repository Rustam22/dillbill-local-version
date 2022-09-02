<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use hoaaah\ajaxcrud\CrudAsset; 
use hoaaah\ajaxcrud\BulkButtonWidget;
use yii\helpers\Url;


/* @var $searchModel backend\models\GrammarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

/* @var $searchModelTodayGrammar backend\models\TodaysGrammarSearch */
/* @var $dataProviderTodayGrammar yii\data\ActiveDataProvider */

$this->title = 'Grammars';
$this->params['breadcrumbs'][] = $this->title;


CrudAsset::register($this);


?>

<style>
    #p0 {
        width: fit-content;
        width: -moz-fit-content;
    }
    .ui-sortable-handle {
        cursor: grab !important;
    }
</style>

<script>
    <?php
        $this->registerJs("
            $(document).ready(function () {
            document.styleSheets[0].disabled = true;
            let _csrf_frontend = '".Yii::$app->request->csrfToken."'
            let swapOrder = '".Url::to(['grammar/swap-order'], true)."'
            
            $(document).on('pjax:end', function(e) {
               // $.pjax.reload({container:'#today-crud-datatable-pjax'})
            });
            
            
    
            function swapOrderId(oldDataKeyId, newDataKeyId) {
                $.ajax({
                    url : swapOrder,
                    type : 'POST',
                    data : {'_csrf-frontend': _csrf_frontend, 'oldDataKeyId': oldDataKeyId, 'newDataKeyId': newDataKeyId},
                    beforeSend: function() {
                    },
                    success : function(data) {
                        data =  JSON.parse(data)
                        console.log(data)
                    },
                    error : function(request, error) {
                        console.log('error')
                    },
                    complete: function() {
                        $.pjax.reload({container:'#crud-datatable-pjax'})
                    }
                });
            }
    
            function doSortable() {
                $('.grammar-index tbody').sortable({
                    disabled: false,
                    placeholder: 'placeholder',
                    forcePlaceholderSize: true,
                    start: function(e, ui) {
                        // creates a temporary attribute on the element with the old index
                        $(this).attr('data-prev-index', ui.item.index())
                        $(this).attr('data-case-index', $('.ui-sortable tr:first-child').data('key'))
                    },
    
                    update: function(e, ui) {
                        // gets the new and old index then removes the temporary attribute
                        let newIndex = ui.item.index()
                        let newDataKeyId = (newIndex === 0) ? $(this).attr('data-case-index') : $('.ui-sortable tr:nth-child(' + newIndex + ')').data('key')
    
                        //let oldIndex = $(this).attr('data-prev-index')
                        //let element_id = ui.item.attr('data-key')
                        let oldDataKeyId = ui.item.attr('data-key')
    
                        console.log('newIndex: ' + newIndex + '; newDataKeyId: ' + newDataKeyId + '; oldDataKeyId: ' + oldDataKeyId)
                        swapOrderId(oldDataKeyId, newDataKeyId)
                        $(this).removeAttr('data-prev-index')
                    }
                }).disableSelection()
            }
    
            doSortable()
    
            $(document).on('pjax:success', function() {
                doSortable()
            })
        })
        ");
    ?>
</script>



<div class="grammar-index">
    <div class="content-fluid" style="width: 100%;">
        <div class="row justify-content-around">
            <div class="col-12">
                <div id="ajaxCrudDatatable">
                    <?=GridView::widget([
                        'id'=>'crud-datatable',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax'=> true,
                        'columns' => require(__DIR__.'/_columns.php'),
                        'toolbar'=> [
                            ['content'=>
                                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],
                                    ['role'=>'modal-remote','title'=> 'Create new Grammars','class'=>'btn btn-default']).
                                Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                                    ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                                '{toggleData}'.
                                '{export}'
                            ],
                        ],
                        'striped' => false,
                        'condensed' => true,
                        'responsive' => true,
                        'panel' => [
                            'type' => 'primary',
                            'heading' => '<i class="glyphicon glyphicon-list"></i> &nbsp;&nbsp;Grammars listing',
                            'before'=>'<em></em>',
                            'after'=>BulkButtonWidget::widget([
                                    'buttons'=>Html::a('<i class="glyphicon glyphicon-trash"></i>&nbsp; Delete All',
                                        ["bulkdelete"] ,
                                        [
                                            "class"=>"btn btn-danger btn-xs",
                                            'role'=>'modal-remote-bulk',
                                            'data-confirm'=>false, 'data-method'=>false,// for override yii data api
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
        </div>
    </div>
</div>




<br><br>
<div class="todays-grammar-index">
    <div class="content-fluid" style="width: 100%;">
        <div class="row justify-content-around">
            <div class="col-12">
                <div id="ajaxCrudGrammar">
        <?=GridView::widget([
            'id' => 'my-crud',
            'dataProvider' => $dataProviderTodayGrammar,
            'filterModel' => $searchModelTodayGrammar,
            'pjax' => true,
            'columns' => require(__DIR__.'/_today_columns.php'),
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', Url::to(['todays-grammar/create'], true),
                        ['role'=>'modal-remote', 'title'=> 'Create new Today &nbsp;Grammars', 'class'=>'btn btn-default']).
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                        ['data-pjax' => 1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                    '{toggleData}'.
                    '{export}'
                ],
            ],
            'striped' => false,
            'condensed' => false,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i>  &nbsp;&nbsp;Today`s Grammar',
                'before'=>'',
                'viewOptions' => ['url' => 'test'],
            ],
            'pjaxSettings'=>[
                'refreshGrid' => true,
                'id' => 'crud-grammar',
                'neverTimeout'=>true,
                //'beforeGrid'=>'My fancy content before.',
                //'afterGrid'=>'My fancy content after.',
            ]
        ])?>
                </div>
            </div>
        </div>
    </div>
</div>



<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>


