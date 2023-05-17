<?php

use app\models\Teacher;
use app\models\TeacherExtended;
use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ClassSubjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Class Subjects');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="class-subject-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Class Subject'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'class',
            'subject',
            'group',
            'value',
            //'teacher',
            [
                'attribute' => 'teacher',
                'value' => function($model) {
                    return TeacherExtended::getTeacherInitial($model->teacher);
                },
                'filter' => ArrayHelper::map(Teacher::find()->asArray()->all(), 'id', 'initial'),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => ['prompt' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            
            'classroom',
            //'updated_at',
            //'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    
</div>
