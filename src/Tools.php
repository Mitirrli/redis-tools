<?php

namespace Mitirrli;

use Mitirrli\Lock\Lock;
use Mitirrli\Queue\Queue;
use Redis;

/**
 * Class Tools
 * @property Lock $lock
 * @property Queue $queue
 */
class Tools
{
    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @var array 配置文件
     */
    protected $config;

    /**
     * @return Tools
     */
    public static function init()
    {
        return new self();
    }

    /**
     * 创建连接
     * @param string $host
     * @param string $port
     * @param string $password
     * @param int $index
     * @return $this
     */
    public function build($host = '127.0.0.1', $port = '6379', $password = '', $index = 0)
    {
        $this->redis = new Redis();

        $this->redis->pconnect($host, $port);
        $this->redis->auth($password);
        $this->redis->select($index);

        return $this;
    }

    /**
     * 设置配置项
     * @param array $params
     */
    public function setConfig(array $params)
    {
        $this->config = $params;
    }


    /**
     * Magic Method .
     */
    public function __get($name)
    {
        switch ($name) {
            case 'lock':
                return new Lock($this->redis, $this->config);
//
//            case 'queue':
//                return new Queue();
        }
    }
}