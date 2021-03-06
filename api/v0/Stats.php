<?php

/**
 * Description of Stats
 *
 * @author sean
 */

require_once __DIR__."/../DataAccessObjects/StatDao.class.php";

class Stats {
   
    public static function init()
    {  
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats(:format)/',
                                                        function ($format = ".json") {
            $data = StatDao::getStatistics('');
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getStatistics',null); 
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/stats/:name/',
                                                        function ($name,$format = ".json") {
            if (!is_numeric($name) && strstr($name, '.')) {
                $name = explode('.', $name);
                $format = '.'.$name[1];
                $name = $name[0];
            }
            $data = StatDao::getStatistics($name);
            Dispatcher::sendResponce(null, $data, null, $format);
        }, 'getStatisticByName',null);
    }
}
Stats::init();
