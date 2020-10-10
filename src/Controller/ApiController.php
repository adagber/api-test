<?php
namespace Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController{

  public function indexAction(Request $request)
  {
    $id = $request->get("id");

    //Comprobamos si el id es numérico
    if(null !== $id && !is_numeric($id)){

      throw new \RuntimeException('El id no es válido');
    }

    $model = new \Model\ServicetestModel(getenv('APP_ENV'));

    //$model->clearCache();
    $result = $id ? $model->getGameById($id) : $model->getAllGames();
    
    return new JsonResponse($result);
  }

}