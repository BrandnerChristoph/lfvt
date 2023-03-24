<?php

use app\models\Teacher;
use app\models\TeacherExtended;
use yii\helpers\Html;


use app\models\Department;
use kartik\widgets\ActiveForm;

//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ClassSubjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Gesamt-Backup');
$this->params['breadcrumbs'][] = ['label' => 'Backup', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backup-index">

    
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class='col-lg-12'>
    
        <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <?php
                    

                    echo "<div class='col-lg-4 ' >";
                        echo "<h3>Einstellungen</h3>";
                        echo "<div class='col-lg-4'><label for='backup_name'>Backupname</label></div>";
                        echo "<div class='col-lg-12'>" . Html::textInput("backup_name", "", ['class' =>'form-control', 'id' => 'backup_name']) . "</div>";
                    echo "</div>";

                    

                ?>  
                <div class="col-lg-12">
                    <div class="form-group">
                        <center>
                            <?= Html::submitButton(Yii::t('app', 'Gesamt-Backup erstellen'), ['class' => 'btn btn-success']) ?>
                        </center>
                    </div>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
       


    
    </div>
</div>
