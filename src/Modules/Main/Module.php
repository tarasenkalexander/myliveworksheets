<?php 

namespace Modules\Main;

use System\Contracts\IModule;
use System\Router;

class Module implements IModule{
    public function registerRoutes(Router $router)
    {
        $router->addRoute("~^/?&~", Main::class);
    }
}