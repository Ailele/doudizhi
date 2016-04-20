<?php 
	namespace CardGame;
	require_once('../requireFile.php');

	date_default_timezone_set('Asia/Shanghai');
	$dbObj = new Database();
	$dbHandle = $dbObj -> getDBHandle();
	$playerOneID = $_POST['playerOneID'];
	$playerTwoID = $_POST['playerTwoID'];
	$playerThreeID = $_POST['playerThreeID'];
	$winerID = $_POST['winerID'];

	$id = $_POST['id'];
	$roomID = $_POST['roomID'];
	$time = date('Y年n月t日 h:i:s', time());
//	$roomMgr = new RoomManager($roomID);
//	$LordID = $roomMgr -> getLordID();

        $sql = "insert  record (gameTime, playerOneID, playerTwoID, playerThreeID, playerWinerID, playerLordID, id)
values('$time', '$playerOneID', '$playerTwoID', '$playerThreeID', '$winerID', '$id');";
	$result = $dbHandle -> query($sql);

	$clearSql = "delete from room  where roomID = '$roomID';";
	$clearRoomDone = $dbHandle -> query($clearSql);
	$clearsql = "delete from card  where cardRoomID = '$roomID';";
	$clearCard = $dbHandle -> query($clearsql);
	$square = new SquareManager();
	$isDelRoom = $square -> delRoom($roomID);


	if($result && $clearRoomDone && $isDelRoom && $clearCard)
	{
		echo "done";
	}
	else 
	{
		echo "failed";
	}

?>
