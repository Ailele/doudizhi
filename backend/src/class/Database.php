<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 2016/3/26
 * Time: 14:58
 */

namespace CardGame;


class Database
{
	private $dbHandle;

	/**
	 * Database constructor.
	 * @param string $host  主机地址默认localhost
	 * @param string $user	数据库用户名
	 * @param string $psw   密码
	 * @param string $db    选择数据据库
	 *在这里更改你的数据库密码
	 */
	public function __construct($host = "localhost", $user = "root", $psw = "12341234", $db = "landlord")
	{
		$DBHandle = new \mysqli($host, $user, $psw, $db);

		if ($DBHandle)
		{
			$this -> dbHandle = $DBHandle;
		}
		else
		{
			echo "数据库连接错误";
			exit;
		}
	}

	public function getDBHandle()
	{
		return $this -> dbHandle;
	}

	public function registerUser($name, $psw)
	{
		$sql = 'select userName from user WHERE  userName = "'.$name.'";';
		$queryResult = $this -> dbHandle -> query($sql);
		$result = $queryResult -> fetch_assoc();
		if (!isset($result['userName']))
		{
			if(isset($psw))
			{
				$sql = 'insert user(userName, userPsw) values("'.$name.'","'.$psw.'")';
				$this -> dbHandle -> query($sql);
				return true;
			}
		}

		return false;
	}

	public function Login($name, $psw)
	{
		$sql = 'select * from user WHERE  userName = "'.$name.'";';
		$queryResult = $this -> dbHandle -> query($sql);
		$result = $queryResult -> fetch_assoc();
		if ($result['userName'] === $name && $result['userPsw'] === $psw)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function isExist($name)
	{
		$sql = 'select userName from user WHERE  userName = "'.$name.'";';
		$queryResult = $this -> dbHandle -> query($sql);
		$result = $queryResult -> fetch_assoc();
		if ($result['userName'] === $name)
		{
			return true;
		}
		return false;
	}

	public function getRoomInfo($roomID)
	{
		$sql = 'select * from room  WHERE  roomID = "'.$roomID.'";';
		$queryResult = $this -> dbHandle -> query($sql);
		return $result = $queryResult -> fetch_assoc();
	}

	public function getUserInfo($name)
	{
		$sql = 'select * from user WHERE  userName = "'.$name.'";';
		$queryResult = $this -> dbHandle -> query($sql);
		return $result = $queryResult -> fetch_assoc();
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

new Database();