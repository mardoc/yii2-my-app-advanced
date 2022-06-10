<?php
	/**
	 * Created by Mardoc.
	 * Web : http://mardoc.net
	 * Mail: works@mardoc.net
	 * DateTime: 17.05.2022 20:29
	 */
	
	namespace common\components\MultiLanguage\Widget;
	
	
	use \yii\base\Widget;
	use yii\helpers\Url;
	use yii\web\View;
	
	/**
	 *
	 * @property array $languages of [ 'lang-code'=>'label']
	 *
	 * @property-write string $languageParam  parameter link language page
	 *
	 * @property string $assetUrl
	 */
	class LanguageSelect extends Widget
	{
		public $languages = ['en'=>'Eng','uk'=>'Ua','lt-LT'=>'Lt'];

		public $languageParam = null;
		
		private $assetUrl = '';
		
		public function init(){
			parent::init();
			if ($this->languageParam === null) {
				$this->languageParam = 'lang';
			}
			
			\common\components\MultiLanguage\Widget\LanguageSelectAsset::register($this->view);
			
			$this->assetUrl = $this->view->assetBundles[LanguageSelectAsset::className()]->baseUrl;
			
			$this->view->registerJs(<<< JS
$('.header-lang__text').click(function () {
	$('.header-lang__list').toggleClass('open');
});
JS
, View::POS_READY);
		
		}
		
		/**
		 * @return string
		 */
		public function run(){
			$items = '';
			foreach ($this->languages as $language=>$label ) {
				if ($language == \Yii::$app->language){
					continue;
				}
				$items.= '<li class="header-lang__item">' .\yii\helpers\Html::tag('a',
						'<img src="'.$this->assetUrl.'/img/'.$language.'.svg" class="header-lang__img" alt="'.$label.'">'.$label,
						[
							'href'=> Url::current([$this->languageParam => $language]),
							'class'=> 'header-lang__link'
						]
					). '</li>';
			}
			return '<div class="header-lang">
						<p class="header-lang__text">
							<span class="header-lang__link"><img src="'.$this->assetUrl.'/img/'.\Yii::$app->language.'.svg" class="header-lang__img" alt="'.$label.'"></span>
						</p>
						<ul class="header-lang__list">'.$items.'</ul>
					</div>';
		}
	}