<?php

namespace App\Middleware;

class MonoLoggerMiddleware extends Middleware
{
  public function __invoke($request, $response, $next)
  {
    // Define a log middleware
    // or you can use $this->get('logger')->info('...');
     //$this->container->Logger->error('Something happen');

      $response = $next($request, $response);


      return $response;



  }



}
