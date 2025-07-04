<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\export\ExportMenu;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Teacher */

$this->title = $model->name . " " . $model->firstname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="teacher-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php 
        $cols = [
            //'id',
            //'initial',
            [
                'attribute' => 'initial',
                'value' => function ($model) {
                    return strtoupper($model->initial);                    
                },
            ],
            //'name',
            [
                'attribute' => 'name',
                'value' => function ($model) {
                    return strtoupper($model->name);                    
                },
            ],
            
            'firstname',
            [
                'attribute' => 'is_active',
                'value' => function ($model) {
                    if($model->is_active == 1)
                        return "Ja";
                    return "Nein";
                }
            ],
            'email_1:email',
            'email_2:email',
            'phone',
            'mobile',
            'sent_lfvt_timestamp:datetime',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'attribute' => 'Wunschliste',
                'format' => 'raw',
                'value' => function ($model) {
                    $objWish = $model->teacherWishlist;
                    $strHours = "";
                    if(!is_null($objWish)){
                        if(!empty($objWish->hours_min))
                            $strHours = "Minimum: " . $objWish->hours_min . "<br />";
                        if(!empty($objWish->hours_max))
                            $strHours .= "Maximum: " . $objWish->hours_max . "<br />";
                        
                        $strHours .= $objWish->info;
                    }
                    return $strHours;
                    
                },
            ],
            [
                'attribute' => 'Stunden',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->hours . " (" . $model->teachingHours . ")";
                },
            ],
            [
                'attribute' => 'Klasse-Fächer',
                'format' => 'raw',
                'value' => function ($model) {
                    $list = array();
                    $strHours = "";
                    foreach($model->classSubjects as $listItem){                            
                        $list[] = $listItem['class'] . ": " . $listItem['subject'];
                    }
                    return $strHours  . implode(' <br /> ', $list); 
                },
            ],
        ];
    ?>

    <p>
        <?php /* fails on css load
                    ButtonDropdown::widget([
                'label' => Yii::t('app', 'Action'),
                'dropdown' => [
                    'items' => [
                        ['label' => 'Unterrichtsliste', 'url' => '/'],
                        ['label' => 'DropdownB', 'url' => '#'],
                    ],
                ],
            ])*/
        ?>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Print Overview (PDF)'), ['print-lesson', 'id' => $model->id], ['class' => 'btn btn-default border', 'target' => 'blank']) ?>
        
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $cols,
    ]) ?>

</div>
