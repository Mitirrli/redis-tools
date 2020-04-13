<?php

namespace Mitirrli;

use Redis;
use think\Env;

/**
 * Trait TpTrait
 * @package Mitirrli
 */
trait TpTrait
{
    protected $redis;

    /**
     * thinkphp框架直接读取env配置
     * @param int $db
     * @return TpTrait
     */
    public function build($db = '')
    {
        $this->redis = new Redis();

        $this->redis->pconnect(Env::get('REDIS_HOST'), Env::get('REDIS_PORT'));
        $this->redis->auth(Env::get('REDIS_PASSWORD'));
        $this->redis->select(empty($db) ? Env::get('REDIS_DB') : $db);

        return $this;
    }
}