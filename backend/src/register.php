<?php
	require_once 'requireFile.php';

	$userName = $_POST['registerName'];
	$userPsw = $_POST['registerPsw'];

	$DBHandle = new \CardGame\Database();
	$registerResult = $DBHandle -> registerUser($userName, $userPsw);

	if ($registerResult)
	{
		setcookie('uid', $userName,time ()+  3600 * 1000, '/');
		\CardGame\updateUserActiveTime($userName);
		header("Location: lib/square.php");
	}
	else
	{
		header("Location: ../../index.html#userexists");
	}