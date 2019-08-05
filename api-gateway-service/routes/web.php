<?php


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['middleware' => 'auth' ,'prefix' => 'api'], function ($router) {
    
    $routesList = config('services-routing');
    $createRoute = function($path, $route) use($router){
        $verb = strtolower($route['verb']);
        $router->{$route['verb']}($path, 'ApiGatewayController@' . $verb);
    };
    
    foreach($routesList as $routePath => $route){
        if(is_array($route['verb'])){
            foreach($route['verb'] as $verb){
                $createRoute($routePath, ['verb' => $verb] + $route);
            }
        }else{
            $createRoute($routePath, $route);
        }
    }
});