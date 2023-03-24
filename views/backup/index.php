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

$this->title = Yii::t('app', 'Backup');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backup-index">

    
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class='col-lg-12'>
    
        <div class="row">
            <div class="col-lg-4" >
                <h2>Abteilungsbackup</h2>
                <p>Backup einzelner Abteilungen (Fächerzuweisungen)</p>
                <p><center><a class="btn btn-warning" href="<?= Yii::$app->urlManager->createUrl(['/backup/part-backup']) ?>">Abteilungsbackup &raquo;</a></center></p>
            </div>
            <div class="col-lg-4" >
                <h2>Gesamtbackup</h2>
                <p>Backup der gesamten Applikation (Lehrer, Klassen, Fächerzuweisungen, ...)</p>
                <p><center><a class="btn btn-warning" href="<?= Yii::$app->urlManager->createUrl(['/backup/full-backup']) ?>">Gesamtbackup &raquo;</a></center></p>
            </div>
        </div>
        
            <div class="col-lg-4" style="margin-top:70px">
                <h2>Wiederherstellung</h2>
                <p>Wiederherstellung einer Sicherung</p>
                <p><center><a class="btn btn-danger" href="<?= Yii::$app->urlManager->createUrl(['/backup/restore']) ?>">Wiederherstellung &raquo;</a></center></p>
            </div>
        
       


    
    </div>
</div>
