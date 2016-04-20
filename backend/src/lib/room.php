<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 2016/3/27
 * Time: 12:27
 */

require_once '../requireFile.php';

if (!empty($_COOKIE['uid']))
{
	$userName = $_COOKIE['uid'];
	$DBHandle = new \CardGame\Database();
	$isExist = $DBHandle -> isExist($userName);
	if (!$isExist)
	{
		setcookie('uid', '', time() - 100);
		header("Location: ../../../index.html#loginerror");
	}
}
else
{
	header("Location: ../../../index.html#loginerror");
}
$userInfo = $DBHandle -> getUserInfo($_COOKIE['uid']);
$roomID = $_GET['id'];


require_once '../../../frontend/template/roomtpl.php';