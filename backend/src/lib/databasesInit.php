<?php 
	//修改12
	$mysqlHandle = new mysqli('localhost', 'root', '12341234');
	$mysqlHandle -> query('CREATE DATABASE IF NOT EXISTS landlord');
	$mysqlHandle -> select_db('landlord');

	$UserSQL =<<<SQL
	CREATE  TABLE IF NOT EXISTS user(
	userName                  varchar(60),
	userPsw                   varchar(60),
	userTotalPlayTimes        int default 0,
	userTotalWinTimes	  int default 0,
	userTotalCoins            int default 3000,
	userAvatar		  varchar(32) default 'default',
	userLeftPlayerID	  varchar(60),
	userRightPlayerID	  varchar(60)
	);
SQL;

	$SquareSQL =<<<SQL
	CREATE  TABLE IF NOT EXISTS square(
	squareTotalRoomNum 	 int,
	squareRoomList 		 text
	);
SQL;

	$RoomSQL =<<<SQL
	CREATE  TABLE IF NOT EXISTS room(

	roomID			  int,
	roomTotalPlayer		  int,		
 
	roomDiZhuPlayerID	  varchar(60),		
	lordIndex		  int,

	roomPostCardTimes	  int default 1,
	roomValidPostCardID 	  varchar(60) ,

	roomPlayerOneID		  varchar(60),
	roomPlayerTwoID		  varchar(60),
	roomPlayerThreeID	  varchar(60),
 
	roomPlayerOnePostList	  varchar(255) default '',
	roomPlayerTwoPostList	  varchar(255) default '',
	roomPlayerThreePostList	  varchar(255) default ''
	);
SQL;

	$CardSQL =<<<SQL
	CREATE  TABLE IF NOT EXISTS card(
	cardRoomID		 varchar(32),
	carduserName             varchar(60),
	cardLeftNum		 int,
	cardList 		 text
	);
SQL;

	$GameOverSQL =<<<SQL
CREATE  TABLE IF NOT EXISTS record(
	id 			  varchar(60),
	gameTime		  varchar(60),
	playerOneID		  varchar(60),
	playerTwoID 		  varchar(60),
	playerThreeID  		  varchar(60),
	playerWinerID  		  varchar(60),
	playerLordID   		  varchar(60)
	  );
SQL;
	
	$user = $mysqlHandle -> query($UserSQL);
	$square = $mysqlHandle -> query($SquareSQL);
	$room = $mysqlHandle -> query($RoomSQL);
	$card = $mysqlHandle -> query($CardSQL);
	$record = $mysqlHandle -> query($GameOverSQL);
	$mysqlHandle -> close();
	if(!($user && $square && $room && $card && $record))
	{
		echo 'DATABASE INIT FAILED';
	}