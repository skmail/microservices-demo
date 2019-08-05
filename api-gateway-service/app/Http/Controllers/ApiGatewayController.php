<?php

namespace App\Http\Controllers;

use App\HttpClient;
use Illuminate\Http\Request;

class ApiGatewayController extends Controller
{
    protected $client;

    protected $request;
    
    protected $prefix = 'api';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, HttpClient $client)
    {
        $this->client = $client;
        $this->request = $request;
    }

    public function get(){
        
        $route = $this->getRoute();

        $queryString = http_build_query($this->request->all());
        $queryString = !empty($queryString) ? '?' . $queryString : '';

        $response = $this->client->get($route['endpoint'] . $queryString);

        return $response;
    }


    protected function getRoute(){
        
        $path = ltrim(substr($this->request->path(), strlen($this->prefix)), '/');
        
        $route = config('services-routing.'. $path);
        if(!$route){
            $route = config('services-routing.'. '/' . $path);            
        }
        
        if(!$route){
            return abort(404);
        }
        $endPoint = rtrim($route['service'],'/') . '/' . $path ; 
        
        return [
            'endpoint' => $endPoint
        ] + $route;
    }
}
