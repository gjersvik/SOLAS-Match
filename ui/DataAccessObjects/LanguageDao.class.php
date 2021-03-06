<?php

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/../../Common/lib/CacheHelper.class.php";
require_once __DIR__."/../../Common/TimeToLiveEnum.php";
require_once __DIR__."/BaseDao.php";

class LanguageDao extends BaseDao
{
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    }

    public function getLanguage($id)
    {
        $request = "{$this->siteApi}v0/languages/$id";
        return $this->client->call("Language", $request);
    }
    
    public function getLanguages()
    {
        $languages = CacheHelper::getCached(CacheHelper::LANGUAGES, TimeToLiveEnum::MONTH, 
                function($args){
                    $request = "{$args[1]}v0/languages";
                    return $args[0]->call(array("Language"), $request);
                },
            array($this->client, $this->siteApi));
        return $languages;
    }

    public function getActiveLanguages()
    {
        $languages = null;
        $request = "{$this->siteApi}v0/languages/getActiveLanguages";
        $languages = $this->client->call(array('Language'), $request);
        return $languages;
    }
    
    public function getActiveSourceLanguages()
    {
        $languages = null;
        $request = "{$this->siteApi}v0/languages/getActiveSourceLanguages";
        $languages = $this->client->call(array('Language'), $request);
        return $languages;
    }
     
    public function getActiveTargetLanguages()
    {
        $languages = null;
        $request = "{$this->siteApi}v0/languages/getActiveTargetLanguages";
        $languages = $this->client->call(array('Language'), $request);
        return $languages;
    }
    
    public function getLanguageByCode($code)
    {
        $request = "{$this->siteApi}v0/languages/getByCode/$code";
        return $this->client->call("Language", $request);
    }
}
