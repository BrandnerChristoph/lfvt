<?php

use app\models\Department;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\ClassSubject */

$this->title = Yii::t('app', 'Restore');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Part-Backup'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="restore-backup-view">

    <h3>Vollbackup Wiederherstellen</h3>
    
    <?php

        if ($handle = opendir(Yii::$app->params["backupDirectory"] . "full/")) {
            $arrFullBackups = array();

            $i=0;
            /* Dies ist der korrekte Weg, ein Verzeichnis zu durchlaufen. */
            while (false !== ($entry = readdir($handle))) {

                if($entry != "." && $entry != ".."){
                    $dateTime = substr($entry, 0, 4) . "-" . substr($entry, 4, 2) . "-" . substr($entry, 6, 2) . " " . substr($entry, 9, 2) . ":" . substr($entry, 11, 2) . ":" . substr($entry, 13, 2);
                    $arrFullBackups[$i]["date"] = $dateTime;
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
                    $arrPartBackups[$i]["date"] = $dateTime . " (".str_replace(".sql", "", substr($entry, strrpos($entry, "_")+1)).")";
                    $arrPartBackups[$i]["filename"] = $entry;                
                    $i++;
                }
            }
            closedir($handle);
            rsort($arrPartBackups);
            
            $form = ActiveForm::begin();
                echo Html::dropDownList("fullRestore", null, ArrayHelper::map($arrPartBackups, "filename", "date"), ['class' => 'form-control', 'label' => 'Sicherungszeitpunkt']);

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
