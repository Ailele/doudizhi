var uid;
var roomID;
var queryTimer;
var isSetProfile = false;
var postQueue = 0;
var isSetWaitLabel = false;
var clickNowPosition = '';
var leftPlayerID;
var rightPlayerID;
var isSetOverInfo = false;
var isGameOver = false;

$(function(){

	uid = $('#uid').attr('value');
	roomID = $('#roomid').attr('value');
	queryTimer = loopQuery();

	$("#post").click(
	function () {
		clearTimeout(queryTimer);
		hideClock('mid');
		actionLabe('hide');
		var cardList = getCardIDListByClassName('selectedCard');
		$.post('http://localhost/backend/src/listen.php',{isPost: 1,roomID: roomID, uid: uid, postQueue: postQueue, cardList:cardList}, function(data){
			data = eval('(' + data + ')');
			if(!data.isPostValidate)
			{
				ShowInfo('出牌错误');
				showClock('mid');
				actionLabe('show');
			}
			else
			{
				if(!isGameOver)
				{
					ShowInfo('出牌成功');
				}
				actionLabe('hide');
				handleResponse(data);
				queryTimer = loopQuery();
			}

		})
	}
	);

	$("#nopost").click(
	function (){

		clearTimeout(queryTimer);
		hideClock('mid');
		actionLabe('hide');

		$.post('http://localhost/backend/src/listen.php',{isPost: 1, roomID: roomID, uid: uid, postQueue: postQueue}, function(data){
			data = eval('(' + data + ')');
			if(!isGameOver)
			{
				ShowInfo('要不起');
			}
			handleResponse(data);
			queryTimer = loopQuery();
		})
	}
);
})

function goSquare()
{
	window.location ='http://localhost/backend/src/lib/square.php';
}



function actionLabe(isShow)
{
	if(isShow == 'show')
	{
		$('#action').show();
	}
	else
	{
		$('#action').hide();
	}
	
}

function getClockPositionByID(id)
{
	if(id == leftPlayerID)
	{
		return 'left';
	}
	else if(id == rightPlayerID)
	{
		return 'right';
	}
	else
		return 'mid';
}

function handleResponse(responseData)
{
	if(!responseData.isGameOver && !isGameOver)
	{
		if(parseInt(responseData.totalPeople) == 3 && !isSetProfile)
		{
			delWaitInfo();
			switch(responseData.lordPosition)
			{
				case 'left' :
					leftPlayerID = responseData.playerLeftID;
					rightPlayerID = responseData.playerRightID;
					var leftProfile = '<img src=\'http://localhost/frontend/img/logo/landlord.png\' /><span>'+responseData.playerLeftID+'</span>';
					$(leftProfile).appendTo("#leftProfile");
					var midProfile = '<img src=\'http://localhost/frontend/img/logo/farmer.png\' /><span>'+uid+'</span>';
					$(midProfile).appendTo("#midProfile");
					var rightProfile = '<img src=\'http://localhost/frontend/img/logo/farmer.png\' /><span>'+responseData.playerRightID+'</span>';
					$(rightProfile).appendTo("#rightProfile");
					break;
				case 'mid' :
					leftPlayerID = responseData.playerLeftID;
					rightPlayerID = responseData.playerRightID;
					var leftProfile = '<img src=\'http://localhost/frontend/img/logo/farmer.png\' /><span>'+responseData.playerLeftID+'</span>';
					$(leftProfile).appendTo("#leftProfile");
					var midProfile = '<img src=\'http://localhost/frontend/img/logo/landlord.png\' /><span>'+uid+'</span>';
					$(midProfile).appendTo("#midProfile");
					var rightProfile = '<img src=\'http://localhost/frontend/img/logo/farmer.png\' /><span>'+responseData.playerRightID+'</span>';
					$(rightProfile).appendTo("#rightProfile");
					break;

				case 'right' :
					leftPlayerID = responseData.playerLeftID;
					rightPlayerID = responseData.playerRightID;
					var leftProfile = '<img src=\'http://localhost/frontend/img/logo/farmer.png\' /><span>'+responseData.playerLeftID+'</span>';
					$(leftProfile).appendTo("#leftProfile");
					var midProfile = '<img src=\'http://localhost/frontend/img/logo/farmer.png\' /><span>'+uid+'</span>';
					$(midProfile).appendTo("#midProfile");
					var rightProfile = '<img src=\'http://localhost/frontend/img/logo/landlord.png\' /><span>'+responseData.playerRightID+'</span>';
					$(rightProfile).appendTo("#rightProfile");
					break;
			}
			isSetProfile = true;
		}

		if(responseData.totalPlayer < 3 && !isSetWaitLabel)
		{
			showWaitInfo();
			isSetWaitLabel = true;
		}
		else if(responseData.totalPlayer == 3 && isSetWaitLabel)
		{
			$('#info').hide();
			isSetWaitLabel = false;
		}
		else if(responseData.postQueueNum == postQueue)
		{
			;
		}
		else if(responseData.postQueueNum != postQueue)
		{	
			renderLRCardList('left', parseInt(responseData.leftPlayerLeft));
			renderPost('left', converStrAToIntA(responseData.leftPostCardList));

			renderPost('mid',  converStrAToIntA(responseData.selfPostList));
			renderMidCardList(responseData.selfCardList);
			
			renderPost('right',  converStrAToIntA(responseData.rightPostCardList));
			renderLRCardList('right', parseInt(responseData.rightPlayerLeft));
			postQueue = responseData.postQueueNum;
			addAnimate();
			
			if(responseData.validPostID == uid)
			{
				actionLabe('show');
				hideClock(clickNowPosition);
				clickNowPosition = 'mid';
				showClock('mid');
			}
			else if(responseData.validPostID == leftPlayerID)
			{
				actionLabe('hide');
				hideClock(clickNowPosition);
				clickNowPosition = 'left'
				showClock('left');
			}
			else if(responseData.validPostID == rightPlayerID)
			{
				actionLabe('hide');
				hideClock(clickNowPosition);
				clickNowPosition = 'right'
				showClock('right');
			}
		}
	}
	else if(responseData.isGameOver && !isSetOverInfo)
	{
			winnerID = responseData.winnerID;
			var str = winnerID + '赢，回到大厅';
			$("#post").click();
			$("#nopost").click();
			ShowInfo(str);
			setTimeout(goSquare, 4000);
			clearTimeout(queryTimer);
			isSetOverInfo = true;
			isGameOver = true;
	}
}


