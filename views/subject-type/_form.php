<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use kartik\widgets\ColorInput;

/* @var $this yii\web\View */
/* @var $model app\models\Subject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subject-form">
    
    <?php $form = ActiveForm::begin(); ?>
    
        <div class="row">
            <div class="col-lg-3">
                <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord]) ?>
            </div>

            <div class="col-lg-9">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-lg-4">
                <?= $form->field($model, 'sortorder')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-lg-4">
                <?= $form->field($model, 'year_update')->widget(Select2::classname(),[
                                    'data' => [
                                                'A' => 'Aufsteigerprinzip', 
                                                'J' => 'Jahrgangsprinzip',                                                
                                            ],
                                    'options' => [
                                        'placeholder' => 'Art des Jahreswechsels',                            
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'multiple' => false,
                                    ]
                                ])
                ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'color')->widget(ColorInput::class, [
                        'options' => ['placeholder' => 'Farbe wählen ...'],
                    ]); 
                ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field($model, 'info')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="form-group col-lg-12 text-center">
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success text-center']) ?>
            </div>

        </div>
    <?php ActiveForm::end(); ?>
</div>
