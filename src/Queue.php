<?php

namespace Mitirrli\Queue;

class Queue implements QueueInterface
{
    /**
     * @var int 队列长度
     */
    protected $lLen = 50;

    /**
     * @var null|\Redis Redis实例
     */
    protected $redis = null;

    /**
     * @var string Redis主机
     */
    protected $host = '127.0.0.1';

    /**
     * @var int 数据库
     */
    protected $database = 0;

    /**
     * @var string 密码
     */
    protected $password = '';

    /**
     * @var bool 是否持久连接
     */
    protected $persistent = true;

    /**
     * @var string 队列key
     */
    protected $key = '';

    /**
     * Queue constructor.
     * @param array $attributes
     * @throws \Exception
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        $this->checkEnv();
        $this->connectRedis();
    }

    /**
     * 连接redis
     */
    public function connectRedis()
    {
        $this->redis = new \Redis();
        //连接
        $this->connect();
        //连接数据库
        $this->redis->select($this->database);
        //验证
        $this->redis->auth($this->password);
    }

    /**
     * 连接redis
     */
    public function connect()
    {
        if ($this->persistent) {
            $this->redis->pconnect($this->host);
        }
        $this->redis->connect($this->host);
    }

    /**
     * redis环境检测
     *
     * @throws \Exception
     */
    final private function checkEnv()
    {
        if (!extension_loaded('redis')) {
            throw new \Exception('缺少Redis扩展', 0);
        }
    }

    /**
     * 保存list至redis(左进右出)
     *
     * @param $key
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function toList($key, $value)
    {
        $lua = "if redis.call('llen', KEYS[1]) < tonumber({$this->lLen}})
        then
            return redis.call('lpush', KEYS[1], ARGV[1])
        else
            redis.call('rpop', KEYS[1])
            return redis.call('lpush', KEYS[1], ARGV[1])
        end";

        return $this->redis->eval($lua, [$this->getKey(), $value]);
    }

    /**
     * @param $key
     * @return $this
     */
    public function key($key)
    {
        $this->key = sprintf(self::KEY_NAME, $key);

        return $this;
    }

    /**
     * 获取key
     *
     * @return string
     * @throws \Exception
     */
    public function getKey()
    {
        if (empty($this->key)) {
            throw new \Exception('Key no exists', '-1');
        }

        return $this->key;
    }

    /**
     * 根据下标获取数据
     *
     * @param $index
     * @throws \Exception
     */
    public function getItemByIndex($index)
    {
        $this->redis->lIndex($this->getKey(), $index);
    }
}
