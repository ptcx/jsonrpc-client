# jsonrpc client

## 适用对象

适用于golang官方jsonrpc服务器的php客户端

## 用法

用golang启动一个jsonrpc服务器（官方例程），参考：https://golang.org/pkg/net/rpc/

```php
<?php

require 'vendor/autoload.php';

$client = new JRClient\Client('tcp', '127.0.0.1:8888');

$result = $client->call('Arith.Multiply', ['A' => 3, 'B' => 4], 1000);
if ($result['error']) {
    echo $result['errorMsg'] . "\n";
} else {
    var_dump($result['data']);
}

$result = $client->call('Arith.Divide', ['A' => 10, 'B' => 4], 2000);
if ($result['error']) {
    echo $result['errorMsg'] . "\n";
} else {
    var_dump($result['data']);
}
```

## 注意

`$client->call()`返回一个数组，格式如下：

```php
$result = [
    'error' => false    // bool，socket错误，比如超时、发送、接收错误，不表示golang服务调用的error错误
    'errorMsg' => ''    // 当error为true时，错误信息
    'data' => []        // golang服务器返回的jsonrpc数据
]
```

现在仅支持jsonrpc 1.0，支持socket tcp连接，不支持http
