# payu-php-ipn-server

By this Class you can check and process payu ipn messages


Usage : 

Firstly set secret key in PayuIpnServer.php
" private static $secret = 'XXXXXXXXXXXX'; "

can you customize PayuIpnServer.php for check order data equility

<?php 

  require 'PayuIpnServer.php';
  $payuIpnServer = new PayuIpnServer;
  $response = $payuIpnServer->init();


  if($response['status']==true)
    echo $response['result'];
  else
    echo $response['error'];

?>


Success Response : <EPAYMENT>20161222231812|c23d802af0b21c88fe7f86c6a38a42b5</EPAYMENT>
