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

$this->title = Yii::t('app', 'Backup wiederherstellen');
$this->params['breadcrumbs'][] = ['label' => 'Backup', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backup-index">

    
    <h1><?= Html::encode($this->title) ?></h1>
    
        <div class='col-lg-12' style="margin-top: 60px">
        <h3>Gesamt-Backup Wiederherstellen</h3>
        
        <?php

    if ($handle = opendir(Yii::$app->params["backupDirectory"] . "full/")) {
        $arrFullBackups = array();

        $i=0;
        /* Dies ist der korrekte Weg, ein Verzeichnis zu durchlaufen. */
        while (false !== ($entry = readdir($handle))) {

            if($entry != "." && $entry != ".."){
                $dateTime = substr($entry, 0, 4) . "-" . substr($entry, 4, 2) . "-" . substr($entry, 6, 2) . " " . substr($entry, 9, 2) . ":" . substr($entry, 11, 2) . ":" . substr($entry, 13, 2);
                $posStartName = strpos($entry, "_")+1;
                $partFilename = substr($entry, $posStartName);
                $backup_name = substr($partFilename, 0, strpos($partFilename, ".sql"));

                $arrFullBackups[$i]["date"] = $dateTime . ": " . $backup_name;
                $arrFullBackups[$i]["filename"] = $entry;                
                $i++;
            }
        }
        closedir($handle);
        rsort($arrFullBackups);
        /*
        print_r($arrFullBackups);
        
        foreach ($arrFullBackups as $backupFile){
            echo $backupFile['date'] . " : ". $backupFile['filename'] ." <br />";
        }
        */

        
    $form = ActiveForm::begin();
            echo Html::dropDownList("fullRestore", null, ArrayHelper::map($arrFullBackups, "filename", "date"), ['class' => 'form-control', 'label' => 'Sicherungszeitpunkt']);

            echo '<div class="form-group text-center" style="margin-top: 5px">';
                echo Html::submitButton(Yii::t('app', 'Vollbackup wiederherstellen'), 
                                                [
                                                    'class' => 'btn btn-warning', 
                                                    
                                                    'data' => [
                                                        'confirm' => Yii::t('app', 'Are you sure you want to restore this item?'),
                                                        'method' => 'post',
                                                    ]
                                                ]
                                        );

            echo '</div>';
    $form = ActiveForm::end();
    }
    ?>

    <h3>Abteilungs-Backup Wiederherstellen</h3>
    <?php

    if ($handle = opendir(Yii::$app->params["backupDirectory"] . "part/")) {
        $arrPartBackups = array();

        $i=0;
        /* Dies ist der korrekte Weg, ein Verzeichnis zu durchlaufen. */
        while (false !== ($entry = readdir($handle))) {

            if($entry != "." && $entry != ".."){
                $dateTime = substr($entry, 0, 4) . "-" . substr($entry, 4, 2) . "-" . substr($entry, 6, 2) . " " . substr($entry, 9, 2) . ":" . substr($entry, 11, 2) . ":" . substr($entry, 13, 2);
                $posStartName = strpos($entry, "_")+1;
                $partFilename = substr($entry, $posStartName);
                $backup_name = substr($partFilename, 0, strrpos($partFilename, "_"));
                $backup_name = substr($backup_name, 0, strrpos($backup_name, "_"));
                $arrPartBackups[$i]["date"] = $dateTime . ": ".$backup_name."  (".str_replace(".sql", "", substr($entry, strrpos($entry, "_")+1)).")";
                $arrPartBackups[$i]["filename"] = $entry;                
                $i++;
            }
        }
        closedir($handle);
        rsort($arrPartBackups);
        
        $form = ActiveForm::begin();
            echo Html::dropDownList("partRestore", null, ArrayHelper::map($arrPartBackups, "filename", "date"), ['class' => 'form-control', 'label' => 'Sicherungszeitpunkt']);

            echo '<div class="form-group text-center" style="margin-top: 5px">';
                echo Html::submitButton(Yii::t('app', 'Abteilungsbackup wiederherstellen'), 
                                                [
                                                    'class' => 'btn btn-success', 
                                                    'data' => [
                                                        'confirm' => Yii::t('app', 'Are you sure you want to restore this item?'),
                                                        'method' => 'post',
                                                    ]
                                                ]
                                        );

            echo '</div>';
    $form = ActiveForm::end();
    }

    ?>


       


    
    </div>
</div>
