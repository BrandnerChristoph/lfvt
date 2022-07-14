<?php

use app\models\Department;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClassSubject */

$this->title = Yii::t('app', 'Part-Backup');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Part-Backup'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="backup-part-view">

    <h1><?= Html::encode($this->title) ?></h1>
    

        <?php $form = ActiveForm::begin(); ?>
            <?php

                $listDepartments = Department::find()->All();

                echo "<h3>Abteilungen</h3>";
                foreach ($listDepartments as $item) {
                    echo "<div class='col-lg-3'>";
                        echo Html::checkbox($item->id, false, ['label' => $item->name]);
                    echo "</div>";
                }

            ?>  
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Backup'), ['class' => 'btn btn-success']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    
</div>
