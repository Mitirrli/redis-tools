<?php

declare(strict_types=1);

namespace Mitirrli\Queue;

use Mitirrli\Constant\constant;
use Mitirrli\Exception\KeyException;
use Predis\Client;

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
     * @var Client Redis Client
     */
    private $redis;

    /**
     * Queue constructor.
     * @param string $key Queue Key
     * @param int $lLen Queue Length
     * @param array|object $mix Redis Conf or Redis Instance
     */
    public function __construct(string $key, int $lLen = 5, $mix = [])
    {
        $this->key = $this->getKey($key);
        $this->lLen = $lLen;
        $this->redis = ($mix instanceof Client) ? $mix : new Client($mix['parameters'] ?? [], $mix['options'] ?? []);
    }

    /**
     * Left in, Right Out
     * @param string $key
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

        return $this->redis->eval($lua, 1, $this->getKey($key), $value);
    }

    /**
     * Format Key
     * @param string $key
     * @return string
     * @throws KeyException
     */
    public function getKey(string $key): string
    {
        if ($key === '') {
            throw new KeyException('Key no exists', '-1');
        }

        return sprintf(self::QUEUE_NAME, $key);
    }

    /**
     * The length of queue
     * @param string $key
     * @return int
     * @throws KeyException
     */
    public function lLen()
    {
        return $this->redis->lLen($this->key);
    }

    /**
     * Get Data By Index
     * @param $key
     * @param $index
     * @return string
     */
    public function getItemByIndex(int $index): string
    {
        $lLen = $this->redis->lLen($this->key);
        if ($lLen < $index + 1) {
            $index = 0;
        }

        return $this->redis->lIndex($this->key, $index) ?? '';
    }
}
