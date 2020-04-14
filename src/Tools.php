<?php

namespace Mitirrli;

use Redis;

/**
 * Class Tools
 * @package Mitirrli
 */
class Tools
{
    /**
     * @var Redis
     */
    protected $redis;

    /**
     * Tools constructor.
     * @param $redis
     */
    public function __construct(array $redis)
    {
        $this->redis = new Redis();

        $this->redis->pconnect($redis['host'] ?? '127.0.0.1', $redis['port'] ?? 6379);
        $this->redis->auth($redis['pwd'] ?? '');
        $this->redis->select($redis['db'] ?? 0);
    }
}
