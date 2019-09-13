<?php
//----------
/*
 * Crytocurrency RPC caller
 * 
 * Based on DogecoinFah by SoCo_cpp <soco@socosoftare.net>
 * JSON RPC functionality addapted from JSON-RPC PHP by Sergio Vaccaro <sergio@inservibile.org>
*/
//----------
define('RPC_HOST', ""); // @ CHANGEME
define('RPC_PORT', ""); // @ CHANGEME
define('RPC_USER', ""); // @ CHANGEME
define('RPC_PASS', ""); // @ CHANGEME
//----------
function XVarDump(&$Var, $flatten = true) // for debugging
{
	//-----------------------------------
	ob_start();
	var_dump($Var);
	$s = ob_get_contents();
	ob_end_clean();
	//-----------------------------------
	if ($flatten === true)
	{
		$lines = explode("\n", $s);
		$s = '';
		foreach ($lines as $l)
			$s .= trim($l)." ";
	}
	//-----------------------------------
	return $s;
}
//---------------------------------------------------------------------
function execWallet($method, $param = array())
{
	//----------
	$uri = 'http://'.RPC_USER.':'.RPC_PASS.'@'.RPC_HOST.':'.RPC_PORT.'/';
	//----------
	$request = json_encode( array('method' => $method, 'params' => $param, 'id' => 1) );
	$opts = array('http' => array( 'method' => 'POST', 'header' => 'Content-type: application/json', 'content' => $request, 'ignore_errors' => false));
	$response = '';
	//----------
	try
	{
		//----------
		$context = stream_context_create($opts);
		if ($fp = @fopen($uri, 'r', false, $context))
		{
			while ($row = @fgets($fp))
				$response .= trim($row)."\n"; // append rows to response
		}
		else
		{
			error_log("RPCSendRawTrans connection failed. Host: ".RPC_HOST.", Port: ".RPC_PORT);
			return false;
		}
		//----------
	}
	catch (exception $e)
	{
		error_log("RPCSendRawTrans exception");
		return false;
	}
	//----------
	return $response;
}
//----------
$postBody = file_get_contents('php://input');
$reply = execWallet("sendrawtransaction", array($postBody));
//----------
if ($reply === false)
	echo "failed";
else
{
	$reply = json_decode($reply, true);
	echo $reply['result'];
}
//----------
?>
