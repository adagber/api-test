<?php

namespace Model;

use GuzzleHttp\Client;

class ServicetestModel
{

  private $client;

  private $cache;

  public function __construct()
  {

    $this->client = new Client([
      'base_url' => 'http://212.32.252.166:3000/'
    ]);
  }

  public function getRoundsByGame($id)
  {
    return $this->getRounds(['game' => $id]);
  }

  public function getRounds($query = [])
  {
    $response = $this->client->get('/rounds', [
      'query' => $query
    ]);

    return $response->json();
  }

  public function countAllRounds()
  {
    return count($this->getRounds(['state' => 1]));
  }

  public function getGames()
  {
    $response = $this->client->get('/games');
    return $json = $response->json();
  }
}