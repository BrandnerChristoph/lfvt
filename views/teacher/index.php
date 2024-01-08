<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\GridView;

use yii\helpers\ArrayHelper;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Teachers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
        $cols = [
            //'initial',
            [
                'attribute' => 'initial',
                'format' => 'raw',
                'value' => function ($model) {
                    return strtoupper($model->initial);
                }
            ], 
            'name',
            'firstname',
            'is_active',
            'email_1:email',
            [
                'attribute' => 'Stunden',
                'format' => 'raw',
                'value' => function ($model) {
                    $strReturn = "";
                    $curHours = $model->hours;
                    $curTeachingHours = $model->teachingHours;
                    $min = -1;
                    $max = -1;
                    foreach($model->teacherWishlists as $listItem){
                        if(!empty($listItem['hours_min']))
                            $min = $listItem['hours_min'];
                        if(!empty($listItem['hours_max']) && ($listItem['hours_min'] != $listItem['hours_max']))
                            $max = $listItem['hours_max'];
                    }

                    if($min >= 0 && $max >=0){
                        if($max < $curHours){
                            $strReturn = "<center><div class='btn-sm btn-danger'>".$curHours."</div></center>";
                        } elseif($min > $curHours){
                            $strReturn = "<center><div class='btn-sm btn-warning'>".$curHours."</div></center>";
                        } else {
                            $strReturn = "<center><div class='btn-sm btn-success'>".$curHours."</div></center>";
                        }
                    } else {
                        $strReturn = "<center>".$curHours."</center>";
                    }
                    
                    return $strReturn ;
                },
            ],
            [
                'attribute' => 'Stundenwunsch',
                'format' => 'raw',
                'value' => function ($model) {
                    $strHours = "";
                    foreach($model->teacherWishlists as $listItem){
                        if(!empty($listItem['hours_min']))
                            $strHours = $listItem['hours_min'];
                        if(!empty($listItem['hours_max']) && ($listItem['hours_min'] != $listItem['hours_max']))
                            $strHours .= " - " . $listItem['hours_max'];
                        
                        $list[] = $listItem['info'];
                    }
                    return $strHours; 
                },
            ],
            [
                'attribute' => 'Minimumstunden',
                'format' => 'raw',
                'value' => function($model){
                    foreach($model->teacherWishlists as $listItem){
                        if(!empty($listItem['hours_min']))
                            return $listItem['hours_min'];
                    }
                    return "";
                }
            ],
            [
                'attribute' => 'Maximumstunden',
                'format' => 'raw',
                'value' => function($model){
                    foreach($model->teacherWishlists as $listItem){
                        if(!empty($listItem['hours_max']))
                            return $listItem['hours_max'];
                    }
                    return "";
                }
            ],
            [
                'attribute' => 'Klasse/Fächer',
                'format' => 'raw',
                'value' => function ($model) {
                    $list = array();
                    $strHours = "";
                    foreach($model->classSubjects as $listItem){                            
                        $list[] = $listItem['class'] . " " . $listItem['subject'];
                    }
                    return $strHours  . implode(' | ', $list); 
                },
            ],
        ];
    ?>

        <?= Html::a(Yii::t('app', 'Create Teacher'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'filename' => 'Lehrerliste',
            'columnSelectorOptions' => [
                'icon' => '<i class="fa fa-list"></i>',
            ],
            'dropdownOptions' => [
                'label' => 'Export',
                'class' => 'btn btn-outline-secondary btn-default'
            ],
            'columns' => $cols,
        ]) ?>
    <p/>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            'initial',            
            [
                'attribute' => 'Stunden',
                'format' => 'raw',
                'value' => function ($model) {

                    $curHours =  $model->getHours();

                    $strReturn = "";
                    //$curHours = $model->hours;
                    //$curTeachingHours = $model->teachingHours;
                    $min = -1;
                    $max = -1;
                    foreach($model->teacherWishlists as $listItem){
                        if(!empty($listItem['hours_min']))
                            $min = $listItem['hours_min'];
                        if(!empty($listItem['hours_max']) && ($listItem['hours_min'] != $listItem['hours_max']))
                            $max = $listItem['hours_max'];
                    }

                    if($min >= 0 && $max >=0){
                        if($max < $curHours){
                            $strReturn = "<center><div class='btn-sm btn-danger'>".$curHours."</div></center>";
                        } elseif($min > $curHours){
                            $strReturn = "<center><div class='btn-sm btn-warning'>".$curHours."</div></center>";
                        } else {
                            $strReturn = "<center><div class='btn-sm btn-success'>".$curHours."</div></center>";
                        }
                    } else {
                        $strReturn = "<center>".$curHours."</center>";
                    }  
                    
                    return $strReturn ;
                },
            ],
            [
                'attribute' => 'Wert',
                'value' => function($model){
                    return $model->teachingHours;
                },
            ],
            'name',
            'firstname',
            'email_1:email',
            //'email_2:email',
            //'phone',
            //'mobile',
            //'created_at',
            //'updated_at',
            
            [
                'attribute' => 'Stundenwunsch',
                'format' => 'raw',
                'value' => function ($model) {
                    $strHours = "";
                    foreach($model->teacherWishlists as $listItem){
                        if(!empty($listItem['hours_min']))
                            $strHours = $listItem['hours_min'];
                        if(!empty($listItem['hours_max']) && ($listItem['hours_min'] != $listItem['hours_max']))
                            $strHours .= " - " . $listItem['hours_max'];
                        
                        $list[] = $listItem['info'];
                    }
                    return $strHours; 
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => "html",
                'value' => function($model) {
                    if($model->is_active == 1)
                        return "Ja";
                    return "<span style='color: red;'>nein</span>";                    
                },
                'filter' => [1 => 'ja', 0 => 'nein'],
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => ['prompt' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
