<?php 

require 'PayuIpnServer.php';

$payuIpnServer = new PayuIpnServer;
$response = $payuIpnServer->init();


if($response['status']==true)
	echo $response['result'];
else
	echo $response['error'];
