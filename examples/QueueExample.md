> 思路:使用lua脚本判断队列长度,如果长度小于用户当前定义的长度,lpush追加元素至队列,否则队列rpop一个元素并lpush追加元素至队列,通过lua脚本确保原子性

## 快速使用
```
$Lock = new \Mitirrli\Queue\Queue(['key' => 'test', 'lLen' => 10]]);

$Lock->toList('a');
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