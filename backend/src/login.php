<?php
require_once 'requireFile.php';

$userName = $_POST['loginName'];
$userPsw = $_POST['loginPsw'];

$DBHandle = new \CardGame\Database();
$loginResutl = $DBHandle->Login($userName, $userPsw);

if ($loginResutl) {
	setcookie('uid', $userName, time() + 3600 * 1000, '/');
	\CardGame\updateUserActiveTime($userName);
	header("Location: lib/square.php");
} else {
	header("Location: ../../index.html#loginerror");
}
