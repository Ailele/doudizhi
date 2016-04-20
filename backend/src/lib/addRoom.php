<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 2016/3/27
 * Time: 10:46
 */
	require_once '../requireFile.php';

	$uid = $_COOKIE['uid'];
	$squareMgr = new \CardGame\SquareManager();
	$roomID = $squareMgr -> getRoomIDList();
	$keys = array_values($roomID);
	sort($keys);
	if (count($keys) > 0)
	{
		$id = array_pop($keys) + 1;
	}
	else
	{
		$id = 1;
	}
	$squareMgr -> addRoom($id);
	$sql = "insert room(roomID,roomTotalPlayer, roomPlayerOneID, roomPostCardTimes) values('$id', 1, '$uid', 0);";
	$dbObj = new \CardGame\Database();
	$dbhandle = $dbObj -> getDBHandle();
	$query = $dbhandle -> query($sql);
	if($query)
	{
		echo $id;
	}

