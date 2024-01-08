<?php

namespace app\controllers;

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

}
