<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 2016/3/26
 * Time: 19:49
 */

namespace CardGame;

class SquareManager
{
	private $roomInfoList;
	private $totalRoom;
	private $roomList;
	private $dbhandle;
	private $dbobj;

	public function __construct()
	{
		$this -> dbobj = new Database();
		$this -> dbhandle = $this-> dbobj -> getDBHandle();
		$this -> roomInfoList = $this ->  getRoomListInfo();
		$this -> getRoomIDList();
		$this -> getTotalRoomNum();
	}

	public function getRoomListInfo()
	{
		$sql = "select * from square";
		$queryResult = $this -> dbhandle -> query($sql);
		$this -> roomInfoList = $queryResult -> fetch_assoc();
		if((int)$this -> roomInfoList['squareTotalRoomNum'] < 0)
		{
			$this -> roomInfoList['squareTotalRoomNum'] = 0;
			$this -> roomInfoList['squareRoomList'] = '';
		}

		return $this -> roomInfoList;
	}

	public function getRoomIDList()
	{
		$this -> roomList = $this -> transferStringToArray($this -> roomInfoList['squareRoomList']);
		if (count($this -> roomList) === 1 && $this -> roomList[0] === '')
		{
			$this ->totalRoom = 0;
			return $this -> roomList = array();
		}
		return $this -> roomList;
	}

	public function getTotalRoomNum()
	{
		return $this -> totalRoom = $this -> roomInfoList['squareTotalRoomNum'];
	}


	public function addRoom($roomID)
	{
		$sql = "select * from 	square";
		$query = $this -> dbhandle -> query($sql);

		if(!($query -> fetch_assoc()))
		{
			$this -> dbhandle -> query("insert square(squareTotalRoomNum) values(0) ;");
		}
		$tempRoomList = $this -> roomList;
		$tempRoomList[] = $roomID;
		$tempRoomList = array_unique($tempRoomList);
		$newRoomList = $this -> transferArrayToString($tempRoomList);
		$oldRoomNum = $this -> totalRoom;
		$newRoomNum = count($tempRoomList);
		$sql = "update square set squareRoomList ='$newRoomList', squareTotalRoomNum = '$newRoomNum' where squareTotalRoomNum ='$oldRoomNum';";

		$result = $this -> dbhandle -> query($sql);
		if ($result)
		{
			$this -> totalRoom = $newRoomNum;
			$this -> roomList = $tempRoomList;
			$this -> getRoomListInfo();
			return true;
		}

		return false;
	}

	public function delRoom($roomID)
	{
		$tempRoomList = $this -> roomList;
		$tempRoomList = $this -> arrayDeleteElement($tempRoomList, $roomID);
		$newRoomList = $this -> transferArrayToString($tempRoomList);
		$oldRoomNum = $this -> totalRoom;
		$newRoomNum = count($tempRoomList);
		$sql = "update square set squareRoomList ='$newRoomList', squareTotalRoomNum = '$newRoomNum' where squareTotalRoomNum = '$oldRoomNum';";
		$result = $this -> dbhandle -> query($sql);
		if($result)
		{
			$this -> totalRoom = $newRoomNum;
			$this -> roomList = $tempRoomList;
			$this -> getRoomListInfo();
			return $result;
		}
		return false;
	}

	public function arrayDeleteElement($array, $elementValue)
	{
		$newArray = array();
		foreach($array as $value)
		{
			if ($value != $elementValue)
			{
				$newArray[] = $value;
			}
		}
		return $newArray;
	}

	/**
	 * 将数组转化为 每个值之间有一个空格的字符串
	 * @param array $arr
	 * @return string
	 */
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