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
     * @param string $type
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
    public function create($host = '127.0.0.1', $port = '6379', $password = '', $index = 0)
    {
        $this->redis = new Redis();

        $this->redis->pconnect($host, $port);
        $this->redis->auth($password);
        $this->redis->select($index);

        return $this;
    }

    /**
     * thinkphp框架直接读取env配置
     * @param string $db
     * @return $this
     */
    public function build($db = '')
    {
        $this->redis = new Redis();

        $this->redis->pconnect(Env::get('REDIS_HOST'), Env::get('REDIS_PORT'));
        $this->redis->auth(Env::get('REDIS_PASSWORD'));
        $this->redis->select(empty($db) ? Env::get('REDIS_DB') : $db);

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
