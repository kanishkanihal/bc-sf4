<?php
/**
 * Created by PhpStorm.
 * User: kanishka
 * Date: 1/31/19
 * Time: 12:12 PM
 */

namespace App\Service;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Predis\Client;

class RedisConnector
{
    /**
     * @var Client|\Redis|\RedisCluster
     */
    public $connection;
    /**
     * @var Client
     */
    public $client;

    /**
     * RedisConnector constructor.
     */
    public function __construct()
    {
        $host = getenv('REDIS_HOST');
        $port = getenv('REDIS_PORT');;
        $password = getenv('REDIS_PASSWORD');;
        $db = getenv('REDIS_DATABASE');

        $this->connection = RedisAdapter::createConnection(
            "redis://:$password@$host:$port/$db",
            [
                'compression' => true,
                'lazy' => false,
                'persistent' => 0,
                'persistent_id' => null,
                'tcp_keepalive' => 0,
                'timeout' => 30,
                'read_timeout' => 0,
                'retry_interval' => 0,
            ]);

        $this->client = new Client();
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->connection->set($key, $value);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function get($key)
    {
        return $this->connection->get($key);
    }


}