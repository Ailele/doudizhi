drop database landlord;
create database landlord;
use landlord;

create table user(
	#用户信息
	userName                 varchar(60),
	userPsw                  varchar(60),
	userTotalPlayTimes       int default 0,
	userTotalWinTimes	 int default 0,
	userTotalCoins           int default 3000,
	userAvatar		 varchar(32) default 'default',
	userLeftPlayerID	 varchar(60),
	userRightPlayerID	 varchar(60)
);

create table square(
	#页面总共的房间数和玩家列表
	squareTotalRoomNum 	int,
	squareRoomList 		text
);

create table room(

	#房间信息及状态
	roomID			 int,
	roomTotalPlayer		 int,		
 
	roomDiZhuPlayerID	 varchar(60),		
	lordIndex		 int,

	#当前出牌轮回次数及合法出牌ID
	roomPostCardTimes	 int default 1,
	roomValidPostCardID 	 varchar(60) ,

	#玩家进入房间顺序，出牌顺序 1， 2， 3
	roomPlayerOneID		 varchar(60),
	roomPlayerTwoID		 varchar(60),
	roomPlayerThreeID	 varchar(60),

	roomPlayerOnePostList	 varchar(255) default '',
	roomPlayerTwoPostList	 varchar(255) default '',
	roomPlayerThreePostList	 varchar(255) default ''
);

create table card(
	#当前保持的牌信息所属房间
	cardRoomID		 varchar(32),
	carduserName             varchar(60),

	#手中剩余牌数和剩余牌面
	cardLeftNum		 int,
	cardList 		 text
);


truncate table user;
truncate table room;
truncate table square;
truncate table card;

select * from user;
select * from card;
select * from square;
select * from room\G
