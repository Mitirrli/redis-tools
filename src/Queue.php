<?php

declare(strict_types=1);

namespace Mitirrli\Queue;

use Predis\Client;

/**
 * Class Queue
 * @package Mitirrli\Queue
 */
class Queue implements QueueInterface
{
    /**
     * @var int Queue Length
     */
    protected $lLen;

    /**
     * @var Client Redis Client
     */
    private $redis;

    /**
     * Queue constructor.
     * @param int $lLen Queue Length
     * @param array $options Redis Conf
     */
    public function __construct(int $lLen = 5, array $options = [])
    {
        $this->lLen = $lLen;
        $this->redis = new Client($options['parameters'] ?? [], $options['options'] ?? []);
    }

    /**
     * Left in, Right Out
     * @param string $key
     * @param string $value
     * @return int
     */
    public function toList(string $key, string $value): int
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

        return sprintf(self::KEY_NAME, $key);
    }

    /**
     * Get Data By Index
     * @param $key
     * @param $index
     * @return string
     */
    public function getItemByIndex(string $key, int $index): string
    {
        $lLen = $this->redis->lLen($this->getKey($key));
        if ($lLen < $index + 1) {
            $index = 0;
        }

        return $this->redis->lIndex($this->getKey($key), $index);
    }
}
