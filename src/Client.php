<?php

namespace Mitirrli;

use Redis;

/**
 * Class Tools
 * @package Mitirrli
 */
class Client
{
    /**
     * Tools constructor.
     * @param $redis
     */
    public function __construct(array $redis)
    {
        $client = new Redis();

        $client->pconnect($redis['host'] ?? '127.0.0.1', $redis['port'] ?? 6379);
        $client->auth($redis['pwd'] ?? '');
        $client->select($redis['db'] ?? 0);

        return $client;
    }
}
