<?php
	namespace CardGame;
	require_once '../requireFile.php';
	$uid = $_COOKIE['uid'];
	$roomID = $_POST['roomID'];
	$dbj = new Database();

	if(count($_POST) == 1)
	{
		$roomInfo = $dbj -> getRoomInfo($roomID);
		$userInfo = $dbj -> getUserInfo($uid);
		$respone = array();
		$respone['userLeftAvatar'] = $userInfo['userAvatar'];
		$respone['userRightAvatar'] = $userInfo['userAvatar'];
		$respone['userLeftPlayerID'] = $userInfo['userLeftPlayerID'];
		$respone['userRightPlayerID'] = $userInfo['userRightPlayerID'];
		$respone['PlayerNum'] = $roomInfo['roomTotalPlayer'];
		$respone['PlayerOneID'] = $roomInfo['roomPlayerOneID'];
		$respone['PlayerTwoID'] = $roomInfo['roomPlayerTwoID'];
		$respone['PlayerThreeID'] = $roomInfo['roomPlayerThreeID'];

		echo json_encode($respone);
	}