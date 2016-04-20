<?php

namespace CardGame;

function updateUserActiveTime($uid)
{
	$timeStamp = time();
	$dbob = new Database();
	$dbhandle = $dbob -> getDBHandle();
	$sql = "update user set userLastActiveTimestamp = $timeStamp where userName='$uid'";
	$query = $dbhandle -> query($sql);
	if($query)
	{
		return $timeStamp;
	}
	return -1;
}