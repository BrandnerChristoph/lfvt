<?php

use yii\helpers\Html;
use app\models\TeacherExtended;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClassSubject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="class-subject-form" id="class-subject-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="container">
        <div class="row">
            <?php // $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
            <div class="col-lg-6">
                <?= $form->field($model, 'class')->textInput(['maxlength' => true])->label("Klasse") ?>
                </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
            </div>

            
            <div class="col-lg-6">
                <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'hours', ['inputOptions' => ['autofocus' => 'autofocus']])->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field($model, 'teacher')->widget(Select2::classname(),[
                                'data' => TeacherExtended::getAllTeachersArrayMap(),
                                'options' => [
                                    'placeholder' => 'Lehrer',
                                    'appendTo' => '#myModal',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => false,
                                    'multiple' => false,
                                ]
                            ])->label("Lehrer")
                ?>
            </div>

            <?php //= $form->field($model, 'classroom')->textInput(['maxlength' => true]) ?>


            <div class="form-group col-lg-12 text-center">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        
    </div>
    <?php ActiveForm::end(); ?>
</div>
