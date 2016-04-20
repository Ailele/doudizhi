<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 2016/3/27
 * Time: 12:19
 */

namespace CardGame;

class RoomManager
{
	private $dbhandle;
	private $roomID;
	private $userList;
	private $totalUserNum;
	private $dbobj;
	private $lordID;
	private $playerIndex;

	public function __construct($roomID)
	{
		$this -> dbobj = new Database();
		$this -> dbhandle = $this -> dbobj -> getDBHandle();
		$this -> roomID = $roomID;
	}

	/**返回对应序号对应的用户名按进场顺序为1，2，3 string
	 * @param $idx
	 * @return bool|string
	 */
	public function getPlayerIDByOrder($idx)
	{
		if($idx > 3 || $idx < 1)
		{
			return false;
		}
		$ID = '';

		switch($idx)
		{
			case 1:
				$sql = "select roomPlayerOneID from room where roomID='$this->roomID';";
				$query = $this -> queryDB($sql);
				$ID = array_values($query)[0];
				break;
			case 2:
				$sql = "select roomPlayerTwoID from room where roomID='$this->roomID';";
				$query = $this -> queryDB($sql);
				$ID = array_values($query)[0];
				break;
			case 3:
				$sql = "select roomPlayerThreeID from room where roomID='$this->roomID';";
				$query = $this -> queryDB($sql);
				$ID = array_values($query)[0];
				break;
			default:
				break;
		}
		return $ID;
	}

	public function getPlayerCardList($id)
	{
		$sql = "select cardLeftNum, cardList from card where cardRoomID='$this->roomID' and cardUserName='$id';";
		$query = $this -> dbhandle -> query($sql);
		$result = $query -> fetch_assoc();
		return array($result['cardLeftNum'], $result['cardList']);
	}

	public function getPlayerPostList($id)
	{
		$idx = $this -> getUserIndex($id, $this -> roomID);
		switch($idx)
		{
			case 1:
				$sql = "select roomPlayerOnePostList from room where roomID='$this->roomID';";
				break;
			case 2:
				$sql = "select roomPlayerTwoPostList from room where roomID='$this->roomID';";
				break;
			case 3:
				$sql = "select roomPlayerThreePostList from room where roomID='$this->roomID';";
				break;
		}

		$query = $this -> dbhandle -> query($sql);
		$result = $query -> fetch_assoc();
		if($idx = 1)
		{
			if(isset($result['roomPlayerOnePostList']))
			{
				return $result['roomPlayerOnePostList'];
			}
			return '';
		}
		else if($idx = 2)
		{
			if(isset($result['roomPlayerTwoPostList']))
			{
				return $result['roomPlayerOnePostList'];
			}
			return '';
		}
		else
		{
			if(isset($result['roomPlayerThreePostList']))
			{
				return $result['roomPlayerOnePostList'];
			}
			return '';
		}
		return $result;
	}

	/**返回当前房间的玩家列表 array
	 * @return mixed
	 */
	public function getUserList()
	{
		$userList = array();
		$sql = "select roomTotalPlayer from room";
		$query = $this -> dbhandle -> query($sql);
		$totalPlayerNum = (int)$query -> fetch_assoc();
		if ($totalPlayerNum > 0)
		{

			for($idx = 1; $idx < 4; $idx++)
			{
				$userList[] = $this -> getPlayerIDByOrder($idx);
			}
			if(count($userList) === $totalPlayerNum)
			{
				return $this -> userList= $userList;
			}
		}
		return $this -> userList= $userList;
	}

	/**获得玩家的队列 1， 2， 3 对应的玩家ID array(idx => id);
	 * @return mixed
	 */
	public function getPlayerIndexList()
	{
		$this -> playerIndex[1] = $this -> getPlayerIDByOrder(1);
		$this -> playerIndex[2] = $this -> getPlayerIDByOrder(2);
		$this -> playerIndex[3] = $this -> getPlayerIDByOrder(3);

		return $this -> playerIndex;
	}

	public function setPlayerAround()
	{
		$playerIndex = $this -> getPlayerIndexList();
		$dbhandle = $this -> dbhandle;
		$sql = "update user set userLeftPlayerID = '$playerIndex[3]', userRightPlayerID = '$playerIndex[2]' WHERE userName = '$playerIndex[1]'";
		$query1 = $dbhandle -> query($sql);
		$sql = "update user set userLeftPlayerID = '$playerIndex[1]', userRightPlayerID = '$playerIndex[3]' WHERE userName = '$playerIndex[2]'";
		$query2 = $dbhandle -> query($sql);
		$sql = "update user set userLeftPlayerID = '$playerIndex[2]', userRightPlayerID = '$playerIndex[1]' WHERE userName = '$playerIndex[3]'";
		$query3 = $dbhandle -> query($sql);

		return ($query1 && $query2 && $query3);
	}


