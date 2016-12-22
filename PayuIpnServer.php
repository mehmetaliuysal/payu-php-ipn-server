<?php

/*
 * 
 * Mehmet Ali UYSAL
 * mehmetali@gelistir.org
 * https://github.com/mehmetaliuysal/payu-php-ipn-server.git
 * 
 * */


class PayuIpnServer {

	private static $secret = 'XXXXXXXXXXXX'; // secret key 
	private static $ipMask = '83.96.157.64/27'; // Indicated in Payu Documentations

	function __construct() {

	}

	function init() {

		$returnArr = array('status' => false, 'error' => null, 'result' => null);

		$checkReq = self::checkRequirements($_POST);
		if ($checkReq['status'] == false)
			return $checkReq;

		$pass = self::$secret;

		$result = '';
		$return = '';
		$signature = $_POST["HASH"];

		while (list($key, $val) = each($_POST)) {
			$$key = $val;

			if ($key != "HASH") {

				if (is_array($val))
					$result .= self::ArrayExpand($val);
				else {
					$size = strlen(stripslashes($val));
					$result .= $size . stripslashes($val);
				}

			}

		}

		$dateReturn = date("YmdGis");

		$return = strlen($_POST["IPN_PID"][0]) . $_POST["IPN_PID"][0] . strlen($_POST["IPN_PNAME"][0]) . $_POST["IPN_PNAME"][0];
		$return .= strlen($_POST["IPN_DATE"]) . $_POST["IPN_DATE"] . strlen($dateReturn) . $dateReturn;

		$hash = self::hmac($pass, $result);

		if ($hash != $signature) {
			$returnArr['error'] = 'Bad IPN signature';
			/* Here can send mail to technical admin*/
			return $returnArr;
		}

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

	}

	static function checkRequirements($args) {

		$returnArr = array('status' => false, 'error' => NULL);
		
		# if u now Payu Ip range in your locale, you can uncomment this control
		/*
		if (self::cidrMatch(self::getIp(), self::$ipMask) == false) {
			$returnArr['error'] = 'Unauthorized ip address:'.self::getIp();
			return $returnArr;
		}
		*/
		
		if (empty($args)) {
			$returnArr['error'] = 'Invalid arg';
			return $returnArr;
		}

		if (!isset($args['ORDERSTATUS']) || $args['ORDERSTATUS'] != 'COMPLETE') {
			$returnArr['error'] = 'No action required';
			return $returnArr;
		}

		if (!isset($args['REFNOEXT'])) {
			$returnArr['error'] = 'RefNo not found';
			return $returnArr;
		}

		$returnArr['status'] = true;

		return $returnArr;

	}

	static private function cidrMatch($ip, $range) {

		list($subnet, $bits) = explode('/', $range);
		$ip = ip2long($ip);
		$subnet = ip2long($subnet);
		$mask = -1<<(32 - $bits);
		$subnet &= $mask;

		return ($ip & $mask) == $subnet;

	}

	public static function getIp() {

		if (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		} elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			if (strstr($ip, ',')) {
				$tmp = explode(',', $ip);
				$ip = trim($tmp[0]);
			}
		} else {
			$ip = $_SERVER["REMOTE_ADDR"];
		}

		return $ip;
	}

	static function ArrayExpand($array) {
		$retval = "";
		for ($i = 0; $i < sizeof($array); $i++) {
			$size = strlen(stripslashes($array[$i]));
			$retval .= $size . stripslashes($array[$i]);
		}

		return $retval;
	}

	static function hmac($key, $data) {
		$b = 64;
		// byte length for md5
		if (strlen($key) > $b) {
			$key = pack("H*", md5($key));
		}
		$key = str_pad($key, $b, chr(0x00));
		$ipad = str_pad('', $b, chr(0x36));
		$opad = str_pad('', $b, chr(0x5c));
		$k_ipad = $key ^ $ipad;
		$k_opad = $key ^ $opad;
		return md5($k_opad . pack("H*", md5($k_ipad . $data)));
	}

}


$payuIpnServer = new PayuIpnServer;
$response = $payuIpnServer->init();


if($response['status']==true)
	echo $response['result'];
else
	echo $response['error'];






