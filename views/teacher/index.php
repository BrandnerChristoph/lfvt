<?php

use yii\helpers\Url;
//use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

use kartik\icons\Icon;
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

                    if(!is_null($model->teacherWishlist)){
                        $min = !empty($model->teacherWishlist->hours_min) ? $model->teacherWishlist->hours_min : $min;
                        $max = !empty($model->teacherWishlist->hours_max) ? $model->teacherWishlist->hours_max : $max;
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
                // Textueller Wunsch
                'attribute' => 'Lehrerwunsch',
                'format' => 'raw',
                'value' => function ($model) {
                    if(!is_null($model->teacherWishlist))
                        return $model->teacherWishlist->info;
                },
            ],
            [
                'attribute' => 'Minimumstunden',
                'format' => 'raw',
                'value' => function($model){
                    if(!is_null($model->teacherWishlist))
                        return $model->teacherWishlist->hours_min;
                }
            ],
            [
                'attribute' => 'Maximumstunden',
                'format' => 'raw',
                'value' => function($model){
                    if(!is_null($model->teacherWishlist))
                        return $model->teacherWishlist->hours_max;
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
            [
                'attribute' => 'initial', 
                'contentOptions' => ['style'=>'text-align: center; width: 10px;'],
            ],                       
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

                    if(!is_null($model->teacherWishlist)){
                        $min = !empty($model->teacherWishlist->hours_min) ? $model->teacherWishlist->hours_min : $min;
                        $max = !empty($model->teacherWishlist->hours_max) ? $model->teacherWishlist->hours_max : $max;
                    }

/*
                    foreach($model->teacherWishlists as $listItem){
                        if(!empty($listItem['hours_min']))
                            $min = $listItem['hours_min'];
                        if(!empty($listItem['hours_max']) && ($listItem['hours_min'] != $listItem['hours_max']))
                            $max = $listItem['hours_max'];
                    }
                    */

                    
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
                'contentOptions' => ['style'=>'text-align: center; width: 100px;'],
            ],
            [
                'attribute' => 'Wert',
                'value' => function($model){
                    return $model->teachingHours;
                },                
                'contentOptions' => ['style'=>'text-align: center; width: 100px;'],
            ],
            'name',
            'firstname',
            'email_1:email',
            
            [
                'attribute' => 'Stundenwunsch',
                'format' => 'raw',
                'value' => function ($model) {
                    $strHours = "";
                    $objWish = $model->teacherWishlist;
                    if(!is_null($objWish)){
                        $strHours = $objWish->hours_min;
                        if(!empty($objWish->hours_max) && ($objWish->hours_min != $objWish->hours_max))
                            $strHours .= " - " . $objWish->hours_max;

                        return $strHours . '<a class="showModalButton btn btn-sm" '
                                            . 'style="font-size: 0.85rem; color:orange; padding: 0.2rem 0.3rem;" '
                                            . 'value="' . Url::to(['teacher-wishlist/update', 'id' => $objWish->id]) . '" href="#" title="Lehrerwunsch">'
                                            . Icon::show('edit')        
                                            . '</a>'; 
                        
                    }
                    return '<a class="showModalButton btn btn-sm" '
                            . 'style="font-size: 0.85rem; color:orange; padding: 0.2rem 0.3rem;" '
                            . 'value="' . Url::to(['teacher-wishlist/create-for-teacher', 'teacher_id' => $model->id]) . '" href="#" title="neuer Lehrerwunsch">'
                            . Icon::show('plus')        
                            . '</a>';; 
                },
            ],
            [
                'attribute' => 'Lehrerwunsch',
                'format' => 'raw',
                'value' => function ($model) {

                    return !is_null($model->teacherWishlist) ? $model->teacherWishlist->info : null;
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function ($model) {
                    if($model->is_active == 1)
                        return "Ja";
                    return "<span style='color: red;'>nein</span>";
                },
    
                'filter' => [1 => 'ja', 0 => 'nein'],
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                        'placeholder' => 'Aktiv?',
                    ],
                ],
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
