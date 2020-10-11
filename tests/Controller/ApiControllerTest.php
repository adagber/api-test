<?php
namespace test;

use Controller\ApiController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiControllerTest extends TestCase
{

  protected $controller;

  protected function setUp()
  {
    $this->controller = new ApiController();
  }

  public function testIndexAction()
  {
    $request = Request::create('/index.php/servicetest/api.php', 'GET', []);
    $response = $this->controller->indexAction($request);

    //Aseveramos que es una respuesta
    $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);

    //Aseveramos que ha devuelto 200
    $this->assertEquals(200, $response->getStatusCode());

    //Aseveramos que es json
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));

    $request = Request::create('/index.php/servicetest/api.php', 'GET', ['id' => 4]);
    $response = $this->controller->indexAction($request);

    //Aseveramos que es una respuesta
    $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);

    //Aseveramos que ha devuelto 200
    $this->assertEquals(200, $response->getStatusCode());

    //Aseveramos que es json
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));

    $this->assertEquals(
      '{"gameid":{"averageRake":1.0930128154873,"playPercent":100,"totalRounds":1799,"averageCancelations":3.3351862145636}}',
      $response->getContent()
    );
  }
}