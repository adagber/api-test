<?php

namespace Model;

use Cache\Cache;
use GuzzleHttp\Client;

class ServicetestModel
{

  private $client;

  private $cache;

  private $env;

  public function __construct($env = 'dev')
  {

    $this->env = $env;

    $this->client = new Client([
      'base_url' => 'http://212.32.252.166:3000/'
    ]);

    $this->cache = new Cache();
  }

  public function clearCache()
  {
    $this->cache->memcache->flush();
  }

  public function warmCache()
  {
    $this->getAllGames();
  }

  public function getAllGames()
  {

    $result = [];

    foreach($this->getGames() as $game){

      $id = $game['game_id'];

      $result[] = $this->getGameById($id);
    }

    return $result;
  }

  public function getGameById($id)
  {
    //Obtenemos las rondas del juego
    $rounds = $this->getRoundsByGame($id);

    $totalRounds = count($rounds);
    $sumBets = 0;
    $sumWins = 0;
    $sumCancelations = 0;

    //Calculamos los campos agrupados
    foreach($rounds as $round){
      
      //Calculamos el Rake
      $sumBets = $sumBets + $round['bet'];
      $sumWins = $sumWins + $round['win'];

      //Calculamos las cancelaciones
      if(2 == $round['state']) $sumCancelations++;
    }

    //Obtenemos el numero total de partidas
    $totalPlayed = $this->countAllRounds();

    $averageRake = $sumBets != 0 ? $sumWins / $sumBets : 0;
    $playPercent = $totalPlayed != 0 ? ($totalRounds / $totalPlayed * 100) : 0;
    $averageCancelations = $totalRounds != 0 ? ($sumCancelations / $totalRounds * 100) : 0;

    return [
      'gameid' => [
        'averageRake' => $averageRake,
        'playPercent' => $playPercent,
        'totalRounds' => $totalRounds,
        'averageCancelations' => $averageCancelations
      ]
    ];
  }

  public function getRoundsByGame($id)
  {
    //Intentamos obtener el valor de todas las partidas
    if(false !== $gameRounds = $this->cache->memcache->get('GAME_ROUNDS_'.$id, MEMCACHE_COMPRESSED)){

      return $gameRounds;
    }

    $gameRounds = $this->getRoundsFromApi(['game' => $id]);

    //Guardamos el valor en cache durante una hora
    $this->cache->memcache->set('GAME_ROUNDS_'.$id, $gameRounds, MEMCACHE_COMPRESSED, 3600);

    return $gameRounds;
  }

  public function countAllRounds()
  {
    //Intentamos obtener el valor de todas las partidas de la cache
    if(false !== $num = $this->cache->memcache->get('NUM_ROUNDS')){

      return $num;
    }

    /**
     * Esta llamada puede tardar muchísimo
     */
    $num = count($this->getRoundsFromApi(['state' => 1]));
    
    //Guardamos el valor en cache durante un día
    $this->cache->memcache->set('NUM_ROUNDS', $num, false, 86400);

    return $num;
  }

  public function getGames()
  {
    //Intentamos sacar de la cache todos los juegos
    if(false !== $allGames = $this->cache->memcache->get('ALL_GAMES', MEMCACHE_COMPRESSED)){

      return $allGames;
    }

    $allGames = $this->getGamesFromApi();

    //Guardamos en la cache los juegos durante un día
    $this->cache->memcache->set('ALL_GAMES', $allGames, MEMCACHE_COMPRESSED, 86400);

    return $allGames;
  }

  public function getGamesFromApi()
  {

    if($this->env != 'prod'){

      $content = file_get_contents(dirname(__FILE__).'/../../var/data/games.json');
      return json_decode($content, true);
    }
    
    $response = $this->client->get('/games');
    return $json = $response->json();
  }

  public function getRoundsFromApi($query = [])
  {

    if($this->env != 'prod'){

      $content = file_get_contents(dirname(__FILE__).'/../../var/data/rounds.json');
      return json_decode($content, true);
    }

    $response = $this->client->get('/rounds', [
      'query' => $query
    ]);

    return $response->json();
  }
}