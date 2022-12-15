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
            'id',
            'initial',
            'name',
            'firstname',
            'email_1:email',
            'email_2:email',
            'phone',
            'mobile',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'attribute' => 'Wunschliste',
                'format' => 'raw',
                'value' => function ($model) {
                    $list = array();
                    $strHours = "";
                    foreach($model->teacherWishlists as $listItem){
                        if(!empty($listItem['hours_min']))
                            $strHours = "Minimum: " . $listItem['hours_min'] . "<br />";
                        if(!empty($listItem['hours_max']))
                            $strHours .= "Maximum: " . $listItem['hours_max'] . "<br />";
                        
                        $list[] = $listItem['info'];
                    }
                    return $strHours  . implode(', ', $list) . "<br /><a href='" . Url::to(['teacher-wishlist/index', 'TeacherWishlistSearch[teacher_id]' => $model->id]) . "'>zur Wusnchliste</a>";; 
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
