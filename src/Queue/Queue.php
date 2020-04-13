<?php

declare(strict_types=1);

namespace Mitirrli\Queue;

use Mitirrli\Constant\constant;
use Mitirrli\Exception\KeyException;

/**
 * Class Queue
 * @package Mitirrli\Queue
 */
class Queue implements constant
{
    /**
     * @var int Queue Length
     */
    protected $lLen;

    /**
     * @var Queue Key
     */
    protected $key;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * Queue constructor.
     * @param $redis
     * @param $config
     * @throws KeyException
     */
    public function __construct($redis, $config)
    {
        foreach ($config as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        $this->redis = $redis;
        $this->key = $this->getKey();
    }

    /**
     * Left in, Right Out
     * @param string $value
     * @return int
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
