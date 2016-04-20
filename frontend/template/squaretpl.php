<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>斗地主大厅</title>
    <script type="application/javascript" src="../../../frontend/js/jquery-1.7.1.min.js"></script>
    <script type="application/javascript" src="../../../frontend/js/square.js"></script>
    <link rel="stylesheet" type="text/css" href="../../../frontend/style/square.css" />
</head>
<body>
<div class="header">
    <img src="../../../frontend/img/logo/banner_logo.png" id="banner_logo" >
    <span id="banner_title">斗地主大厅</span>
    <div class="profile" id="<?php echo $userInfo['userName']; ?>">
        <img src="../../upload/default.png" id="avatar" />
        <a id="name" href="userProfile.php?id=<?php echo $userInfo['userName']; ?>"><?php echo $userInfo['userName']; ?></a>
        <a id="singout" href="http://localhost/">退出</a>
    </div>
</div>
<div id="main">
    <div id="square">
            <div id="addroom">
                    <span id="addroomlogo">+</span>
            </div>
        <div class="rooms">
            <ul class="roomlist">
               <?php
                    if (count($roomIDList) === 0)
                    {
                            echo "<span id=\"noroom\" >请点击右上方按钮添加房间</span>";
                    }
                       else
                       {
                               foreach($roomIDList as $roomID)
                               {
                                       $liStr =<<<EOF
                                    <li id="li$roomID">
                                        <span class='del' id='dl$roomID'>&times;</span>
                                        <a class='room' id="$roomID" href="../waiting.php?id=$roomID"><span>房间$roomID</span></a>
                                    </li>
EOF;
                                       echo $liStr;
                               }
                       }
                ?>
            </ul>
        </div>
    </div>
</div>
</body>
</html>