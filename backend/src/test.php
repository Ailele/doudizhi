<?php 
	require_once 'requireFile.php';
	$roo = new \CardGame\RoomManager('1');
	var_dump($roo -> getValidatePostPlayerID());