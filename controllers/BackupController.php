<?php

namespace app\controllers;

use app\models\ClassSubject;
use Yii;
use app\models\Department;
use app\models\SchoolClass;
use app\models\search\DepartmentSearch;
use Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepartmentController implements the CRUD actions for Department model.
 */
class BackupController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * creates a full backup of the database
     * @return mixed
     */
    public function actionCreateFull()
    {
        try{
            $filepath = Yii::$app->params["backupDirectory"] . "full/". date("Ymd-His", time()) . "_" . Yii::$app->user->id . ".sql";
            $tables = array();
            $tables = Yii::$app->db->schema->getTableNames();
            $return = '';

            foreach ($tables as $table) {

                $result = Yii::$app->db->createCommand('SELECT * FROM ' . $table)->query();
        
                //$return.= '\nDROP TABLE IF EXISTS ' . $table . ';\n';
        
                //$row2 = Yii::$app->db->createCommand('SHOW CREATE TABLE ' . $table)->queryAll();
                //$test = $row2[0]['Create Table'];
                //echo ($test . "<hr />");
                //echo($row2);
                //exit(0);
                //$return .= htmlentities($test) . ";\n\n";
                //$return.= $row2[0]['Create Table'] . ";\n\n";
        
                foreach ($result as $row) {
        
                    $return.= 'REPLACE INTO ' . $table . ' VALUES(';
        
                    foreach ($row as $data) {
        
                        $data = addslashes($data);
        
                        // Updated to preg_replace to suit PHP5.3 +
        
                        $data = preg_replace("/\n/", "\\n", $data);
        
                        if (isset($data)) {    
                            $return.= '"' . $data . '"';    
                        } else {    
                            $return.= '""';    
                        }
        
                        $return.= ',';    
                    }
        
                    $return = substr($return, 0, strlen($return) - 1);    
                    $return.= ");\n";    
                }    
                $return.="\n\n\n";    
            }
        
            //save file    
            $handle = fopen($filepath, 'w+');    
            fwrite($handle, $return);    
            fclose($handle);

            Yii::$app->session->setFlash('success', "Voll-Backup wurde erfolgreich vorgenommen.");
        } catch(Exception $ex){
            Yii::$app->session->setFlash('error', "Voll-Backup fehlgeschlagen. " . $ex->getMessage() . " (Line: " . $ex->getLine() . ")");
        }
        return $this->redirect(Yii::$app->request->referrer);

    }

    
    public function actionViewPartBackupSettings()
    {
        if ($this->request->isPost) {
            $arrDepartment = array();

            if(!is_null($this->request->Post("IT")))
                $arrDepartment[] = "IT";
            if(!is_null($this->request->Post("ETEC")))
                $arrDepartment[] = "ETEC";
            if(!is_null($this->request->Post("WING")))
                $arrDepartment[] = "WING";
            if(!is_null($this->request->Post("AUT")))
                $arrDepartment[] = "AUT";

            if (sizeof($arrDepartment) > 0) {
                self::actionCreateDepartment($arrDepartment);
                Yii::$app->session->setFlash('success', "Sicherung der Abteilung(en) <b>" . implode(", ", $arrDepartment) . "</b> vorgenommen."); 
            } else {
                Yii::$app->session->setFlash('warning', "Es wurde keine Abteilung gewählt."); 
            }
        }
        
        return $this->render('view-part-backup-settings', [
            
        ]);
    }
    
    /**
     * creates backup for named Departments
     * @return mixed
     */
    public function actionCreateDepartment(array $arrDepartment)
    {
        try{

            $filepath = Yii::$app->params["backupDirectory"] . "part/". date("Ymd-His", time()) . "_" . Yii::$app->user->id . "_". implode('-', $arrDepartment) . ".sql";
            $return = '';

            // Class Subject Table
                $result = Yii::$app->db->createCommand('SELECT class_subject.* FROM `class_subject` 
                                                        join school_class ON class_subject.class = school_class.id 
                                                        WHERE school_class.`department` in ("' . implode('", "', $arrDepartment) . '")' )->query();
                
                foreach ($result as $row) {
                    $return.= 'REPLACE INTO class_subject VALUES(';
        
                    foreach ($row as $data) {            
                        $data = addslashes($data);
        
                        // Updated to preg_replace to suit PHP5.3 +
        
                        $data = preg_replace("/\n/", "\\n", $data);
                        if (isset($data)) {    
                            $return.= '"' . $data . '"';    
                        } else {    
                            $return.= '""';    
                        }
                        $return.= ',';    
                    }
        
                    $return = substr($return, 0, strlen($return) - 1);    
                    $return.= ");\n";    
                }    
                $return.="\n\n\n";    
        
            //save file    
            $handle = fopen($filepath, 'w+');    
            fwrite($handle, $return);    
            fclose($handle);

            Yii::$app->session->setFlash('success', "Backup der Abteilung(en) <b>" . implode(", ", $arrDepartment) . "</b> wurde vorgenommen.");
        } catch(Exception $ex){
            Yii::$app->session->setFlash('error', "Backup fehlgeschlagen. " . $ex->getMessage());
        }
        return $this->redirect(Yii::$app->request->referrer);        
    }

    public function actionRestore()
    {
        $restoreData = "";
        $restoreType = "";
        if ($this->request->isPost) {
            if(!is_null($this->request->Post("fullRestore"))){
                $restoreType = "Vollbackup";
                $restoreData = Yii::$app->params["backupDirectory"] . "full/".$this->request->Post("fullRestore");
            } elseif(!is_null($this->request->Post("partRestore"))){
                $restoreType = "Abteilungsbackup";
                $restoreData = Yii::$app->params["backupDirectory"] . "part/".$this->request->Post("fullRespartRestoretore");
            }
                
            Yii::$app->session->setFlash('success', "Backup (".$restoreType." | Datei: " . $restoreData . ") wurde wiederhergestellt. ES PASSIERT NOCH NIX");

        }
        return $this->render('restore', [
            
        ]);
    }
}