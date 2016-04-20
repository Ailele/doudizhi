<?php
/**
 * Created by PhpStorm
 * User: mao
 * Date: 2016/3/27
 * Time: 16:57
 */

namespace CardGame;

class CardManager
{
	private $dbhandle;

	public function __construct()
	{
		$dbobj = new Database();
		$this -> dbhandle = $dbobj -> getDBHandle();
	}

	/**生成一副牌和三张地主牌，其中cardList[3]为三张地主牌
	 * @return array
	 */
	public function genereteCard()
	{
		$cards = range(1, 54);
		shuffle($cards);
		$cards = array_chunk($cards, 17);
		$cardlist[0] = $cards[0];
		$cardlist[1] = $cards[1];
		$cardlist[2] = $cards[2];
		array_push($cardlist[2], $cards[3][0]);
		array_push($cardlist[2], $cards[3][1]);
		array_push($cardlist[2], $cards[3][2]);

		return $cardlist;
	}

	public function setInitCardList($roomID, $lord, $farmOne, $farmeTwo)
	{
		$sql = "delete from card where  cardRoomID = '$roomID' and (carduserName = '$farmOne' or
									    carduserName = '$farmeTwo' or
									    carduserName = '$lord');";
		$this -> dbhandle -> query($sql);

		$cards = $this ->genereteCard();

		$lordCardList = $this -> transferArrayToString($cards[2]);
		$farmOneCardList = $this -> transferArrayToString($cards[0]);
		$farmeTwoCardList = $this -> transferArrayToString($cards[1]);

		$lordSql = "insert card(cardRoomID,carduserName,cardLeftNum,cardList) values('$roomID', '$lord', 20, '$lordCardList');";
		$farmOneSql = "insert card(cardRoomID,carduserName,cardLeftNum,cardList) values('$roomID', '$farmOne', 17, '$farmOneCardList');";
		$farmeTwoSql = "insert card(cardRoomID,carduserName,cardLeftNum,cardList) values('$roomID', '$farmeTwo', 17, '$farmeTwoCardList');";
		
