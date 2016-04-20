<?php
	require_once 'requireFile.php';

	$roomID = (int)$_POST['roomID'];
	$userQueue = (int)$_POST['postQueue'];
	$userID = $_COOKIE['uid'];

	$isPost = ($_POST['post'] === '1');
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

	$validatePosterID = $roomMgr -> getValidatePostPlayerID();
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

	//等待玩家进入
	if($totalPeople != 3)
	{
		$response = array('totalPlayer' => $totalPeople, 'debug' => '1');
	}
	//状态未改变 等待其他玩家出牌
	else if($userQueue == $serPostCardTimes && $userID != $validatePosterID)
	{
		$response = array( 'debug' => '2','postQueueNum' => $serPostCardTimes,
				  'localQueue' => $userQueue,
				  'totalPeople' => $totalPeople,
				'validPostID' => $validatePosterID,
				'lordPosition' => $roomMgr -> getRelateLordPosition($userID));
	}
	else if($userQueue == $serPostCardTimes && $userID != $validatePosterID)
	//状态改变 更新本地状态
	else if($userQueue != $serPostCardTimes && $validatePosterID != $userID)
	{
		$response['debug'] = '3';
		$response['postQueueNum'] = $serPostCardTimes;
		$response['validPostID'] = $validatePosterID;

		$response['playerLeftID'] = $leftID;
		$response['playerRightID'] = $rightID;

		$leftInfo = $roomMgr -> getPlayerCardList($leftID);
		$response['leftPlayerLeft'] =$leftInfo[0];
		$response['leftPostCardList'] = $leftPostList;

		$rightInfo = $roomMgr -> getPlayerCardList($rightID);
		$response['rightPlayerLeft'] = $rightInfo[0];
		$response['rightPostCardList'] = $rightPostList;

		$selfCardStr = $roomMgr->getPlayerCardList($userID);
		$response['selfCardList'] = $cardMgr -> transferStringToArray($selfCardStr[1]);
		$response['selfPostList'] = $cardMgr -> transferStringToArray($roomMgr -> getPlayerPostList($userID));
	}
	//用户出牌
	else if($userQueue != $serPostCardTimes && $validatePosterID == $userID && $isPost)
	{
		//用户出牌
		if(isset($_POST['cardlist']))
		{
			$postList = $_POST['cardList'];
			$isPostValidate = false;
			if($cardMgr -> isValidType($postList))
			{
				if ($leftPostList[0] == '') {
					if ($rightPostList[0] == '')
					{
						$cardMgr->userPostCard($roomID, $userID, $postList);
						$isPostValidate = true;
						$postListStr = $cardMgr->transferArrayToString($postList);
						$idx = $cardMgr->getUserIndex($userID, $roomID);
						$roomMgr->updatePostList($roomID, $idx, $postListStr);
						$roomMgr->addPostCardQueueNum();
					}
					else
					{
						if ($cardMgr->isOverPre($rightPostList[0], $postList))
						{
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
					if ($cardMgr->isOverPre($leftPostList[0], $postList))
					{
						$cardMgr->userPostCard($roomID, $userID, $postList);
						$isPostValidate = true;
						$postListStr = $cardMgr->transferArrayToString($postList);
						$idx = $cardMgr->getUserIndex($userID, $roomID);
						$roomMgr->updatePostList($roomID, $idx, $postListStr);
						$roomMgr->addPostCardQueueNum();
					}
				}
			}

			$response['postQueueNum'] = $roomMgr -> getPostCardQueueNum();;
			$response['isPostValidate'] = $isPostValidate;


			if($isPostValidate)
			{
				$response['validPostID'] = $rightID;
			}
			else
			{
				$response['validPostID'] = $userID;
			}
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
			$response['validPostID'] = $rightID;

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
		}
	}
	else if($userQueue != $serPostCardTimes && $validatePosterID == $userID && !$isPost)
	{
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
			$response['debug'] = '6';
			$response['post'] = $flag;

	}
	else
	{
		$response['debug'] = 'out of range';
	}

echo json_encode($response);