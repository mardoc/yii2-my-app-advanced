<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
    $menuItems = [
        ['label' => Yii::t('app','Home'), 'url' => ['/site/index']],
        ['label' =>  Yii::t('app','About'), 'url' => ['/site/about']],
        ['label' =>  Yii::t('app','Write to us'), 'url' => ['/site/write-to-us']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' =>  Yii::t('app','Signup'), 'url' => ['/user/login/signup']];
        $menuItems[] = ['label' =>  Yii::t('app','Login'), 'url' => ['/user/login/login']];
        
    } else {
    	if (Yii::$app->user->can(\common\rbac\Rbac::PERMISSION_ADMIN_PANEL)){
			$menuItems[] = ['label' =>  Yii::t('app','Admin Panel'), 'url' => '/admin'];
		}
    	
        $menuItems[] = '<li>'
            . Html::beginForm(['/user/login/logout'], 'post', ['class' => 'form-inline'])
            . Html::submitButton(
				Yii::t('app','Logout').' (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
		$menuItems[] = 	\common\components\MultiLanguage\Widget\LanguageSelect::widget([
			'languages' => [
				'uk'=>'Ua',
				'en'=>'En',
			],
			'languageParam'=>'lang'
		]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ml-auto'],
        'items' => $menuItems,
    ]);
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
        <p class="float-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
        <p class="float-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
