<?php
namespace test;

use Model\ServicetestModel;
use PHPUnit\Framework\TestCase;

class ServicetestModelTest extends TestCase
{

  protected $model;
  protected function setUp()
  {
    $this->model = new ServicetestModel('test');
  }

  public function testGetAllGames()
  {
    $allGames = $this->model->getAllGames();

    //Aseveramos que devuelve un array
    $this->assertTrue(is_array($allGames));

    //Aseveramos que hay 96 juegos
    $this->assertCount(96, $allGames);
  }

  public function testGetGameById(){

    $game = $this->model->getGameById(4);
    
    //Aseveramos que devuelve un array
    $this->assertTrue(is_array($game));

    //Aseveramos que tiene los campos necesarios
    $this->assertArrayHasKey('gameid', $game);
    $this->assertArrayHasKey('averageRake', $game['gameid']);
    $this->assertArrayHasKey('playPercent', $game['gameid']);
    $this->assertArrayHasKey('totalRounds', $game['gameid']);
    $this->assertArrayHasKey('averageCancelations', $game['gameid']);
  }

  public function testCountAllRounds(){

      $count = $this->model->countAllRounds();

      $this->assertEquals(1799, $count);
  }
  
  public function testGetGamesFromApi()
  {
    
    $games = $this->model->getGamesFromApi();

    //Aseveramos que devuelve un array
    $this->assertTrue(is_array($games));

    //Aseveramos que hay 96 juegos
    $this->assertCount(96, $games);

    //Comprobamos la estructura del array
    $game = current($games);

    $this->assertArrayHasKey('game_id', $game);
    $this->assertArrayHasKey('game_type', $game);
    $this->assertArrayHasKey('game_name', $game);
    $this->assertArrayHasKey('game', $game);
    $this->assertArrayHasKey('description', $game);
    $this->assertArrayHasKey('available', $game);
    $this->assertArrayHasKey('image', $game);
  }

  public function testGetRoundsFromApi()
  {
    $rounds = $this->model->getRoundsFromApi();

    //Aseveramos que devuelve un array
    $this->assertTrue(is_array($rounds));

    //Aseveramos que hay 1799 rondas
    $this->assertCount(1799, $rounds);

    //Comprobamos la estructura del array
    $round = current($rounds);

    $this->assertArrayHasKey('play_id', $round);
    $this->assertArrayHasKey('user_id', $round);
    $this->assertArrayHasKey('date', $round);
    $this->assertArrayHasKey('game', $round);
    $this->assertArrayHasKey('version', $round);
    $this->assertArrayHasKey('client_version', $round);
    $this->assertArrayHasKey('bet', $round);
    $this->assertArrayHasKey('win', $round);
    $this->assertArrayHasKey('currency', $round);
    $this->assertArrayHasKey('state', $round);
  }
}