<?php

namespace app\controllers;

use app\models\ClassSubject;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class TestController extends Controller
{
    public function actionIndex()
    {
        return "TestController";
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionDb()
    {
        $servername = "mysql";  // Servicename als Adresse
        $username = "usrLfv";
        $password = "T]QaG5WqJZ*hV2Cl";
        $dbname = "lfv";

        // Create connection
        $conn = new \mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT id, txt FROM test";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"]. " - Name: " . $row["txt"] . "<br />";
        }
        } else {
        echo "0 results";
        }
        $conn->close();
        
    }

    public function actionShowCalendar(){
        return $this->render('show-calendar', [
            
        ]);
    }

    public function actionJsoncalendar($start=NULL,$end=NULL,$_=NULL){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
            $times = ClassSubject::find()->limit(2)->all();
        
            $events = array();
        
            foreach ($times AS $time){
              //Testing
              $Event = new \yii2fullcalendar\models\Event();
              $Event->id = $time->id;
              $Event->title = $time->subject . " (".$time->class.")";
              $Event->start = date('Y-m-d\TH:i:s\Z',strtotime('today 7am')); //date('Y-m-d\TH:i:s\Z',strtotime($time->dadata));
              $Event->end = date('Y-m-d\TH:i:s\Z',strtotime('today 8am')); // date('Y-m-d\TH:i:s\Z',strtotime($time->adata));
              $Event->overlap=true;
              $Event->startEditable = true;
              $Event->durationEditable = true;
              $Event->color = 'green';
              $Event->nonstandard = [
                'room' => 'LIT',
                'info' => 'additional Info',
              ];
              $Event->editable = true;
              $Event->startEditable = true;
              $Event->durationEditable = true;

              $events[] = $Event;
            }
        \Yii::warning($events);
            return $events;
          }

}
