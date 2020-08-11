> 思路:使用lua脚本判断队列长度,如果长度小于用户当前定义的长度,lpush追加元素至队列,否则队列rpop一个元素并lpush追加元素至队列,通过lua脚本确保原子性

## 快速使用
```
$Lock = new \Mitirrli\Queue\Queue(['key' => 'test', 'lLen' => 10]]);

$Lock->toList('a');
```
