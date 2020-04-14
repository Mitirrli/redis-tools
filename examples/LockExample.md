> 思路:通过setnx给key设置一个值,只要redis中key存在,其他用户无法获取key,实现占用锁,业务完成后解锁,确保可以让其他请求占用锁,不出现数据混乱等情况

## 快速使用
```
$Lock = new \Mitirrli\Lock\Lock(['key' => 'test']);

//业务逻辑占用锁
$Lock->lock();

//解锁
$Lock->unlock();
```

## 其他参数
```
arg1:
    key: 锁的名称
    time: 锁自动过期时间,避免因其他异常问题无法解锁导致业务无法进行,默认10秒
arg2:
    host、port、pwd、db

例子:
$Lock = new \Mitirrli\Lock\Lock(
    ['key' => 'aa', 'time' => 60],
    ['host' => '127.0.0.1', 'port' => '6355', 'pwd' => '123456', 'db' => 9]
);
```