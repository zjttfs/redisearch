<?php

namespace FKRediSearch\RedisRaw;

use Predis\Client;

/**
 * Class PredisAdapter
 * @package FKRediSearch\RedisRaw
 *
 * This class wraps the NRK client: https://github.com/nrk/predis
 */
class PredisAdapter extends AbstractRedisRawClient {
  /**
   * @var Client
   */
  public $redis;
  
  public function connect($hostname = '127.0.0.1', $port = 6379, $db = 0, $password = null, $scheme = 'tcp'): RedisRawClientInterface {
    $clientArgs = array(
      'database'  => $db,
      'password'  => $password
    );
    if ( $scheme === 'tcp' ) {
      $clientArgs = array_merge(
        $clientArgs,
        array(
        'scheme'    => 'tcp',
        'port'      => $port,
        'host'      => $hostname
        )
      );
    } else if ( $scheme === 'unix' ) {
      $clientArgs = array_merge(
        $clientArgs,
        array(
        'scheme'    => 'unix',
        'path'      => $hostname
        )
      );
    }
    $this->redis = new Client( $clientArgs );
    $this->redis->connect();
    return $this;
  }
  
  public function multi(bool $usePipeline = NULL) {
    return $this->redis->pipeline();
  }
  
  public function rawCommand(string $command, array $arguments) {
    $preparedArguments = $this->prepareRawCommandArguments($command, $arguments);

    $rawResult = $this->redis->executeRaw($preparedArguments);
    return $this->normalizeRawCommandResult($rawResult);
  }
}
