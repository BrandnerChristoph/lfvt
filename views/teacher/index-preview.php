<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Teachers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-index">

    <div class="col-lg-12 text-right">
        <?= Html::a("aktualisieren", Yii::$app->request->url, ["class" => "btn btn-primary border"] ) ?>
                
    </div>
    <h1><?= Html::encode($this->title) ?></h1>

    <!--div class="col-lg-12">
        <b>Abteilungen</b>
        <?= Html::checkboxList("Abteilung", null, ['IT', 'ETEC']); ?>
    </div>
    
    <div class="col-lg-12">
        <b>Lehrertypen</b>
        <?= Html::checkboxList("Abteilung", null, ['Allgemeinbildung', 'Fachtheorie', 'Werkstatt']); ?>
    </div-->


    <div style="display:none">
        <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'filename' => 'Lehrerliste',
                'columnSelectorOptions' => [
                    'icon' => '<i class="fa fa-list"></i>',
                ],
                'dropdownOptions' => [
                    'label' => 'Export',
                    'class' => 'btn btn-outline-secondary btn-default'
                ],
                
            ]) ?>
    </div>

    <script>
        function showTeacherOverview(inTeacherId){
            //alert(inTeacherId);
            //var callUrl = <?= Url::to(['/teacher/print-lesson'])?>;
            //var fullUrl = callUrl + "?id=" + inTeacherId;
            var fullUrl = "index.php?r=teacher/print-lesson&id=" + inTeacherId;
                                    
            //alert(fullUrl)
            window.open(fullUrl,
                        "Lehrerverwendung",
                        "directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no,width=750,height=900,top=100, left=200");
        }
    </script>
    

    <?php //Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'initial',
                'format' => 'raw',
                'value' => function ($model) {
                    $strName = trim ($model->name . " " . $model->firstname);
                    !empty($strName) ? $strName = " <small>(" . $strName . ")</small>" : $strName = "";
                    //return trim("<b>" . $model->initial . "</b>" . $strName);

                    
                    return Html::a(trim("<b>" . strtoupper($model->initial) . "</b>" . $strName), '#', ['onClick' => 'showTeacherOverview("'.$model->id.'");']);
                }
            ],
            //'initial',            
            [
                'attribute' => 'Stunden',
                'format' => 'raw',
                'value' => function ($model) {
                    $strReturn = "";
                    $curHours = $model->hours;
                    $curTeachingHours = $model->teachingHours;
                    $min = -1;
                    $max = -1;
                    foreach($model->teacherWishlists as $listItem){
                        if(!empty($listItem['hours_min']))
                            $min = $listItem['hours_min'];
                        if(!empty($listItem['hours_max']) && ($listItem['hours_min'] != $listItem['hours_max']))
                            $max = $listItem['hours_max'];
                    }

                    if($min >= 0 && $max >=0){
                        if($max < $curHours){
                            $strReturn = "<center><div class='btn-sm btn-danger'>".$curHours."</div></center>";
                        } elseif($min > $curHours){
                            $strReturn = "<center><div class='btn-sm btn-warning'>".$curHours."</div></center>";
                        } else {
                            $strReturn = "<center><div class='btn-sm btn-success'>".$curHours."</div></center>";
                        }
                    } else {
                        $strReturn = "<center>".$curHours."</center>";
                    }  
                    
                    return $strReturn ;
                },
            ],
            [
                'attribute' => 'Wert',
                'value' => function($model){
                    return $model->teachingHours;
                },
            ],
            [
                'attribute' => 'Stunden',
                'label' => 'Std. in Abt.',
                'format' => 'raw',
                'value' => function ($model) use($department) {                    
                    return "<center>".$model->fetchHoursByDepartment($department) .
                                    " (<small>" . $model->fetchTeachingHoursByDepartment($department) . ")</small></center>";
                      
                },
            ],
            /*
            'name',
            'firstname',
            'email_1:email',
            */
            //'email_2:email',
            //'phone',
            //'mobile',
            //'created_at',
            //'updated_at',
            
            [
                'attribute' => 'Stundenwunsch',
                'format' => 'raw',
                'value' => function ($model) {
                    $strHours = "";
                    foreach($model->teacherWishlists as $listItem){
                        if(!empty($listItem['hours_min']))
                            $strHours = $listItem['hours_min'];
                        if(!empty($listItem['hours_max']) && ($listItem['hours_min'] != $listItem['hours_max']))
                            $strHours .= " - " . $listItem['hours_max'];
                        
                        $list[] = $listItem['info'];
                    }
                    return $strHours; 
                },
            ],

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php // Pjax::end(); ?>

</div>
