<?php

namespace Mitirrli\Lock;

use Mitirrli\Constant\constant;
use Mitirrli\Exception\KeyException;
use Redis;

/**
 * Class Lock
 * @package Mitirrli\Lock
 */
class Lock implements constant
{
    /**
     * @var int Lock Time
     */
    protected $time = 10;

    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $val;

    /**
     * Lock constructor.
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
        $this->key = $this->setKey();
        $this->val = $this->setValue();
    }

    /**
     * 设置Value.
     * @return string
     */
    public function setValue()
    {
        return time() . uniqid() . md5($_SERVER['REQUEST_TIME'] . $_SERVER['REMOTE_ADDR']) . mt_rand(10, 999);
    }

    /**
     * 设置Key.
     * @return string
     * @throws KeyException
     */
    public function setKey()
    {
        // key不能为空
        if (empty($this->key)) {
            throw new KeyException('Key can not be empty string.', '1004');
        }

        return sprintf(self::LOCK_NAME, $this->key);
    }


    /**
     * 加锁 .
     * @return bool
     */
    public function lock()
    {
        return $this->redis->set($this->key, $this->val, ['nx', 'ex' => $this->time]);
    }

    /**
     * 解锁 .
     * @return mixed
     */
    public function unlock()
    {
        $lua = "if redis.call('get', KEYS[1]) == ARGV[1]
        then
            return redis.call('del', KEYS[1]) 
        else 
            return 0 
        end";

        return $this->redis->eval($lua, [$this->key, $this->val], 1);
    }
}
