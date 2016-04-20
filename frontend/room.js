$(function(){

	
	function requestRoom()
	{
		$.post('http://localhost/backend/src/room.php', {
			roomID : roomID, 
			uid: uid, 
			postQueueIdx: postQueueIdx },
				function(data){
					responseData = eval('(' + data + ')');
					curPostID = responseData.
					postQueueNum = 
		});
	}
})






等待时返回的json
gamming.php
request:
post(
	roomID => 'roomID', 
	udi => uid,
	postQueueNum => int,
	isPostCard => bool,
	cardList => list
	);

resonse:
json{
	isQDZ bool,
	isWaitStatus bool,
	IsPostValidate => bool,

	左右玩家ID和左右剩余牌数
	LeftPlayer => uid,
	RightPlayer => uid,

	selfPostList => array()
	selfCardNum => int

	LeftPostCardList => array()
	LeftLeftCardNum => int
	RightPostCardList => array()
	RigheLeftCardNum => int

	当前出牌序列
	postQueue => int,
	当前出牌玩家ID
	validPostID => uid,

	lordPosition => ,
}
浏览器发起请求
	服务器端收到请求
		查看QueueNum
			和服务器一样
				返回isChanged false
			不一样
				返回isChanged true
				和更新的数据

var isHandling  
var postQueue

浏览器受到json数据后
	
	判断本地isHandling
		是
			不做反应等待本地处理
		不是
			判断postQueue是否和本地一样，
			一样
				不做任何事
				isHandling false

			不一样
				判断是否有牌数为0
				是 
					更新QueueNum + 1
					isHandling false
					结束游戏计算积分,停止xhr
					显示结束按钮回到大厅

				否
					设置 isHandling true 关闭xhr
					对左右玩家进行排版 将自己的牌进行排版
						如果自己是validPostID
							显示出牌按钮和计时器
								选牌后ajax出牌，验证isValidPostList
									可以
										出牌更新QueueNum 关闭按钮和计时器
										isHandling false
									不可以
										显示提示信息
										isHandling false
								30秒后未出牌
									发送要不起
									isHandling false
										
						如果不是
							更新prePostID validPostID玩家牌面
							显示计时器
							