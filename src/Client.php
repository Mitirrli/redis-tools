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
     * 返回Client
     * @return Client
     */
    public static function app()
    {
        return new self();
    }

    /**
     * 创建redis
     * @param array $redis
     * @return Redis
     */
    public function make(array $redis)
    {
        $client = new Redis();

        $client->pconnect($redis['host'] ?? '127.0.0.1', $redis['port'] ?? 6379);
        $client->auth($redis['pwd'] ?? '');
        $client->select($redis['db'] ?? 0);

        return $client;
    }
}
