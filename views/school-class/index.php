<?php

use app\models\Department;
use yii\helpers\Html;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SchoolClassSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'School Classes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="school-class-index">

    <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(Yii::t('app', 'Create School Class'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'filename' => 'Klassenliste',
            'columnSelectorOptions' => [
                'icon' => '<i class="fa fa-list"></i>',
            ],
            'dropdownOptions' => [
                'label' => 'Export',
                'class' => 'btn btn-outline-secondary btn-default'
            ],
            'columns' => [
                'id',
                'classname',
                'department',
                //'period',
                'annual_value',
                'class_head',
                'studentsnumber',
                'info',
                'updated_at:datetime',
                'created_at:datetime',
            
            ],
        ]) ?>
    <p/>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'classname',
            //'department',
            [
                'attribute' => 'department',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->department;
                },
    
                'filter' => ArrayHelper::map(Department::find()->asArray()->all(), 'id', 'id'),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                        'placeholder' => 'Abteilung',
                    ],
                ],
            ],
            //'period',
            'annual_value',
            'class_head',
            'studentsnumber',
            //'info',
            //'updated_at',
            //'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
