<?php

class StaticRouteHandeler
{
    public function init()
    {
        $app = Slim::getInstance();
        $app->get("/static/privacy", array($this, "privacy"))->name("privacy");
        $app->get("/static/terms", array($this, "terms"))->name("terms");
    }

    public function privacy()
    {
         $app = Slim::getInstance();
         $app->render("privacy.tpl");
    }
    
    public function terms()
    {
         $app = Slim::getInstance();
         $app->render("terms.tpl");
    }
}

$route_handler = new StaticRouteHandeler();
$route_handler->init();
unset ($route_handler);