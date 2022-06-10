<?php

namespace common\components\MultiLanguage;

use yii\base\InvalidConfigException;
use yii\web\Application;
use yii\web\Request;


class MultiLangRequest extends Request
{

    /**
     * @var
     */
    private $_lang_url;
    /**
     * @var
     */
    private $_lang_from_url;

    /**
     * @return bool|string
     * @throws InvalidConfigException
     */
    public function getUrl()
    {
        //Работает только для web проекта
        if (!\Yii::$app instanceof Application) {
            return '';
        }
		
        if ($this->_lang_url === null) {
            //Содержит строку с префиксом языка
            $this->_lang_url = parent::getUrl();
			
            //Из строки выделяем язык
            $url_list = explode('/', $this->_lang_url);
            $lang_from_url = isset($url_list[1]) ? $url_list[1] : null;

            //Если этого языка нет среди доступных в настройках компонента, значит это не язык, просто возвращаем строку по сути определенную в parent::getUrl();
            if (!in_array($lang_from_url, \Yii::$app->multiLanguage->langs)) {
                return $this->_lang_url;
            }

            //Если этот язык является языком по умолчанию
            if ($lang_from_url == \Yii::$app->multiLanguage->default_lang) {

                return $this->_lang_url;
            }

            $this->_lang_from_url = $lang_from_url;
            //В настройки проекта записываем язык из строки запроса
            \Yii::$app->language = $lang_from_url;
            //Определение home page url, возможно тут надо переделать возможно надо переписать метод Request::getBaseUrl();
            \Yii::$app->homeUrl = \Yii::$app->homeUrl . $lang_from_url;

            //Если в запросе есть указание языка, то его нужно вырезать
            if ($lang_from_url !== null && strpos($this->_lang_url, $lang_from_url) === 1) {
                $this->_lang_url = substr($this->_lang_url, strlen($lang_from_url) + 1);
            }
        }
		
        return $this->_lang_url;
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function getAbsoluteUrl()
    {
        if ($this->_lang_from_url) {
            return $this->getHostInfo() . "/{$this->_lang_from_url}" . $this->getUrl();
        }

        return parent::getAbsoluteUrl();
    }
}