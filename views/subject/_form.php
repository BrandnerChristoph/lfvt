<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Subject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subject-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->widget(Select2::classname(),[
                        'data' => [
                                    'Allgemeinbildenden Gegenstände' => 'Allgemeinbildenden Gegenstände', 
                                    'Fachtheorie' => 'Fachtheorie',
                                    'Werkstatt' => 'Werkstatt',
                                ],
                        'options' => [
                            'placeholder' => 'Unterrichtstyp',                            
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'multiple' => false,
                        ]
                    ]) 
    ?>

    <?= $form->field($model, 'sortorder')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
