<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers : Content-Type');
header('Access-Control-Allow-Methods : GET, POST, PUT, DELETE');


require __DIR__."/vendor/autoload.php";
mb_internal_encoding("UTF-8");

\DrSlump\Protobuf::autoload();

require_once __DIR__."/../Common/Settings.class.php";
require_once __DIR__."/../Common/lib/ModelFactory.class.php";
require_once __DIR__."/../Common/lib/BadgeTypes.class.php";
require_once __DIR__."/../Common/lib/APIHelper.class.php";
require_once __DIR__."/../Common/lib/UserSession.class.php";
require_once __DIR__."/../Common/HttpMethodEnum.php";
require_once __DIR__."/../Common/HttpStatusEnum.php";


class Dispatcher {
    
    private static $apiDispatcher = null;
    private static $oauthServer = null; 
    private static $oauthRequest = null;
            
    public static function  getDispatcher()
    {
        if (Dispatcher::$apiDispatcher == null) {
            Dispatcher::$apiDispatcher = new Slim(array(
                 'debug' => true
                ,'mode' => 'development' // default is development. TODO get from config file, or set
                // in environment...... $_ENV['SLIM_MODE'] = 'production';
            ));
            $app = Dispatcher::$apiDispatcher;
            Dispatcher::$apiDispatcher->configureMode('production', function () use ($app) {
                $app->config(array(
                    'log.enable' => true,
                    'log.path' => '../../logs', // Need to set this...
                    'debug' => false
                ));
            });

            $app->configureMode('development', function () use ($app) {
                $app->config(array(
                    'log.enable' => false,
                    'debug' => true
                ));
            });
        }
        return Dispatcher::$apiDispatcher;
    }
    

    public static function init()
    {
       $path = Dispatcher::getDispatcher()->request()->getResourceUri();
       $path = explode("/", $path);
       $path =$path[1];
       $providerNames = Dispatcher::readProviders("$path/");
       Dispatcher::autoRequire($providerNames,"$path/");
       self::initOAuth();
       Dispatcher::getDispatcher()->run();  
    }
    
    private static function initOAuth() {
        self::$oauthRequest = new League\OAuth2\Server\Util\Request();
        self::$oauthServer = new League\OAuth2\Server\Authorization(
            new League\OAuth2\Server\Storage\PDO\Client(),
            new League\OAuth2\Server\Storage\PDO\Session(),
            new League\OAuth2\Server\Storage\PDO\Scope()
        );
        self::$oauthServer->setAccessTokenTTL(Settings::get('site.oauth_timeout'));
        self::$oauthServer->addGrantType(new League\OAuth2\Server\Grant\Password(self::$oauthServer));

    }
    
    public static function getOauthServer()
    {
        return self::$oauthServer;
    }
    
    public static function sendResponce($headers, $body, $code = 200, $format = ".json",$oauthToken=null)
    {
        header('Access-Control-Allow-Origin: *');
        $response = Dispatcher::getDispatcher()->response();
        $apiHelper = new APIHelper($format);
        $response['Content-Type'] = $apiHelper->getContentType();
        $body = $apiHelper->serialize($body);
        $token = $apiHelper->serialize($oauthToken);
        $response["X-Custom-Token"] = base64_encode($token);
        if ($headers != null) {
            foreach ($headers as $key => $val) {
                $response[$key] = $val;
            }
        }
        
        if ($code != null) {
            $response->status($code);
        }
        
        $response->body($body);        
    }
    
    public static function register($httpMethod, $url, $function, $middleware = null)
    {        
        switch ($httpMethod) {
            
            case HttpMethodEnum::DELETE: {
                Dispatcher::getDispatcher()->delete($url,$middleware, $function);
                break;
            }
            
            case HttpMethodEnum::GET: {
                Dispatcher::getDispatcher()->get($url,$middleware, $function);
                break;
            }
            
            case HttpMethodEnum::POST: {
                Dispatcher::getDispatcher()->post($url,$middleware, $function);
                break;
            }
            
            case HttpMethodEnum::PUT: {
                Dispatcher::getDispatcher()->put($url,$middleware, $function);
                break;
            }
        }
    }
    
    public static function registerNamed($httpMethod, $url, $function, $name,  $middleware = "Middleware::isloggedIn")
    {        
        switch ($httpMethod) {
            
            case HttpMethodEnum::DELETE: {
                
                Dispatcher::getDispatcher()->delete($url, $middleware, $function)->name($name);
                break;
            }
            
            case HttpMethodEnum::GET: {
                Dispatcher::getDispatcher()->get($url,  $middleware, $function)->name($name);
                break;
            }
            
            case HttpMethodEnum::POST: {
                Dispatcher::getDispatcher()->post($url,  $middleware, $function)->name($name);
                break;
            }
            
            case HttpMethodEnum::PUT: {
                Dispatcher::getDispatcher()->put($url,  $middleware, $function)->name($name);
                break;
            }
        }
    }

    
    public static function clenseArgs($index, $httpMethod=null, $default=null)
    {
        $req = Dispatcher::getDispatcher()->request();
        switch ($httpMethod){
            case HttpMethodEnum::GET : {
                 $result = $req->get($index);
                 return is_null($result) ? $default : $result; 
            }
            case HttpMethodEnum::POST : {
                $result = $req->post($index);
                return is_null($result) ? $default : $result; 
            }
            case HttpMethodEnum::PUT : {
               $result = $req->put($index);
               return is_null($result) ? $default : $result; 
            }
            default : {
               $result = $req->params($index);
               return is_null($result) ? $default : $result; 
            }
        }
    }
    
    private static function autoRequire(array $providers, $root = "providers")
    {
        foreach ($providers as $provider) {
            require_once $root.$provider.".php";
        }
    }
    
    private static function readProviders($root)
    {
        $temp = scandir($root);
        $ret = array();
        foreach ($temp as $provider) {
            if ($provider != "." && $provider != ".." && strncmp($provider, ".", 1)) {
                $ret[] = substr($provider, 0, sizeof($provider)-5);
            }
        }
        return $ret;
    }

}
Dispatcher::init();
