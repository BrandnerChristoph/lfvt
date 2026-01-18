<?php

namespace app\controllers;

use app\models\ClassSubject;
use Yii;
use app\models\Department;
use app\models\SchoolClass;
use app\models\search\DepartmentSearch;
use Exception;
use yii\db\Transaction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HelperController implements operations to process settings
 */
class HelperController extends Controller
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

    private function createFullbackup(){
        try{
            $backupname="Gesamtbackup-Jahreswechsel";
            
            $filepath = Yii::$app->params["backupDirectory"] . "full/". date("Ymd-His", time())  . "_" . $backupname . "_" . Yii::$app->user->id . ".sql";
            $tables = array();
            $tables = Yii::$app->db->schema->getTableNames();
            $return = '';

            foreach ($tables as $table) {

                $result = Yii::$app->db->createCommand('SELECT * FROM ' . $table)->query();    
                $return.= "DELETE FROM " . $table . ";\n";            
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
    }

    public function actionProcessToNextYear(){

        // Full-Backup
        $this->createFullbackup();

        $transaction = Yii::$app->db->beginTransaction();
        try{
            echo "Verarbeitung starten";
            /*
            folgende Aktionen werden mit Hilfe von DB Transaction umgesetzt
                - Fächer/Klassenkombinationen des aktuellen Jahres analysieren und in temp_subjectPerYear schreiben
                - Klassenaufsteigen lassen (außer Abteilung TEMP)
                - ausgeschiedene Klassen löschen (6. Jahrgang der Höheren Abteilungen, ...)
                - neue "erste" Klassen erstellen (Höhere Abteilungen, Fachschulen)
                - Fächer löschen, die im neuen Schuljahr nicht mehr unterrichtet werden.
                - Fächer hinzufügen, die im neuen Schuljahr HINZUKOMMEN
                - Stundenausmaß aus dem Vorjahr übernehmen (Info mehr/weniger Stunden)
            */

            // Fächerkombinationen in temporärer Tabelle erstellen
                Yii::$app->db->createCommand("DELETE from temp_subjectPerYear;")->execute();
                echo "<br /> - alte Klassen/Fächer Kombination wurde aus der DB entfernt";

                $tempSaveSubjectClass = "INSERT INTO temp_subjectPerYear (class, subject, t1, t2, t3, t4) 
                                            SELECT distinct(CONCAT(LEFT(class,1), 'x', SUBSTRING(class,3))), 
                                                subject,
                                                SUM(class_subject.hours),
                                                CONCAT(LEFT(class,1), 'x', SUBSTRING(class,3), class_subject.subject),
                                                COUNT((class_subject.class)),
                                                (SUM(class_subject.hours) / COUNT((class_subject.class)))
                                            FROM class_subject
                                                join school_class on class_subject.class = school_class.id
                                            WHERE class REGEXP '^[1-9](A)'
                                                AND school_class.department NOT IN ('TEMP')
                                                GROUP BY CONCAT(LEFT(class,1), 'x', SUBSTRING(class,3), subject);";

                Yii::$app->db->createCommand($tempSaveSubjectClass)->execute();
                echo "<br /> - Klassen/Fächer Kombination wurde in DB (Tabelle: temp_subjectPerYear) gespeichert";

            // Klassen aufsteigen lassen
                Yii::$app->db->createCommand("UPDATE school_class 
                                                SET id = CONCAT(LEFT(id, 1)+1, SUBSTRING(id,2)), classname = CONCAT(LEFT(classname, 1)+1, SUBSTRING(classname,2)) 
                                                WHERE classname REGEXP '^[1-9]'
                                                AND department not in ('TEMP') 
                                                ORDER BY id desc;")->execute();
                echo "<br /> - Klassen und Einheiten aktualisiert (ins neue Schuljahr übernommen).";

                
            // Abschlussklassen der höheren Abteilungen löschen
                Yii::$app->db->createCommand("UPDATE school_class set annual_value = 0 WHERE id REGEXP '^5[A-Z]H'; ")->execute();
                echo "<br /> - 5. Klassen der höheren Abteilungen mit einem Jahreswert von 0 setzen";

            // Abschlussklassen der höheren Abteilungen löschen
                Yii::$app->db->createCommand("DELETE from school_class WHERE id REGEXP '^6[A-Z]H'; ")->execute();
            // Abschlussklassen der Fachschulen löschen
                Yii::$app->db->createCommand("DELETE from school_class WHERE id REGEXP '^7[A-Z](A|K)'; ")->execute();
                Yii::$app->db->createCommand("DELETE from school_class WHERE id REGEXP '^5AFME'; ")->execute();
                
                echo "<br /> - ausgeschiedene Klassen gelöscht.";

            // neue 1. Klassen erstellen
                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('1AHIT', '1.a Informationstechnologie', 'IT', '', '?', '', unix_timestamp(), unix_timestamp());")->execute();

                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('1AHET', '1.a Elektrotechnik', 'ETEC', '', '?', '', unix_timestamp(), unix_timestamp());")->execute();

                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('1AHMBA', '1.a Maschinenbau', 'AUT', '', '?', '', unix_timestamp(), unix_timestamp());")->execute();

                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('1BHMBA', '1.b Maschinenbau', 'AUT', '', '?', '', unix_timestamp(), unix_timestamp());")->execute();

                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('1AHWIM', '1.a Wirtschaftsingenieure', 'WING', '', '?', '', unix_timestamp(), unix_timestamp());")->execute();

                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('1BHWIM', '1.b Wirtschaftsingenieure', 'WING', '', '?', '', unix_timestamp(), unix_timestamp());")->execute();

                // Zusatzklassen für Abteilungen: AUF, ME
                echo "<br /> - neue erste Klassen erstellt.";

                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('1AFME', '1. FS-Mechatronik Fussball', 'ME', '', '?', '', unix_timestamp(), unix_timestamp());")->execute();
                                                
                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `annual_value`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('2AAME', '2. Sem. Aufbaulehrgang', 'AUF', '', '0.5', '?', '', unix_timestamp(), unix_timestamp());")->execute();
                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `annual_value`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('3AKME', '3. Sem. Kolleg Mechatronik', 'AUF', '', '0.5', '?', '', unix_timestamp(), unix_timestamp());")->execute();

            /*************************************************************
                Fächer bereinigen
            */

            // Fächer löschen, die im neuen Schuljahr nicht mehr unterrichtet werden.
                /*
                Script für 2024
                Yii::$app->db->createCommand("DELETE class_subject from class_subject 
                                                JOIN school_class on class_subject.class = school_class.id 
                                                WHERE  
                                                    (class_subject.class != '5AHIT')
                                                    AND (class_subject.class != '4AHET')
                                                    AND (class_subject.class not in (select id from school_class where department='TEMP'))
                                                    AND concat(LEFT(class, 1), 'x', SUBSTRING(class,3), subject) NOT IN (SELECT concat(temp_subjectPerYear.class, temp_subjectPerYear.subject) FROM temp_subjectPerYear);")->execute();
                                                    */
                Yii::$app->db->createCommand("DELETE class_subject from class_subject 
                    JOIN school_class on class_subject.class = school_class.id 
                    WHERE  
                        (class_subject.class not in (select id from school_class where department='TEMP'))
                        AND concat(LEFT(class, 1), 'x', SUBSTRING(class,3), subject) NOT IN (SELECT concat(temp_subjectPerYear.class, temp_subjectPerYear.subject) FROM temp_subjectPerYear);")->execute();


            // Fächer hinzufügen, die im neuen Schuljahr HINZUKOMMEN
                Yii::$app->db->createCommand("INSERT INTO `class_subject` (`id`, `class`, `subject`, `group`, `hours`, `teacher`, `classroom`, `updated_at`, `created_at`) 
                                    SELECT concat(RIGHT(UNIX_TIMESTAMP(),5), school_class.id, temp_subjectPerYear.subject), 
                                            school_class.id, 
                                            temp_subjectPerYear.subject, 
                                            '', 
                                            0, 
                                            '?', 
                                            '',
                                            UNIX_TIMESTAMP(), 
                                            UNIX_TIMESTAMP()  
                                        FROM temp_subjectPerYear
                                        JOIN school_class ON (concat(left(temp_subjectPerYear.class,1) ,SUBSTRING(temp_subjectPerYear.class,3)) = concat(LEFT(school_class.id, 1),SUBSTRING(school_class.id,3)))
                                        WHERE
                                            concat(temp_subjectPerYear.class, temp_subjectPerYear.subject) not IN (select concat(LEFT(class, 1), 'x', SUBSTRING(class,3), subject)  FROM class_subject);")->execute();

                // Update Stundenausmaß (Input aus dem Vorjahr)
                $this->actionUpdateHours();

            $transaction->commit();
        } catch(Exception $e){
            Yii::error($e->getMessage() . " (Line: " . $e->getLine() . ")", "BN");
            echo "<br /><b>FEHLER: Rollback wird vorgenommen!</b>";
            $transaction->rollBack();
        }
    }

    public function actionUpdateHours(){
        $arrInputHourSum = Yii::$app->db->createCommand("SELECT * from temp_subjectPerYear;")->queryAll();

        foreach($arrInputHourSum as $item){
            $updatQuery = "";
            if(substr($item['class'], 0, 1) == "1" ||
                $item['class'] == "2AAME" ||
                $item['class'] == "3AKME"){
                // 1. Klassen höhere Abteilung
                $updatQuery = "UPDATE class_subject set hours = " . $item['t4'] . " 
                                    WHERE subject = '" . $item['subject'] . "'
                                        AND ((class = '".substr($item['class'], 0, 1)."A".substr($item['class'], 2)."') OR (class = '".substr($item['class'], 0, 1)."B".substr($item['class'], 2)."') OR (class = '".substr($item['class'], 0, 1)."C".substr($item['class'], 2)."'));";
                //echo $item['subject'] . " - " . $item['t4']. " (".$updatQuery.") <br />";
                
                if(!empty($updatQuery))
                    Yii::$app->db->createCommand($updatQuery)->execute();
                
            } else {
                $hourDiff = 0;
                // get last years hours
                $arrClassSub = ClassSubject::find()
                                    ->andFilterWhere(['subject' => $item["subject"]])
                                    ->andWhere("`class` IN ('" . substr($item['class'], 0, 1)."A".substr($item['class'], 2) . "', '" . substr($item['class'], 0, 1)."B".substr($item['class'], 2) . "', '" . substr($item['class'], 0, 1)."C".substr($item['class'], 2) . "')")
                                    ->All();

                foreach($arrClassSub as $lastYearItem){
                    $hourDiff = $item['t4'] - $lastYearItem->hours;
                    if($hourDiff != 0){
                        $updatQuery = "INSERT INTO `class_subject` (`id`, `class`, `subject`, `group`, `hours`, `teacher`, `classroom`, `updated_at`, `created_at`) 
                                            VALUES
                                            ('" . uniqid() . "', 
                                                    '" . $lastYearItem->class . "',  
                                                    '" . $lastYearItem->subject . "', 
                                                    '', 
                                                    " . $hourDiff . ", 
                                                    '?', 
                                                    '',
                                                    UNIX_TIMESTAMP(), 
                                                    UNIX_TIMESTAMP());";
    
    
                        //echo $item['subject'] . " - " . $item['t4']. " (".$updatQuery.") <br />";
                        
                        if(!empty($updatQuery))
                            Yii::$app->db->createCommand($updatQuery)->execute();
                        
                    }     
                }                         
            }            
        }
    }

    private function MoveClass(){

    }

}