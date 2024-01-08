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

    public function actionProcessToNextYear(){
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
            */

            // Fächerkombinationen in temporärer Tabelle erstellen
                Yii::$app->db->createCommand("DELETE from temp_subjectPerYear;")->execute();
                echo "<br /> - alte Klassen/Fächer Kombination wurde aus der DB entfernt";

                $tempSaveSubjectClass = "INSERT INTO temp_subjectPerYear (class, subject) 
                                        SELECT distinct(CONCAT(LEFT(class,1), 'x', SUBSTRING(class,3))), subject
                                            FROM class_subject
                                            join school_class on class_subject.class = school_class.id
                                            WHERE class REGEXP '^[1-9](A)'
                                            AND school_class.department NOT IN ('TEMP');";

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
                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('3AKME', '1. FS-Mechatronik Fussball', 'ME', '', '?', '', unix_timestamp(), unix_timestamp());")->execute();
                                                
                Yii::$app->db->createCommand("INSERT INTO school_class (`id`, `classname`, `department`, `period`, `class_head`, `info`, `updated_at`, `created_at`) 
                                                VALUES ('2AAME', '2. Sem. Aufbaulehrgang', 'AUF', '', '?', '', unix_timestamp(), unix_timestamp());")->execute();

            /*************************************************************
                Fächer bereinigen
            */

            // Fächer löschen, die im neuen Schuljahr nicht mehr unterrichtet werden.

                Yii::$app->db->createCommand("DELETE class_subject from class_subject 
                                                JOIN school_class on class_subject.class = school_class.id 
                                                WHERE  
                                                    (class_subject.class != '5AHIT')
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
            
            $transaction->commit();
        } catch(Exception $e){
            Yii::error($e->getMessage() . " (Line: " . $e->getLine() . ")", "BN");
            echo "<br /><b>FEHLER: Rollback wird vorgenommen!</b>";
            $transaction->rollBack();
        }
    }

    private function MoveClass(){

    }

}