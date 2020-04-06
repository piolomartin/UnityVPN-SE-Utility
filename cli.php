<?php
set_time_limit(15);
ini_set('max_execution_time', 15);

require('./SoftEtherApi/SoftEther.php');

use SoftEtherApi\SoftEther;

try{
$softEther = new SoftEther("localhost", 5555);

$res = $softEther->Connect();
$authRes = $softEther->Authenticate($_SERVER['PASSWORD']);

if($authRes->Error!="NoError") die($authRes->Error);

$sessions = $softEther->HubApi->GetSessionList("UnityVPN");
if($sessions->Error!="NoError") die($sessions->Error);

$fp = fopen("/var/www/html/users.log", "a+");
if(!$fp) throw new Exception('Cant open users.log');

$s = flock($fp, LOCK_EX);
if(!$s) throw new Exception('Error on locking file.');
$s=ftruncate($fp, 0);
if(!$s) throw new Exception('Error on truncating file.');

fwrite($fp, time()."\n");

foreach($sessions as $session){
	$sessionName = $session->Name;
	$username = $session->Username;

	fwrite($fp, "{$username}|{$sessionName}\n");
}

fclose($fp);
}catch(Exception $e){
	echo "ERROR: ".$e->getMessage()."\n";
}
