<?php

use yii\helpers\Html;
use app\models\Department;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
//use yii\widgets\ActiveForm;
use app\models\TeacherExtended;
use app\models\DepartmentExtended;

/* @var $this yii\web\View */
/* @var $model app\models\SchoolClass */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="school-class-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord]) ?>

    <?= $form->field($model, 'classname')->textInput(['maxlength' => true]) ?>

    <?php //$form->field($model, 'department')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department')->widget(Select2::classname(),[
                        'data' => DepartmentExtended::getAllDepartmentsArrayMap(),
                        'options' => [
                            'placeholder' => 'Abteilung',
                            
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'multiple' => false,
                        ]
                    ]) 
    ?>


    <?php //$form->field($model, 'period')->textInput() ?>

    <?= $form->field($model, 'annual_value')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'class_head')->textInput() ?>

    <?= $form->field($model, 'class_head')->widget(Select2::classname(),[
                        'data' => TeacherExtended::getAllTeachersArrayMap(),
                        'options' => [
                            'placeholder' => 'Klassenvorstand',                            
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'multiple' => false,
                        ]
                    ]) 
    ?>

    <?= $form->field($model, 'studentsnumber')->textInput() ?>

    <?= $form->field($model, 'info')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
