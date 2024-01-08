<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SubjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Subjects');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-index">

    <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(Yii::t('app', 'Create Subject'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'filename' => 'Faecher',
            'columnSelectorOptions' => [
                'icon' => '<i class="fa fa-list"></i>',
            ],
            'dropdownOptions' => [
                'label' => 'Export',
                'class' => 'btn btn-outline-secondary btn-default'
            ],
            'columns' => [
                'id',
                'name',
                //'value:decimal',
                [
                    'attribute' => 'value',
                    'format' => 'raw',
                    'value' => function($model){
                        return Yii::$app->formatter->asDecimal($model->value, 3);
                    },
                    'contentOptions' => ['style'=>'text-align: right;'],
                ],
                [
                    'attribute' => 'value_real',
                    'format' => 'raw',
                    'value' => function($model){
                        return Yii::$app->formatter->asDecimal($model->value, 2);
                    },
                    'contentOptions' => ['style'=>'text-align: right;'],
                ],
                'type',
                'sortorder',
                'updated_at:datetime',
                'created_at:datetime',

            ]
        ]) ?>
    <p/>


    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            //'value:decimal',
            [
                'attribute' => 'value',
                'format' => 'raw',
                'value' => function($model){
                    return Yii::$app->formatter->asDecimal($model->value, 3);
                },
                'contentOptions' => ['style'=>'text-align: right;'],
            ],
            [
                'attribute' => 'value_real',
                'format' => 'raw',
                'value' => function($model){
                    return Yii::$app->formatter->asDecimal($model->value_real, 2);
                },
                'contentOptions' => ['style'=>'text-align: right;'],
            ],
            [
                'attribute' => 'type',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->type;
                },
                /*
                'filter' => [
                    'Allgemeinbildenden Gegenstände' => 'Allgemeinbildenden Gegenstände', 
                    'Fachtheorie' => 'Fachtheorie',
                    'Werkstatt' => 'Werkstatt',
                ],
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                        //'placeholder' => '',
                    ],
                ],
                */
            ],
            'sortorder',
            'updated_at:datetime',
            'created_at:datetime',

            //['class' => 'yii\grid\ActionColumn'],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update} {delete}'],

        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
