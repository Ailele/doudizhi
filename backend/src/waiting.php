<?php
	/*
	 * 用户点击房间后第一步验证用户是否可以进入该房间
	 */

	namespace CardGame;
	
	require_once 'requireFile.php';

	$uid = $_COOKIE['uid'];
	$roomID = $_GET['id'];

	$dbobj = new Database();
	$dbhandle = $dbobj -> getDBHandle();

	$sql = "select * from room where roomID = '$roomID'";
	$query = $dbhandle -> query($sql);
	$result = $query -> fetch_assoc();
	if($result)
	{
		$totalPlayerNum = (int)$result['roomTotalPlayer'];
		if($totalPlayerNum == 3)
		{
			$userList[] = $result['roomPlayerOneID'];
			$userList[] = $result['roomPlayerTwoID'];
			$userList[] = $result['roomPlayerThreeID'];
			if(in_array($uid, $userList))
			{
				header("Location: lib/room.php?id=$roomID&ID=$uid&status=tgaming");
			}
			else
			{
				header("Location: /backend/src/lib/square.php?status=roomfull&ID=$uid");
			}
		}
		else if	($totalPlayerNum == 2)
		{
			$userList[] = $result['roomPlayerOneID'];
			$userList[] = $result['roomPlayerTwoID'];
			if(in_array($uid, $userList))
			{
				header("Location: lib/room.php?id=$roomID&status=waiting&ID=$uid&totalPlayer=2");
			}
			else
			{
				$sql = "update room set roomPlayerThreeID = '$uid', roomTotalPlayer = 3 where roomID= '$roomID';";
				$dbhandle -> query($sql);

				$roomMgr = new RoomManager($roomID);
				$lordId = $roomMgr -> setLord();
				$roomMgr -> setPlayerAround();

				$cardMgr = new CardManager();
				if($lordId == $uid)
				{
					$init = $cardMgr -> setInitCardList($roomID, $lordId, $userList[0], $userList[1]);
				}
				else if($lordId == $userList[0])
				{
					$init = $cardMgr -> setInitCardList($roomID, $lordId, $uid, $userList[1]);
				}
				else if($lordId == $userList[1])
				{
					$init = $cardMgr -> setInitCardList($roomID, $lordId, $userList[0], $uid);
				}
				if($init)
				{
					$roomMgr -> setPostCardQueueNum(1);
					header("Location: lib/room.php?id=$roomID&status=StartGaming&ID=$uid&totalPlayer=1");
				}
				else
				{
					header("Location: /backend/src/lib/square.php?status=roomfull#IniCardError");
				}
			}
		}
		else if($totalPlayerNum == 1)
		{
			{
				if($uid == $result['roomPlayerOneID'])
				{
					header("Location: lib/room.php?id=$roomID&status=waiting&ID=$uid&totalPlayer=1");
				}
				else
				{
					$sql = "update room set roomPlayerTwoID = '$uid', roomTotalPlayer = '2' where roomID= '$roomID';";
					$addPlayerQuery =  $dbhandle -> query($sql);
					if($addPlayerQuery)
					{
						header("Location: lib/room.php?id=$roomID&status=waiting&total=2");
					}
				}
			}
		}		
	}
	else
	{
		header("Location: http://localhost/#waitinphpNOreturnReusl");
	}