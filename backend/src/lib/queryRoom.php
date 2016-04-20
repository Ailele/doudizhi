<?php
	namespace CardGame;
	require_once '../requireFile.php';

	$square = new SquareManager();
	$usrName = $_COOKIE['uid'];
	echo json_encode($square -> getRoomIDList());

