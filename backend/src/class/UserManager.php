<?php
/**
 * Created by PhpStorm.
 * User: mao
 * Date: 2016/3/27
 * Time: 13:58
 */

namespace CardGame;


class UserManager
{
	private $dbhandle;
	private $uid;
	private $userInfo;

	public function __construct($uid)
	{
		$this -> uid = $uid;
		$dbobj = new Database();
		$this -> dbhandle = $dbobj -> getDBHandle();
	}

	public function updateUserInfo($column, $value)
	{
		$sql = "update user set $column = $value where userName ='{$this -> uid}';";
		$result = $this -> dbhandle -> query($sql);
		if ($result)
		{
			return true;
		}
		return false;
	}

	public function updateLocalInfo()
	{
		$sql = "select * from user WHERE userName ='{$this -> uid}'; ";
		$query = $this -> dbhandle -> query($sql);
		$result = $query -> fetch_assoc();

		if($result)
		{
			$this -> userInfo = $result;
			return true;
		}
		return false;
	}

	public function getUserInfo($column)
	{
		return $this -> userInfo[$column];
	}
}