function addAnimate()
{
	$('#midCardList li').toggle(
	function()
	{
		$(this).animate({'marginTop': '-20px'}, "fast");
		$(this).addClass('selectedCard');
	},
	function()
	{
		$(this).animate({'marginTop': '0px'}, "fast");
		$(this).removeClass('selectedCard');
	}
	);
}

function getCardIDListByClassName(className)
{
	var cardList = new Array();
	var cardEle =  $("."+className);
	var cardLen =cardEle.length;

	for(var idx = 0; idx < cardLen; idx++)
	{
		cardList.push(cardEle[idx].id);
	}
	return cardList;
}

function converStrAToIntA(cardList)
{
	if(cardList == undefined || cardList[0] == '')
	{
		return new Array();
	}
	else
	{
		var len = cardList.length;
		var res = new Array();
		for(var idx = 0; idx < len; idx++)
		{
			res.push(parseInt(cardList[idx]));
		}
		return res;
	}
}

function loopQuery()
{
	$.post('http://localhost/backend/src/listen.php', {isPost:0, roomID: roomID, uid: uid, postQueue: postQueue}, 
		function(data){
			data = eval('('+data+')');
			handleResponse(data);
			queryTimer = setTimeout(loopQuery, 1000);
	});
}

function ShowInfo(info)
{
	elementStr =   "<div id='info'><span id='infoContent'>"+
					info+
					"</span>"+
					"</div>";
	$(elementStr).appendTo('body');
	$('#info').fadeOut(4000);
	
	setTimeout(function(){
		$('#info').remove();
	}, 4000);
}

function showWaitInfo()
{
	elementStr =   "<div id='info'><span id='infoContent'>"+
					'等待加入中。。。'+
					"</span>"+
					"</div>";
	$(elementStr).appendTo('body');
}

function showGameOver(str)
{

	elementStr =   "<div id='info'><span id='infoContent'>"+
					str +
					"</span>"+
					"</div>";
	$(elementStr).appendTo('body');
}
function sortNumber(a,b)
{
	return a - b
}

function delWaitInfo()
{
	$('#info').remove();
}

function renderLRCardList(position, num)
{
	var selector = "#"+position+"CardList";
	var content = '';

	for(var idx = 0; idx < num; idx++)
	{
		content += '<li><img src=\'http://localhost/frontend/img/logo/cover.png\'></li>';
	}
	$(selector).empty();
	$(selector).append(content);
	padding = (20 - num) * 10;
	$(selector).css({paddingTop: padding + 'px'});
}

function renderMidCardList(cardList)
{
	if(cardList == undefined)
	{
		$('#midCardList').empty();
	}
	else
	{
		var Newlen = cardList.length;
		var exist = $('#midCardList');

		content = '';
		for(var idx = 0; idx < Newlen; idx++)
		{
			content += '<li id=\''+ cardList[idx]+'\'><img src=\'http://localhost/frontend/img/card/'+cardList[idx]+'.png\'></li>';
		}
		$('#midCardList').empty();
		$('#midCardList').append(content);

		padding = (20 - Newlen) * 10;
		$('#midCardList').css({paddingLeft: padding + 'px'});
	}
}

function renderPost(position, cardList)
{
	positionCardList = '#'+ position + 'PostLogo';
	positionClock = '#'+position + 'Timer';
	if(!cardList)
	{
		content = '<ul></ul>';
	}
	else
	{
		$(positionClock).hide();
		len = cardList.length;
		cardList = cardList.sort(sortNumber);

		content = '<ul>';
		for (var idx = 0; idx < len; idx++ ) {
			content += '<li><img src=\'http://localhost/frontend/img/card/'+cardList[idx]+'.png\'></li>';
		}
		content += '</ul>';
	}
	$(positionCardList).empty();
	$(positionCardList).append(content);
}

function showClock(position)
{
	var numSelector = "#" + position + "Timer";
	var postSelector = "#" + position + "PostLogo";

	$(numSelector).show();
	// $(postSelector).empty();
}

function hideClock(position)
{
	var numSelector = "#" + position + "Timer";
	$(numSelector).hide();
}