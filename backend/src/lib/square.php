<?php
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
		header("Location: http://localhost/index.html#loginerror");
	}
	$userInfo = $DBHandle -> getUserInfo($_COOKIE['uid']);
	$squareManager = new \CardGame\SquareManager();
	$totalRoomNum = $squareManager -> getTotalRoomNum();
	$roomIDList = $squareManager -> getRoomIDList();

	require_once '../../../frontend/template/squaretpl.php';



