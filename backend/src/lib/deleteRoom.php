<?php
namespace CardGame;
require_once '../requireFile.php';
$id = $_POST['id'];
$square = new SquareManager();
if ($square -> getTotalRoomNum() > 0)
{
	$deleteRoomSQL = "delete from room where roomID='$id';";
	$dbObj = new Database();
	$deleteCard = "delete from card where cardRoomID='$id';";
	$dh = $dbObj -> getDBHandle();
	$dh -> query($deleteRoomSQL);
	$dh -> query($deleteCard);
	if ($square -> delRoom($id))
	{
		echo "true";
	}
	else
	{
		echo "false";
	}
}