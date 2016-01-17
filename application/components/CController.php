<?php

namespace Multiple\Components;

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Db\Column;

class CController extends Controller {
    
    protected $_response;

    public function initialize(){
        
        // Create a response
        $this->_response = new Response();
        
        $this->_response->setHeader('Access-Control-Allow-Methods', 'GET, PUT, PATCH, DELETE, OPTIONS, HEAD');
        $this->_response->setHeader('Access-Control-Allow-Credentials', 'true');
        $this->_response->setHeader('Access-Control-Allow-Headers', "origin, x-requested-with, content-type");
        $this->_response->setHeader('Access-Control-Max-Age', '3600'); 
        
        $this->_response->setHeader('Content-type', 'application/json;charset=utf-8');
        $this->_response->setHeader('Accept', 'application/json');
        
        $this->_response->setHeader('X-Powered-By', 'REST API Version 1.0.0');
        
    }
    
}