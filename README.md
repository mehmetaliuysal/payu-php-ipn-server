# payu-php-ipn-server

By this Class you can check and process payu ipn messages


Usage : 

```sh
<?php 

  require 'PayuIpnServer.php';
  $payuIpnServer = new PayuIpnServer;
  $response = $payuIpnServer->init();


  if($response['status']==true)
    echo $response['result'];
  else
    echo $response['error'];

?>
```

Success Response : 
```sh
<EPAYMENT>20161222231812|c23d802af0b21c88fe7f86c6a38a42b5</EPAYMENT>
```


Secret Key Definition 
```sh
PayuIpnServer.php
```

```sh
private static $secret = 'XXXXXXXXXXXX';
```

Customizing for request data and live order data equality

```sh
PayuIpnServer.php
```
```sh
$resultHash = self::hmac($pass, $return);
$returnArr['result'] = "<EPAYMENT>" . $dateReturn . "|" . $resultHash . "</EPAYMENT>";

/*
 * Here check equality with your order data. 
 * if not equal,  set $returnArr['error'] wtih your custom error msg and 
 * not set $returnArr['status'] = true;
 * 
 * */

$returnArr['status'] = true;
return $returnArr;
```
