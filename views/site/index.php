<?php


use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'LFV - HTL Waidhofen/Ybbs';
?>
<div class="site-index">
    <script>
        $("form").submit(function(event){
            console.log("load fullpage loader");
            $("#fullpage-loader").show();
        });
    </script>

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Lehrfächerverteilung</h1>

        <p class="lead">HTL Waidhofen/Ybbs</p>

        <p><a class="btn btn-lg btn-success" href="<?= Yii::$app->urlManager->createUrl(['/class-subject/update-department']) ?>">zur Lehrfächerverteilung</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4" >
                <h2>Lehrer</h2>
                <p>Lehrerverwaltung: erstellen, bearbeiten und löschen von Lehrern sowie die Verwaltung der Lehrerwünsche </p>
                <p><center><a class="btn btn-warning" href="<?= Yii::$app->urlManager->createUrl(['/teacher/index']) ?>">zur Lehrerverwaltung &raquo;</a></center></p>
            </div>
            <div class="col-lg-4" >
                <h2>Klassen</h2>
                <p>Klassenverwaltung: erstellen, bearbeiten und löschen von Schulklassen, dabei können Abteilungszugehörigkeiten sowie Jahreswertigkeiten festgelegt werden. Weiters können die Schüleranzahl und der Klassenvorstand festgelegt werden.</p>
                <p><center><a class="btn btn-warning" href="<?= Yii::$app->urlManager->createUrl(['/school-class/index']) ?>">zur Klassenverwaltung &raquo;</a></center></p>
            </div>
            <div class="col-lg-4" >
                <h2>Fach</h2>
                <p>Fächervewaltung: erstellen, bearbeiten und löschen von Fächern, dabei können auch Wertigkeiten festgelegt werden</p>
                <p><center><a class="btn btn-warning" href="<?= Yii::$app->urlManager->createUrl(['/subject/index']) ?>">zur Fächerverwaltung &raquo;</a></center></p>
            </div>
        </div>

    </div>

</div>
