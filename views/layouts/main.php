<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Url;
use app\widgets\Alert;
use yii\bootstrap4\Nav;
use app\assets\AppAsset;
use yii\bootstrap4\Html;
use yii\bootstrap\Modal;
use yii\bootstrap4\NavBar;
use yii\bootstrap4\Breadcrumbs;
use mdm\admin\components\MenuHelper;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);

    // Nav Callback
        $callback = function($menu){
            $data = eval($menu['data']);
            $req = Yii::$app->request;
            
            $linkOptions = array();
            return [
                'label' => isset($menu['name']) ? $menu['name'] : "Label",
                'url' => [$menu['route']],
                'options' => [
                    'title' => isset($data['title']) ? $data['title'] : "",
                ],
                'icon' => isset($data['icon']) ? $data['icon'] : "",
                'items' => $menu['children'],
                'linkOptions' => $linkOptions,
            ];
        };

    if(Yii::$app->user->isGuest) {
        ////////////////////////////////////////////////////////////////
        // Menü für Gäste
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav pull-right'],
            'encodeLabels' => false,
            'items' => [
                ['label' => 'About', 'url' => ['/site/about']],
                ['label' => 'Login', 'url' => ['/admin/user/login']] 
            ],
        ]);
    } else {  
        ////////////////////////////////////////////////////////////////
        // dynamische Menü (logged in users)
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-left'],
            'encodeLabels' => false,
            'items' => array_merge(MenuHelper::getAssignedMenu(Yii::$app->user->id, 1, $callback, true),
                                    [
                                        '<li class="pull-right">' 
                                        . Html::beginForm(['/site/logout'], 'post', ['class' => 'form-inline'])
                                        . Html::submitButton(
                                            'Logout (' . Yii::$app->user->identity->username . ')',
                                            ['class' => 'btn btn-link logout']
                                        )
                                        . Html::endForm()
                                        .'</li>'
                                    ]),

            
        ]);
    }
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-left">&copy; HTL Waidhofen/Ybbs <?= date('Y') ?></p>
        <!--p class="float-right"><?= Yii::powered() ?></p-->
        <p class="float-right">
            <?= Yii::$app->user->isGuest ? (
                Html::a('Login', ['/admin/user/login']) . " " .
                Html::a('Registrieren', ['/admin/user/signup']) 
            ) : (
                "&nbsp;"
            ) ?>
        </p>
    </div>
</footer>


<div id="fullpage-loader" style="display: none">
    <div class="text-right">
        <button type="button" class="fullpage-loader-close btn btn-link tip" aria-label="close"
                title="close" data-placement="left" onclick="functionFullpageLoaderClose()">
            <span aria-hidden="true"><i class="fa fa-close"></i></span>
        </button>
    </div>
    <div class="loader-content">
        <i id="loader-icon-wait" class="fa fa-spinner fa-pulse"></i>
        <i id="loader-icon-info" class="fa fa-exclamation-triangle" style="display:none; color:red;" ></i>
        
        <div id="loader-error-header" style="font-size: 16pt; font-weight: 600;">
        </div>
        <div id="loader-error">
        </div>
    </div>
    <br />
    <center>
        <button type="button" id="btn-close" class="btn btn-danger" aria-label="close"
                title="close" onclick="functionFullpageLoaderClose()" style="display:none;">
            <i class="fa fa-close"></i> schließen
        </button>
    </center>
    
</div>

<?php
    
        yii\bootstrap4\Modal::begin([
            'title' => '<span id="modalHeaderTitle"></span>',
            //'headerOptions' => ['id' => 'modalHeader'],
            //'closeButton' => ['tag' => 'button', 'label' => 'close'],
            'id' => 'modal',
            'size' => 'modal-lg',
            //keeps from closing modal with esc key or by clicking out of the modal.
            // user must click cancel or X to close
            'options' => ['tabindex' => ''],
            'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
        ]);
        //echo "<div id='modalContent'></div>";
        // Startnachricht
            echo "<div id='modalContent'><div style='text-align:center'><img src='". Url::home() . "img/ajax/ajax-loader.gif'></div></div>";
            echo "<div id='myModalLabel'><div style='text-align:center'></div></div>";
            echo "<div class='modal-footer'></div>";
             
        yii\bootstrap4\Modal::end();

?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

