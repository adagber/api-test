<?php

use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();
$routes->add('api', new Routing\Route('/servicetest/api.php/{name}', [
  'name'        => 'World',
  '_controller' => '\\Controller\\ApiController::indexAction',
]));

return $routes;