<?php

namespace Mitirrli;

use Mitirrli\Exception\KeyException;
use Mitirrli\Lock\Lock;
use Mitirrli\Queue\Queue;
use Redis;
use think\Env;

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
     * Tools constructor.
     * @param string $host
     * @param string $port
     * @param string $password
     * @param int $index
     */
    public function __construct($host = '127.0.0.1', $port = '6379', $password = '', $index = 0)
    {
        $this->redis = new Redis();

        $this->redis->pconnect(isset($host) ? $host : Env::get('REDIS_HOST'), isset($port) ? $port : Env::get('REDIS_PORT'));
        $this->redis->auth(isset($password) ? $password : Env::get('REDIS_PASSWORD'));
        $this->redis->select(isset($index) ? $index : Env::get('REDIS_DB'));
    }

    /**
     * 选择数据库
     * @param $db
     * @return Tools
     */
    public function selectDb($db)
    {
        $this->redis->select($db);

        return $this;
    }

    /**
     * 设置配置项
     * @param array $params
     * @return $this
     */
    public function setConfig(array $params)
    {
        $this->config = $params;

        return $this;
    }

    /**
     * Magic Method .
     * @param $name
     * @return Lock|Queue
     * @throws Exception\KeyException
     */
    public function __get($name)
    {
        switch ($name) {
            case 'lock':
                return new Lock($this->redis, $this->config);

            case 'queue':
                return new Queue($this->redis, $this->config);

            default:
                throw new KeyException('指定的key不存在', 1004);
        }
    }
}
