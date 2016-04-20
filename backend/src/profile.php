<?php 
	require_once '../../backend/src/class/Database.php';

	$dbObj = new Database();
	$dbHandle = $dbObj ->　getHandle();
	$id = $_GET['id'];

	$sql =<<<SQL
		select * from record where id= '$id';
SQL;
	if($queryResult = $dbHandle -> query($sql))
	{
		$recordList = array();
		while($result = $queryResult -> fetch_assoc())
		{
			array_push($record, array(
						$result['playerWinerID'],
						$result['playerOneID'],
						$result['playerTwoID'],
						$result['playerThreeID'], 
						$result['gameTime'],
						$result['playerLordID']
						));
		}	
	}
 ?>
 <html>
 <head>
 	<title><?php eho $id.'的游戏历史'; ?></title>
 	<link rel="stylesheet" type="text/css" href="http://localhost/frontend/style/profile.css" />
 </head>
 <body>
 	<div class="header">
	    <img src="../../../frontend/img/logo/banner_logo.png" id="banner_logo" >
	    <span id="banner_title"><?php eho $id.'的游戏历史'; ?></span>
	    <div class="profile" id="<?php echo $userInfo['userName']; ?>">
	        <img src="../../upload/default.png" id="avatar" />
	        <a id="name" href="userProfile.php?id=<?php echo $userInfo['userName']; ?>"><?php echo $userInfo['userName']; ?></a>
	        <a id="singout" href="http://localhost/">退出</a>
	    </div>
	</div>
	<div id="body">
		<?php
			if
		?>
	</div>
 </body>
 </html>