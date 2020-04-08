# redis-list

固定大小的redis队列
```
1. easy example:
$queue = new Queue(6);
```
```
2. another redis config, like this
$redis = [
  'parameters' => env('host'),
  'options' => ['parameters' => ['database' => env('db'), 'password' => env('pwd')]]
];
$queue = new Queue(6, $redis);
```