	/**获得当前玩家总数量
	 * @return int
	 */
	public function getTotalPlayerNum()
	{
		$sql = "select roomTotalPlayer  from room where roomid='$this->roomID';";
		$result = $this -> queryDB($sql);
		if(isset(array_values($result)[0]))
		{
			return $this -> totalUserNum = array_values($result)[0];
		}
		return $this -> totalUserNum = 0;
	}


	/**设置地主及地主位置，以及地主前后玩家ID
	 * @return bool|int|string
	 */
	public function setLord()
	{
		//$playerOneScore = $this -> getQDZScoreByOrder(1);
		//$playerTwoScore = $this -> getQDZScoreByOrder(2);
		//$playerThreeScore = $this -> getQDZScoreByOrder(3);

		$playerOneScore = rand(1, 10);
		$playerTwoScore = rand(8, 16);
		$playerThreeScore = rand(14, 20);

		if($playerOneScore > $playerTwoScore)
		{
			if($playerOneScore > $playerThreeScore)
			{
				$this -> lordID =  $this -> getPlayerIDByOrder(1);
				$lordIndex = 0;
			}
			else
			{
				$this -> lordID = $this -> getPlayerIDByOrder(3);
				$lordIndex = 2;
			}
		}
		else
		{
			if($playerTwoScore > $playerThreeScore)
			{
				$this -> lordID = $this -> getPlayerIDByOrder(2);
				$lordIndex = 1;
			}
			else
			{
				$this -> lordID = $this -> getPlayerIDByOrder(3);
				$lordIndex = 2;
			}
		}

		$sql = "update room set roomValidPostCardID = '$this->lordID',roomDiZhuPlayerID = '$this->lordID', lordIndex = '$lordIndex' WHERE roomID='$this->roomID';";
		$query = $this -> dbhandle -> query($sql);

		if($query)
		{
			return $this -> lordID;
		}

		return -1;
	}

	/**获得地主ID
	 * @return int
	 */
	public function getLordID($roomID)
	{
		$sql = "select roomDiZhuPlayerID from room where roomID = '$roomID';";

		$query = $this -> queryDB($sql);
		$result = array_values($query)[0];
		if(isset($result))
		{
			return $this -> lordID = $result;
		}
		return -1;
	}

	/**获得地主索引位
	 * @return int
	 */
	public function getLordIndex()
	{
		$sql = "select lordIndex from room where roomID ='$this->roomID';";
		$query = $this -> queryDB($sql);
		$result = array_values($query)[0];

		if(isset($result))
		{
			return $result;
		}
		return -1;
	}

	public function getRelateLordPosition($id)
	{
		$lordIndex = $this -> getLordIndex();
		$lordIndex++;
		$uindex = $this -> getUserIndex($id, $this ->roomID);
		if($uindex == 1)
		{
			if($lordIndex == 2)
			{
				return 'right';
			}
			else if($lordIndex == 3)
			{
				return 'left';
			}
			else
			{
				return 'mid';
			}
		}
		else if($uindex == 2)
		{
			if($lordIndex == 1)
			{
				return 'left';
			}
			else if($lordIndex == 2)
			{
				return 'mid';
			}
			else
			{
				return 'right';
			}
		}
		else
		{
			if($lordIndex == 1)
			{
				return 'right';
			}
			else if($lordIndex == 2)
			{
				return 'left';
			}
			else
			{
				return 'mid';
			}
		}
	}

	/**从数据库查找sql语句，不可写入
	 * @param $sql
	 * @return array
	 */
	public function
	 queryDB($sql)
	{
		$query = $this -> dbhandle -> query($sql);
		return $query -> fetch_assoc();
	}


	/**设置发牌队列值 第多少次发牌
	 * @param $queueNum
	 * @return int
	 */
	public function setPostCardQueueNum($queueNum)
	{
		$sql = "update room set roomPostCardTimes = $queueNum where roomID ='$this->roomID';";
		$query = $this -> dbhandle -> query($sql);
		if($query)
		{
			return $queueNum;
		}
		return -1;
	}

