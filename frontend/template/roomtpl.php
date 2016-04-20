<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>斗地主房间</title>
	 <script type="application/javascript" src="../../../frontend/js/jquery-1.7.1.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="../../../frontend/style/room.css" />
</head>
<body id="<?php echo $roomID; ?>" >
<input type="hidden" id="uid" value="<?php echo $userInfo['userName']; ?>" />
<input type="hidden" id="roomid" value="<?php echo $roomID; ?>" />
<div class="header">
	<img src="../../../frontend/img/logo/banner_logo.png" id="banner_logo" >
	<span id="banner_title">房间 <?php echo "&nbsp;&nbsp;".$roomID; ?></span>
	<div class="profile" id="<?php echo $userInfo['userName']; ?>">
		<img src="http://localhost/backend/upload/default.png" id="avatar" />
		<a id="name" href="userProfile.php?id=<?php echo $userInfo['userName']; ?>"><?php echo $userInfo['userName']; ?></a>
		 <a id="singout" href="http://localhost/">退出</a>
	</div>
</div>
<div id="main">
	<div id="room">
		<div id="desk">
		<div id="leftSide">
			<div id="leftPlayer">
				<div id="leftProfile" class ='idcard'></div>
				<div id="leftCard">
					<ul id="leftCardList">
						
					</ul>
				</div>
				<div id="leftTimer" class="timer"></div>
				<div id="leftPostLogo" class="postLogo"></div>
			</div>
		</div>	
		<div id="midSide">
			<div id="midPlayer">
				<div id="midProfile" class ='idcard'></div>
				<div id="midCard">
				<div id="action">
					<div id="post"></div>
					<div id="nopost"></div>
				</div>
					<ul id="midCardList">
						
					</ul>
				</div>
				<div id="midTimer" class="timer"></div>
				<div id="midPostLogo" class="postLogo"></div>
			</div>
		</div>
		<div id="rightSide">
			<div id="rightPlayer">
				<div id="rightProfile"  class ='idcard'></div>
				<div id="rightCard">
					<ul id="rightCardList">
						
					</ul>
				</div>
				<div id="rightTimer" class="timer"></div>
				<div id="rightPostLogo" class="postLogo"></div>
			</div>			
		</div>
	</div>
	</div>
</div>
 <script type="application/javascript" src="../../../frontend/js/room.js"></script>
 </body>

 </html>