<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ClassSubject */

$this->title = Yii::t('app', 'Create Class Subject');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Class Subjects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="class-subject-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
