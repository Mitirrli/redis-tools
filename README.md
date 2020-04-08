# redis-list

```
一 固定大小的redis队列
1. easy example:
$queue = new Queue(6);

2. another redis config, like this
$redis = [
    'parameters' => \think\Env::get('REDIS_HOST'),
    'options' => ['parameters' => ['database' => 6, 'password' => \think\Env::get('REDIS_PASSWORD')]]
];
$queue = new Queue(6, $redis);
```