		if($this -> dbhandle -> query($lordSql) && $this -> dbhandle -> query($farmOneSql) && $this -> dbhandle -> query($farmeTwoSql))
		{
			return true;
		} 
	}


	public function setUserCardList($roomID, $userName, $cardList)
	{
		$len = count($cardList);
		$cardList = $this -> transferArrayToString($cardList);
		$sql = "insert card(cardRoomID, carduserName, cardLeftNum, cardList) values('$roomID', '$userName', $len, '$cardList')";
		$query = $this -> dbhandle -> query($sql);
		return $query;
	}

	public function getUserCardInfo($roomID, $userName)
	{
		$sql = "select cardLeftNum, cardList from card where cardRoomID = '$roomID' and carduserName = '$userName';";
		$query = $this -> dbhandle -> query($sql);
		$result = $query -> fetch_assoc();
		$cardLeft = (int)$result['cardLeftNum'];
		$cardList = $this -> transferStringToArray($result['cardList']);
		return array($cardLeft, $cardList);
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

	public function userPostCard($roomID, $userName, array $delCardList)
	{
		if(empty($delCardList) || (count($delCardList) == 1 && $delCardList[0] == ''))
		{
			return true;
		}
		$sql = "select cardLeftNum, cardList from card where cardRoomID = '$roomID' and carduserName = '$userName';";
		$query = $this -> dbhandle -> query($sql);
		$result = $query -> fetch_assoc();
		$cardLeft = (int)$result['cardLeftNum'];
		$cardList = $this -> transferStringToArray($result['cardList']);

		$newCardList = array_del($cardList, $delCardList);

		$newlen = $cardLeft - count($delCardList);
		$newCardList = $this -> transferArrayToString($newCardList);
		$sql = "update card set cardLeftNum='$newlen', cardList ='$newCardList' where cardRoomID = '$roomID' and carduserName = '$userName';";
		$query = $this -> dbhandle -> query($sql);
		return $query;
	}

	/**获得单张牌的权重值 3最小 大王最大
	 * @param $value
	 * @return int
	 */
	public function getGrade($value)
	{
		$grade = 0;

		if ($value == 53)
		{
			$grade = 16;
		}
		else if ($value == 54)
		{
			$grade = 17;
		}
		else
		{
			$modResult = $value % 13;

			if ($modResult == 1)
			{
				$grade = 14;
			}
			else if ($modResult == 2)
			{
				$grade = 15;
			}
			else if ($modResult == 3)
			{
				$grade = 3;
			}
			else if ($modResult == 4)
			{
				$grade = 4;
			}
			else if ($modResult == 5)
			{
				$grade = 5;
			}
			else if ($modResult == 6)
			{
				$grade = 6;
			}
			else if ($modResult == 7)
			{
				$grade = 7;
			}
			else if ($modResult == 8)
			{
				$grade = 8;
			}
			else if ($modResult == 9)
			{
				$grade = 9;
			}
			else if ($modResult == 10)
			{
				$grade = 10;
			}
			else if ($modResult == 11)
			{
				$grade = 11;
			}
			else if ($modResult == 12)
			{
				$grade = 12;
			}
			else if ($modResult == 0)
			{
				$grade = 13;
			}

		}

		return $grade;
	}

	public function getImgSrc($value)
	{
		$src = "http://localhost/frontend/img/card/$value.png";
		return $src;
	}

	/**
	 * 十种牌型
	 * 1. 单 2.对子 3.3不带 4.3带1 5.炸弹 6.顺子 7.4带2 8.连队 9.飞机 10.对王
	 */

	/**按牌面数量进行排序
	 * @param $cardList
	 * @return array
	 */
	public function sortCardList($cardList)
	{
		$len = count($cardList);
		$list = array();
		for($idx = 3; $idx < 18; $idx++)
		{
			$list[$idx] = 0;
		}

		for ($idx = 0; $idx < $len; $idx++)
		{
			$value = $this -> getGrade($cardList[$idx]);
			$list[$value]++;
		}
		arsort($list);

		$result = array();
		$loc = 0;
		foreach($list as $key => $value)
		{
			for($idx = 0; $idx < $value; $idx++)
			{
				$result[$loc++] = $key;
			}
			if($value === 0)
			{
				break;
			}
		}
		return $result;
	}

	function transCardToG($arrayList)
	{
		$array = array();
		$len = count($arrayList);

		for ($idx =0; $idx < $len; $idx++) 
		{ 
			$array[] = $this -> getGrade($arrayList[$idx]);	
		}
		return $array;
	}

	/**是否为单
	 * @param $postList
	 * @return bool
	 */
	public function isDanZhang($postList)
	{
		if (count($postList) === 1)
		{
			return $this -> getGrade($postList[0]);
		}
		return false;
	}

	public function isDuiZi($postList)
	{
		if(count($postList) == 2 && $this -> getGrade($postList[0]) == $this-> getGrade($postList[1]))
		{
			return $this -> getGrade($postList[0]);
		}
		return false;
	}

	public function isSanBuDai($postList)
	{
		if(count($postList) == 3 && $this -> getGrade($postList[0]) == $this-> getGrade($postList[1])
				         && $this -> getGrade($postList[2]) == $this-> getGrade($postList[1]))
		{
			return $this -> getGrade($postList[0]);
		}
		return false;
	}

	public function isSanDai($postList)
	{
		$postList = $this -> sortCardList($postList);

		if(count($postList) == 4 && $postList[0] == $postList[1]
					 && $postList[1] == $postList[2]
					 && $postList[2] != $postList[3])
		{
			return $postList[0];
		}
		if(count($postList) == 5 && $postList[0] == $postList[1]
					 && $postList[1] == $postList[2]
					 && $postList[3] == $postList[4])
		{
			return $this -> getGrade($postList[0]);
		}
		return false;
	}

	public function isBoom($postList)
	{
		$postList = $this->sortCardList($postList);

		if (count($postList) == 4 && $postList[0] == $postList[1]
					  && $postList[1] == $postList[2]
				          && $postList[2] == $postList[3]
		)
		{
			return $this -> getGrade($postList[0]);
		}
		return false;
	}

	public function isWangBoom($postList)
	{
		$postList = $this -> sortCardList($postList);
		if(count($postList) === 2 && $postList[0] > 15 && $postList[1] > 15)
		{
			return $this -> getGrade($postList[0]);
		}
		return false;
	}

	public function isDanShun($postList)
	{
		$postList = $this -> sortCardList($postList);
		sort($postList);
		$len = count($postList);
		if($len >= 5)
		{
			if($postList[$len - 1] >= 15)
			{
				return false;
			}
			for($idx = 0; $idx < $len - 2; $idx++)
			{
				if ($postList[$idx] !== ($postList[$idx + 1] - 1))
				{
					return false;
				}
			}
			return $this -> getGrade($postList[$len - 1]);
		}
		return false;
	}

	public function isShuangShun($postList)
	{
		$postList = $this -> sortCardList($postList);
		sort($postList);
		$len = count($postList);
		if( $len < 6 || $len % 2 != 0 || $postList[$len - 1] > 14)
		{
			return false;
		}

		for($idx = 0; $idx < $len - 1; $idx++)
		{
			if($idx % 2 != 0)
			{
				if($postList[$idx] != $postList[$idx + 1] - 1)
				{
					return false;
				}
			}
			else
			{
				if($postList[$idx] != $postList[$idx + 1])
				{
					return false;
				}
			}
		}
		return $this -> getGrade($postList[$len - 1]);
	}

	public function isFeiJiBuDai($postList)
	{
		$postList = $this -> sortCardList($postList);
		sort($postList);
		$len = count($postList);
		
		if($len < 6 || $len % 3 != 0)
		{
			return false;
		}

		for($idx = 0; $idx < $len - 1; $idx++)
		{
			if($idx % 3 == 0)
			{
				if($postList[$idx] == 15 || !($postList[$idx] == $postList[$idx + 1] && $postList[$idx] == $postList[$idx + 2]))
				{
					return false;
				}
				continue;
			}
			if($idx % 3 == 2)
			{
				if($postList[$idx] == 15 || $postList[$idx + 1] - 1 != $postList[$idx] || $postList[$idx + 1] == 15)
				{
					return false;
				}
			}

		}
		return $this -> getGrade($postList[$len - 1]);

	}

	public function isFeiJiDai($postList)
	{
		$len = count($postList);
		$postList = $this -> sortCardList($postList);
		$size = $len / 4;
		$mod = $len % 4;
		if($len < 20)
		{
			if($mod == 0)
			{
				$sub = array_slice($postList, 0, $size * 3);
				sort($sub);
				if($this -> isFeiJiBuDai($sub))
				{
					return $this -> getGrade($sub[count($sub) - 1]);
				}
			}
			else
			{
				$subHead = array_slice($postList, 0, $mod * 3);
				sort($subHead);
				if($this -> isFeiJiBuDai($subHead))
				{
					$subTail = array_slice($postList, $mod * 3);
					sort($subTail);
					$flag = 0;
					for($idx = 0; $idx < $mod; $idx++)
					{
						if(!($this -> isDuiZi(array($subTail[$idx *2], $subTail[$idx * 2 + 1]))))
						{
							$flag = 1;
						}
					}
					if($flag)
					{
						return false;
					}
					return $this -> getGrade($subHead[$mod * 3 -1]);
				}
			}
			return false;
		}
		else if($len == 20)
		{
			$postList = $this -> sortCardList($postList);
			$subPostList4 = array_slice($postList, 0, 12);
			$subPostList5 = array_slice($postList, 0, 15);
			rsort($subPostList4);
			rsort($subPostList5);

			if($this -> isSanBuDai($subPostList5))
			{
				return $this -> getGrade($subPostList5[0]);
			}
			else if($this -> isSanBuDai($subPostList4))
			{
				$sub = array_slice($postList, 12);
				if($this->isShuangShun($sub))
				{
					return $this -> getGrade($subPostList4[0]);
				}
				return false;
			}
			
			return false;
		}

	}

	public function isFeiJi($postList)
	{
		$postList = $this -> sortCardList($postList);
		if($return = $this -> isFeiJiBuDai($postList))
		{
			return $return;
		}
		if($return = $this -> isFeiJiDai($postList))
		{
			return $return;
		}
		return false;
	}

	public function isSiDaiEr($postList)
	{
		$postList = $this -> sortCardList($postList);
		$len = count($postList);

		if($len != 6)
		{
			return false;
		}

		if($return = $this -> isBoom(array_slice($postList, 0, 4)))
		{
			return $return ;
		}
		return false;
	}

	public function isOverPre($prePostList, $curPostList)
	{
		if(($prePostList == '' || $prePostList == ' ' ||  $prePostList[0] == '' || $prePostList[0] == ' ' ) && $this -> isValidType($curPostList))
		{
			return true;
		}
		if($this -> isWangBoom($prePostList))
		{
			return false;
		}

		if($this -> isWangBoom($curPostList))
		{
			return true;
		}

		//现牌炸 前牌不炸
		if($this -> isBoom($curPostList) && !($this -> isBoom($prePostList)))
		{
			return true;
		}

		//现不炸 前牌炸
		if($this -> isBoom($prePostList) && !($this -> isBoom($curPostList)))
		{
			return false;
		}

		//单
		if(($cur = $this -> isDanZhang($curPostList)) && ($pre = $this -> isDanZhang($prePostList)))
		{
			return $cur > $pre;
		}

		//对子
		else if(($cur = $this -> isDuiZi($curPostList)) &&( $pre = $this -> isDuiZi($prePostList)))
		{
			return $cur > $pre;
		}

		//三不带
		else if(($cur = $this -> isSanBuDai($curPostList)) && ($pre = $this -> isSanBuDai($prePostList)))
		{
			return $cur > $pre;
		}

		//炸弹
		else if(($cur = $this -> isBoom($curPostList) )&&( $pre = $this -> isBoom($prePostList)))
		{
			return $cur > $pre;
		}

		//三带
		else if(($cur = $this -> isSanDai($curPostList)) && ($pre = $this -> isSanDai($prePostList)))
		{
			return $cur > $pre;
		}

		//四带二
		else if(($cur = $this -> isSiDaiEr($curPostList)) && ($pre = $this -> isSiDaiEr($prePostList)))
		{
			return $cur > $pre;
		}

		//顺子
		else if(($cur = $this -> isDanShun($curPostList)) && ($pre = $this -> isDanShun($prePostList)))
		{
			return $cur > $pre;
		}

		//连对
		else if(($cur = $this -> isShuangShun($curPostList)) &&( $pre = $this -> isShuangShun($prePostList)))
		{
			return $cur > $pre;
		}

		//飞机
		else if(($cur = $this -> isFeiJi($curPostList)) && ($pre = $this -> isFeiJi($prePostList)))
		{
			return $cur > $pre;
		}

		return false;
	}

	public function isValidType($postlist)
	{
		if( is_array($postlist) && ($postlist[0] == ''||
						$postlist[0] == ' ' || $this -> isDanZhang($postlist)
				|| $this -> isBoom($postlist)
				|| $this -> isDanShun($postlist)
				|| $this -> isDuiZi($postlist)
				|| $this -> isFeiJi($postlist)
				|| $this -> isDanZhang($postlist)
				|| $this -> isFeiJiBuDai($postlist)
				|| $this -> isFeiJiDai($postlist)
				|| $this -> isDanShun($postlist)
				|| $this -> isSanBuDai($postlist)
				|| $this -> isSiDaiEr($postlist)
				|| $this -> isWangBoom($postlist)
				|| $this -> isShuangShun($postlist)
				|| $this -> isSanDai($postlist))
		)
		{
			return true;
		}
		return false;
	}

	/**
	 * 将数组转化为 每个值之间有一个空格的字符串
	 * @param array $arr
	 * @return string
	 */
	public function transferArrayToString(array $arr)
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
	public function transferStringToArray($string)
	{
		$string = trim($string);
		$array = preg_split('/\s/', $string);

		return $array;
	}
}
