<?php

namespace Mitirrli\Lock;

use Mitirrli\Constant\constant;
use Predis\Client;
use Mitirrli\Exception\KeyException;

/**
 * Class Lock
 * @package Mitirrli\Lock
 */
class Lock implements constant
{
    /**
     * @var int Lock Time
     */
    protected $time;

    /**
     * @var Client Redis Client
     */
    protected $redis;

    /**
     * @var Lock Key
     */
    protected $key;

    /**
     * @var Lock Val
     */
    protected $val;

    /**
     * Lock constructor.
     * @param array $attributes
     * @param array $options Redis Conf
     * @throws KeyException
     */
    public function __construct(array $attributes, array $options = [])
    {
        foreach ($attributes as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
        $this->key = $this->setKey();
        $this->val = $this->setValue();
        $this->redis = new Client($options['parameters'] ?? [], $options['options'] ?? []);
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
     * 加锁
     * @return mixed
     */
    public function lock()
    {
        $result = $this->redis->set($this->key, $this->val, 'NX', 'EX', $this->time);
        return is_null($result) ? 0 : 1;
    }

    /**
     * 解锁.
     * @param $key
     * @param $val
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

        return $this->redis->eval($lua, 1, $this->key, $this->val);
    }
}


