<?php
	require_once 'requireFile.php';

	$roomID = (int)$_POST['roomID'];
	$userQueue = (int)$_POST['postQueue'];
	$userID = $_COOKIE['uid'];


	$isPost = ($_POST['isPost'] == '1');

	if($isPost)
	{
		$flag = 'true';
	}
	else
	{
		$flag = 'false';
	}

	$roomMgr = new \CardGame\RoomManager($roomID);
	$cardMgr = new \CardGame\CardManager();

	$serPostCardTimes = $roomMgr -> getPostCardQueueNum();
	$totalPeople = $roomMgr -> getTotalPlayerNum();
	$response = array();
	$response['totalPeople'] = $totalPeople;
	$LRPlayerInfo = $roomMgr -> getLRPostList($userID);
	$lordPosition = $roomMgr -> getRelateLordPosition($userID);
	$response['lordPosition'] = $lordPosition;
	$leftID = $LRPlayerInfo[0][0];
	$leftPostList = $cardMgr -> transferStringToArray($LRPlayerInfo[0][1]);
	$rightID = $LRPlayerInfo[1][0];
	$rightPostList = $cardMgr -> transferStringToArray($LRPlayerInfo[1][1]);
	$validatePosterID = $roomMgr -> getValidatePostPlayerID();

	//等待玩家进入
	if($totalPeople != 3)
	{
		$response = array('totalPlayer' => $totalPeople, 'debug' => '1');
	}

	//轮询数据
	else if(!$isPost)
	{
		if($userQueue == $serPostCardTimes)
		{
			$response['postQueueNum'] = $serPostCardTimes;
			$response['totalPeople'] = $totalPeople;
			$response['validPostID'] = $validatePosterID;
			$response['debug'] = 'debug 1';
			$response['post'] = $flag;
			$response['leftLeftNum'] = $roomMgr -> getCardLeftNum($leftID);
			$response['midLeftNum'] = $roomMgr -> getCardLeftNum($userID);
			$response['rightLeftNum'] = $roomMgr -> getCardLeftNum($rightID);
		}
		else
		{
			$response['totalPeople'] = $totalPeople;
			$response['postQueueNum'] = $serPostCardTimes;
			$response['validPostID'] = $validatePosterID;

			$response['playerLeftID'] = $leftID;
			$response['playerRightID'] = $rightID;

			$leftInfo = $roomMgr -> getPlayerCardList($leftID);
			$response['leftPlayerLeft'] =(int)$leftInfo[0];
			$response['leftPostCardList'] = $leftPostList;

			$rightInfo = $roomMgr -> getPlayerCardList($rightID);
			$response['rightPlayerLeft'] = $rightInfo[0];
			$response['rightPostCardList'] = $rightPostList;

			$selfCardStr = $roomMgr->getPlayerCardList($userID);
			$response['selfCardList'] = $cardMgr -> transferStringToArray($selfCardStr[1]);
			$response['selfPostList'] = $cardMgr -> transferStringToArray($roomMgr -> getPlayerPostList($userID));
			$response['debug'] = 'debug 2';
			$response['post'] = $flag;

			$response['leftLeftNum'] = $roomMgr -> getCardLeftNum($leftID);
			$response['midLeftNum'] = $roomMgr -> getCardLeftNum($userID);
			$response['rightLeftNum'] = $roomMgr -> getCardLeftNum($rightID);
		}
	}
	else
	{
		if(isset($_POST['cardList']))
		{
			$response['issspost'] = 'yes';
			$postList = $_POST['cardList'];
			$response['postVal'] = $postList;
			$isPostValidate = false;
			if($cardMgr -> isValidType($postList))
			{
				$response['postValid'] = 'yes';

				if ($leftPostList[0] == '' || $leftPostList[0] == ' ')
				{
					$response['leftPostVal'] = 'empty';
					if ($rightPostList[0] == '' || $rightPostList[0] == ' ')
					{
						$whereEmp = 'both';
						$response['bothEmp'] = 'yes';
						$cardMgr->userPostCard($roomID, $userID, $postList);
						$isPostValidate = true;
						$postListStr = $cardMgr->transferArrayToString($postList);
						$idx = $cardMgr->getUserIndex($userID, $roomID);
						$roomMgr->updatePostList($roomID, $idx, $postListStr);
						$roomMgr->addPostCardQueueNum();
					}
					else
					{
						$whereEmp = 'only left ';
						$response['onlyLeftEmp'] = 'yes';
						if ($cardMgr->isOverPre($rightPostList, $postList))
						{
							$whereEmp = 'only left over';
							$cardMgr->userPostCard($roomID, $userID, $postList);
							$isPostValidate = true;
							$postListStr = $cardMgr->transferArrayToString($postList);
							$idx = $cardMgr->getUserIndex($userID, $roomID);
							$roomMgr->updatePostList($roomID, $idx, $postListStr);
							$roomMgr->addPostCardQueueNum();
						}
					}
				}
				else
				{
					$response['leftNo'] = 'yes';
					if ($cardMgr->isOverPre($leftPostList, $postList))
					{
						$whereEmp = 'isove';
						$cardMgr->userPostCard($roomID, $userID, $postList);
						$isPostValidate = true;
						$postListStr = $cardMgr->transferArrayToString($postList);
						$idx = $cardMgr->getUserIndex($userID, $roomID);
						$roomMgr->updatePostList($roomID, $idx, $postListStr);
						$roomMgr->addPostCardQueueNum();
					}
				}
				$whereEmp = 'no ';
			}

			$response['leftVal'] = $leftPostList[0];
			$response['rightVal'] = $rightPostList[0];
			$response['emp'] = $whereEmp;
			$response['possss'] = $postList;
			$response['postQueueNum'] = $roomMgr -> getPostCardQueueNum();;
			$response['isPostValidate'] = $isPostValidate;


			$validatePosterID = $roomMgr -> getValidatePostPlayerID();
			$response['validPostID'] = $validatePosterID;

			$selfCardStr = $roomMgr->getPlayerCardList($userID);
			$response['selfCardList'] = $cardMgr -> transferStringToArray($selfCardStr[1]);
			$response['selfPostList'] = $postList;

			$leftInfo = $roomMgr -> getPlayerCardList($leftID);
			$response['leftPlayerLeft'] =$leftInfo[0];
			$response['leftPostCardList'] = $leftPostList;

			$rightInfo = $roomMgr -> getPlayerCardList($rightID);
			$response['rightPlayerLeft'] = $rightInfo[0];
			$response['rightPostCardList'] = $rightPostList;

			$response['playerLeftID'] = $leftID;
			$response['playerRightID'] = $rightID;
			$response['debug'] = '4';

			$response['leftLeftNum'] = $roomMgr -> getCardLeftNum($leftID);
			$response['midLeftNum'] = $roomMgr -> getCardLeftNum($userID);
			$response['rightLeftNum'] = $roomMgr -> getCardLeftNum($rightID);
		}
		//用户要不起直接过
		else
		{
			$cardMgr->userPostCard($roomID, $userID, array());
			$postListStr = '';
			$idx = $cardMgr->getUserIndex($userID, $roomID);
			$roomMgr->updatePostList($roomID, $idx, $postListStr);
			$roomMgr->addPostCardQueueNum();

			$response['postQueueNum'] = $serPostCardTimes++;
			$response['isPostValidate'] = true;
			$validatePosterID = $roomMgr -> getValidatePostPlayerID();
			$response['validPostID'] = $validatePosterID;

			$selfCardStr = $roomMgr->getPlayerCardList($userID);
			$response['selfCardList'] = $cardMgr -> transferStringToArray($selfCardStr[1]);
			$response['selfPostList'] = array();

			$leftInfo = $roomMgr -> getPlayerCardList($leftID);
			$response['leftPlayerLeft'] =$leftInfo[0];
			$response['leftPostCardList'] = $leftPostList;

			$rightInfo = $roomMgr -> getPlayerCardList($rightID);
			$response['rightPlayerLeft'] = $rightInfo[0];
			$response['rightPostCardList'] = $rightPostList;

			$response['playerLeftID'] = $leftID;
			$response['playerRightID'] = $rightID;
			$response['debug'] = '6';

			$response['leftLeftNum'] = $roomMgr -> getCardLeftNum($leftID);
			$response['midLeftNum'] = $roomMgr -> getCardLeftNum($userID);
			$response['rightLeftNum'] = $roomMgr -> getCardLeftNum($rightID);
		}
	}

	if($response['totalPeople'] == 3 && ($response['leftLeftNum']  <= 0 || $response['midLeftNum'] <= 0|| $response['rightLeftNum'] <= 0))
	{
		$response['isGameOver'] = true;
//		$dbObj = new \CardGame\Database();
//		$dh = $dbObj -> getDBHandle();
//		$square = new \CardGame\SquareManager();
//		$square -> delRoom($roomID);
		if($response['leftLeftNum'] <= 0)
		{
			$response['winnerID'] = $leftID;
		}
		else if($response['rightLeftNum'] <= 0)
		{
			$response['winnerID'] = $rightID;
		}
		else
		{
			$response['winnerID'] = $userID;
		}


	}
	else
	{
		$response['isGameOver'] = false;
	}


echo json_encode($response);