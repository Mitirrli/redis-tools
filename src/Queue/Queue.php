<?php

declare(strict_types=1);

namespace Mitirrli\Queue;

use Mitirrli\Client;
use Mitirrli\Constant\constant;
use Mitirrli\Exception\KeyException;
use Redis;

/**
 * Class Queue
 * @package Mitirrli\Queue
 */
class Queue implements constant
{
    /**
     * @var int 队列长度
     */
    protected $lLen = 10;

    /**
     * @var Queue 队列名
     */
    protected $key;

    /**
     * @var Redis
     */
    private $redis;

    /**
     * Queue constructor.
     * @param $redis
     * @param $config
     * @throws KeyException
     */
    public function __construct($config = [], $redis = [])
    {
        foreach ($config as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        $this->redis = Client::app()->make($redis);;
        $this->key = $this->getKey();
    }

    /**
     * Format Key
     * @return string
     * @throws KeyException
     */
    public function getKey(): string
    {
        if ($this->key === '') {
            throw new KeyException('Key no exists', '-1');
        }

        return sprintf(self::QUEUE_NAME, $this->key);
    }

    /**
     * Left in, Right Out
     * @param string $value
     * @return int 队列元素数目
     */
    public function toList(string $value)
    {
        $lua = "if redis.call('llen', KEYS[1]) < tonumber({$this->lLen})
        then
            return redis.call('lpush', KEYS[1], ARGV[1])
        else
            redis.call('rpop', KEYS[1])
            return redis.call('lpush', KEYS[1], ARGV[1])
        end";

        return $this->redis->eval($lua, [$this->key, $value], 1);
    }

    /**
     * The length of queue
     * @return int
     */
    public function lLen()
    {
        return $this->redis->lLen($this->key);
    }

    /**
     * Get Data By Index
     * @param int $index
     * @return bool|mixed|string
     */
    public function getItemByIndex(int $index)
    {
        $lLen = $this->redis->lLen($this->key);
        if ($lLen < $index + 1) {
            $index = 0;
        }

        return $this->redis->lIndex($this->key, $index) ?? '';
    }
}
