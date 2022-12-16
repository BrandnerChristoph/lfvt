<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Report');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
<?php
/*
    • Übersicht alle Lehrer der Klasse
	• Alle Unterrichte des Lehrers
	• Liste der Lehrer und deren Werteinheiten
	
	• Vergleich der Lehrerwünsche zu den eingegebenen Daten (Abweichungen)
	•  
		*/ ?>

        <div class="col-lg-12">
        </div>
            <div class="col-lg-3">
                <div class="box box-body box-success bg-gray-light">
                    <h4><?= Yii::t("app", "Report.TeacherByClass")  ?></h4>
                    <p>Übersicht aller Lehrer der Klasse (PDF)</p>
                        <?= Html::a(Yii::t("app", "Report.TeacherByClass") . ' &raquo;', 
                                    ['/report-print/teacher-in-class'], 
                                    ['class' => 'btn btn-primary', 'target' => '_blank']
                            ) ?>
                </div>
            </div>

        

    <?php Pjax::end(); ?>

</div>
