<?php
namespace Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController{

  public function indexAction(Request $request)
  {
    
    $name = $request->get("name");
    return new JsonResponse("Hola $name");
  }
}