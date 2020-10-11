<?php

namespace Cache;

class Cache
{

  public $memcache;
  
  public function __construct()
  {
    $this->memcache = new \Memcache();

    //Obtenemos los datos de conexion
    $host = getenv('MEMCACHED_HOST');
    $port = getenv('MEMCACHED_PORT');

    if(false === $this->memcache->connect($host, $port)){

      throw new \Exception('No se ha podido conectar al host');
    }
  }

  function __destruct() {
    $this->memcache->close();
  }
}