	/**获得发牌队列值 当前为第多少次出牌
	 * @return int
	 */
	public function getPostCardQueueNum()
	{
		$sql = "select roomPostCardTimes from room where roomID = '$this->roomID';";

		$result = $this -> queryDB($sql);
		$queueNum = array_values($result)[0];
		if(isset($queueNum))
		{
			return (int)$queueNum;
		}
		return -1;
	}

	/**更新队列值
	 * @return int
	 */
	public function addPostCardQueueNum()
	{
		$nowQueueNum = $this -> getPostCardQueueNum();
		return $this -> setPostCardQueueNum($nowQueueNum + 1);
	}

	public function getCardLeftNum($id)
	{
		$cardInfo = $this -> getPlayerCardList($id);
		$cardNum = $cardInfo[0];
		$cardVal = $cardInfo[1];
		if($cardNum == '0' && $cardVal == '')
		{
			return 0;
		}
		else
		{
			return (int)$cardNum;
		}
	}
	public function getValidatePostPlayerID()
	{
		$postQueue = $this -> getPostCardQueueNum();
		$lordIndex = $this -> getLordIndex();
		$idx = ($postQueue) % 3 + $lordIndex;

		if($idx > 3)
		{
			$idx = $idx % 3;
		}
		$id =$this -> getPlayerIDByOrder($idx);
		return $id;
	}

	public function getLRPostList($uid)
	{
		$sql = "select userLeftPlayerID, userRightPlayerID from user where userName ='$uid';";
		$query = $this -> dbhandle -> query($sql);
		$result = $query -> fetch_assoc();

		$leftID = $result['userLeftPlayerID'];
		$rightID = $result['userRightPlayerID'];

		$sql = "select roomPlayerOnePostList, roomPlayerTwoPostList,roomPlayerThreePostList from room where roomID ='$this->roomID';";
		$query = $this -> dbhandle -> query($sql);
		$result = $query -> fetch_assoc();

		if($this -> getUserIndex($uid, $this -> roomID) == 1)
		{
			$LRPostList = array(
					array($leftID , $result['roomPlayerThreePostList']),
					array($rightID, $result['roomPlayerTwoPostList'])
					);
		}
		else if($this -> getUserIndex($uid, $this -> roomID) == 2)
		{
			$LRPostList = array(
					array($leftID, $result['roomPlayerOnePostList']),
					array($rightID, $result['roomPlayerThreePostList'])
			);
		}
		else if($this -> getUserIndex($uid, $this -> roomID) == 3)
		{
			$LRPostList = array(
					array($leftID, $result['roomPlayerTwoPostList']),
					array($rightID, $result['roomPlayerOnePostList'])
			);
		}
		return $LRPostList;
	}

	public function updatePostList($roomID, $idx, $postListStr)
	{
		switch($idx)
		{
			case 1:
				$sql = "update room set roomPlayerOnePostList ='".$postListStr."' where roomID='$roomID';";
				$this -> dbhandle -> query($sql);
				break;
			case 2:
				$sql = "update room set roomPlayerTwoPostList ='".$postListStr."' where roomID='$roomID';";
				$this -> dbhandle -> query($sql);
				break;
			case 3:
				$sql = "update room set roomPlayerThreePostList ='".$postListStr."' where roomID='$roomID';";
				$this -> dbhandle -> query($sql);
				break;
		}
	}

	public function getUserIndex($uid, $roomID)
	{
		$sql = "select roomPlayerOneID, roomPlayerTwoID, roomPlayerThreeID from room where roomID = '$roomID';";
		$query = $this -> dbhandle -> query($sql);
		$result = $query -> fetch_assoc();

		if($uid == $result['roomPlayerOneID'])
		{
			return 1;
		}
		else if($uid == $result['roomPlayerTwoID'])
		{
			return 2;
		}
		else if($uid == $result['roomPlayerThreeID'])
		{
			return 3;
		}
		return -1;
	}

	private function transferArrayToString(array $arr)
	{
		$str = '';

		foreach($arr as $value)
		{
			$str .= $value." ";
		}

		//去掉末尾的多余空格
		return trim($str);
	}

	/**将空格分隔的字符串转化为数组
	 * @param $string
	 * @return array
	 */
	private function transferStringToArray($string)
	{
		$string = trim($string);
		$array = preg_split('/\s/', $string);

		return $array;
	}


}