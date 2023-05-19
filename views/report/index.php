<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Report');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="container row">

        <div class="col-lg-12"></div>
        
            <div class="col-lg-3">
                <div class="box box-body box-success bg-gray-light">
                    <h4><?= Yii::t("app", "Report.TeacherByClass")  ?></h4>
                    <p>Gesamt-Übersicht aller Lehrer der Klasse (PDF)</p>
                        <?= Html::a(Yii::t("app", "Report.TeacherByClass") . ' &raquo;', 
                                    ['/report-print/teacher-in-class'], 
                                    ['class' => 'btn btn-primary', 'target' => '_blank']
                            ) ?>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="box box-body box-success bg-gray-light">
                    <h4><?= Yii::t("app", "Report.TeacherWorkload")  ?></h4>
                    <p>Gesamt-Übersicht der Stunden (Werteinheiten) von Lehrern (PDF)</p>
                        <?= Html::a(Yii::t("app", "Report.TeacherWorkload") . ' &raquo;', 
                                    ['/report-print/teacher-workload'], 
                                    ['class' => 'btn btn-primary', 'target' => '_blank']
                            ) ?>
                </div>
            </div>

            <!--div class="col-lg-3">
                <div class="box box-body box-success bg-gray-light">
                    <h4><?= Yii::t("app", "Report.AllTeacherWorkload")  ?></h4>
                    <p>Gesamt-Übersicht aller Lehrer und deren Fächer (PDF)</p>
                        <?= Html::a(Yii::t("app", "Report.AllTeacherWorkload") . ' &raquo;', 
                                    ['/teacher/print-lesson'], 
                                    ['class' => 'btn btn-primary', 'target' => '_blank']
                            ) ?>
                </div>
            </div-->


        <div class="col-lg-12"></div>
        
            

        <!--div class="col-lg-12"></div>
        
            <div class="col-lg-3">
                <div class="box box-body box-success bg-gray-light">
                    <h4><?= Yii::t("app", "Report.Department.OverviewReport")  ?></h4>
                    <p>Gesamt-Übersicht aller Lehrer und deren Fächer (PDF)</p>
                        <?= Html::a(Yii::t("app", "Report.DepartmentOverviewReport") . ' &raquo;', 
                                    ['/report-print/department-overview'], 
                                    ['class' => 'btn btn-warning', 'target' => '_blank']
                            ) ?>
                </div>
            </div-->

            
        
    </div>
</div>
