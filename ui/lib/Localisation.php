<?php

require_once __DIR__.'/../../Common/lib/CacheHelper.class.php';
require_once __DIR__.'/../../Common/TimeToLiveEnum.php';
require_once __DIR__.'/../localisation/Strings.php';

class Localisation {
    
    public static $currentLanguage;
    private static $doc;
    private static $xPath;
    private static $ready = false;
    
    public static function init()
    {   
        self::$ready = true;      
        $userLang = UserSession::getUserLanguage();
        if(!$userLang || strcasecmp(Settings::get('site.default_site_language_code'), $userLang) === 0) {
            self::$currentLanguage = null;
        } else {
            self::$currentLanguage = $userLang;
        }
        self::$doc = new DOMDocument();
        
        if(is_null(self::$currentLanguage)) {
            self::$doc->loadXML(CacheHelper::getCached(CacheHelper::SITE_LANGUAGE, TimeToLiveEnum::HOUR, 'Localisation::fetchTranslationFile', 'strings.xml'));
        } else {
            self::$doc->loadXML(CacheHelper::getCached(CacheHelper::SITE_LANGUAGE."_".self::$currentLanguage, TimeToLiveEnum::HOUR, 'Localisation::fetchTranslationFile', "strings_".self::$currentLanguage.".xml"));
        }
    }
    
    public static function getStrings(){
        if(!self::$ready) self::init();
        return self::$doc->saveXML(self::$doc->firstChild);
    }


    public static function getTranslation($stringId)
    {
        if(!self::$ready) self::init();
        self::$xPath = new DOMXPath(self::$doc);
        $stringElement = self::$xPath->query("/resources/string[@name='$stringId']");
        if($stringElement->length == 0) {
            error_log("Could not find/load: $stringId");
            return "Could not find/load: $stringId";
        }
        $foundNode = self::$doc->saveXML($stringElement->item(0));
        $foundNode = substr($foundNode, strpos($foundNode, ">")+1);
        return substr($foundNode,0,strrpos($foundNode,"<"));
    }
    
    public static function fetchTranslationFile($lang = "strings.xml")
    {   
        return file_get_contents(__DIR__."/../localisation/$lang");
    }
    
    public static function loadTranslationFiles()
    {
        $matches = array();
        $locales = array();
        $filePaths = glob(__DIR__."/../localisation/strings_*.xml");
        $langDao = new LanguageDao();
        $locales[] = $langDao->getLanguageByCode(Settings::get('site.default_site_language_code')); 
        foreach($filePaths as $filePath) {
            preg_match('/_(.*)\.xml/', realpath($filePath), $matches);
            $lang = CacheHelper::getCached(CacheHelper::LOADED_LANGUAGES."_$matches[1]", TimeToLiveEnum::QUARTER_HOUR, array($langDao, 'getLanguageByCode'), $matches[1]);
            if(!in_array($lang, $locales)) $locales[] = $lang;
        }   
        return $locales;        
    }
    
}
