<h1 align="center"> Redis Tools </h1>
<p align="center">:rainbow: 基于redis的一些组件</p>

[![Total Downloads](https://poser.pugx.org/mitirrli/redis-tools/downloads)](https://packagist.org/packages/mitirrli/redis-tools)
[![Latest Stable Version](https://poser.pugx.org/mitirrli/redis-tools/v/stable)](https://packagist.org/packages/mitirrli/redis-tools)
[![Latest Unstable Version](https://poser.pugx.org/mitirrli/redis-tools/v/unstable)](https://packagist.org/packages/mitirrli/redis-tools)
<a href="https://packagist.org/packages/mitirrli/redis-tools"><img src="https://poser.pugx.org/mitirrli/redis-tools/license" alt="License"></a>


### 固定大小的redis队列
```
$Lock = new \Mitirrli\Queue\Queue(['key' => 'test', 'lLen' => 10]]);

$Lock->toList('a');
```

### redis分布式锁
```
$Lock = new \Mitirrli\Lock\Lock(['key' => 'test']);

//业务逻辑占用锁
$Lock->lock();

//解锁
$Lock->unlock();
```
