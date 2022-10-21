<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\export\ExportMenu;
use yii\bootstrap\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $model app\models\Teacher */

$this->title = $model->teacher_id . " - " . $model->type;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers Fav'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="teacher-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php 
        $cols = [
            'id',
            'teacher_id',
            'type',
            'value',
            'created_at:datetime',
            'updated_at:datetime',
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
        
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $cols,
    ]) ?>

</div>
