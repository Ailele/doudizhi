$(function(){
    function queryRoom()
    {
        $.get('http://localhost/backend/src/lib/queryRoom.php','',function ($data) {
            var serverRoomList = eval('('+$data+')');
            var localRoomList = getRoomList();
            handleRoom(serverRoomList, localRoomList);
            if ($(".del").length > 0)
            {
                $("#noroom").remove();
            }
            else if($("#noroom").length === 0)
            {
                $('.roomlist').append('<span id=\"noroom\" >请点击右上方按钮添加房间</span>');
            }
        });
    }

    function handleRoom(serverRoomList, localRoomList)
    {
        var serverLen = serverRoomList.length;
        var localLen = localRoomList.length;

        if(serverLen === 1 && serverRoomList[0] === '')
        {
            serverRoomList = [];
            serverLen = 0;
        }

        var flag;
        for (var i = 0; i < serverLen; i++)
        {
            flag = 0;
            for (var j = 0; j < localLen; j++)
            {
                if (serverRoomList[i] == localRoomList[j])
                {
                    flag = 1;
                }
            }
            if (flag !== 1)
            {
                $('ul:last').append("<li id=\'li"+serverRoomList[i]+"\'> <span class=\'del\' id=\'dl"+serverRoomList[i]+"\'>&times;</span><a class=\'room\' id=\'" + serverRoomList[i] + "\' href=\'http://localhost/backend/src/waiting.php?id=" + serverRoomList[i] + "\'><span>房间" + serverRoomList[i] + "</span></a></li>");
                $('#'+"dl"+serverRoomList[i]).click(
                    function () {
                        var id = $(this).attr('id').substr(2);
                        $.post("http://localhost/backend/src/lib/deleteRoom.php", {"id": id}, function($data)
                        {
                            $data = eval('('+$data+')');
                            if ($data === true)
                            {
                                $('#li' + id).remove();
                            }
                            else
                            {
                                alert('delete failed');
                            }
                        })
                    }
                );
            }
        }

        localRoomList = getRoomList();
        localLen = localRoomList.length;
        for (j = 0; j < localLen; j++)
        {
            flag = 0;
            for ( i = 0; i < serverLen; i++)
            {
                if (serverRoomList[i] == localRoomList[j]) {
                    flag = 1;
                }
            }
            if (flag !== 1)
            {
                $('#li'+j).remove();
            }
        }
        if ($(".del").length > 0)
        {
            $("#noroom").remove();
        }
        else if($("#noroom").length === 0)
        {
            $('.roomlist').append('<span id=\"noroom\" >请点击右上方按钮添加房间</span>');
        }
    }
    function getRoomList()
    {
        var len = $('.room').length;
        var localRoomList = [];

        for(var k = 0; k < len; k++)
        {
            localRoomList.push($($('.room')[k]).attr('id'));
        }

        return localRoomList;
    }


    setInterval(queryRoom,2000);

    $('.del').click(
        function () {
            var id = $(this).attr('id').substr(2);
            $.post("http://localhost/backend/src/lib/deleteRoom.php", {"id": id}, function($data)
            {
                $data = eval('('+$data+')');
                if ($data === true)
                {
                    $('#li' + id).remove();
                }
                else
                {
                    alert('delete failed');
                }
            })

            if ($(".del").length > 0)
            {
                $("#noroom").remove();
            }
            else if($("#noroom").length === 0)
            {
                $('.roomlist').append('<span id=\"noroom\" >请点击右上方按钮添加房间</span>');
            }
        }
    );

    $("#addroomlogo").click(
        function()
        {
            $('#addroomlogo').attr('display', 'none');
            $.get("http://localhost/backend/src/lib/addRoom.php", "", function(data)
            {
                var data = eval('('+data+')');
                if (typeof data  === 'number')
                {
                    $('.roomlist').append("<li id=\'li"+data+"\'> <span class=\'del\' id=\'dl"+data+"\'>&times;</span><a class=\'room\' id=\'" + data + "\' href=\'http://localhost/backend/src/waiting.php?id=" + data + "\'><span>房间" + data + "</span></a></li>");
                    $('#dl'+data).click(
                        function () {
                            var id = data;
                            $.post("http://localhost/backend/src/lib/deleteRoom.php", {"id": id}, function($data)
                            {
                                $data = eval('('+$data+')');
                                if ($data === true)
                                {
                                    $('#li' + id).remove();
                                }
                                else
                                {
                                    alert('delete failed');
                                }
                            })
                        }
                    );
                }
                else
                {
                    alert('添加房间失败');
                }
                $('#addroomlogo').attr('display', 'block');
            })

            if ($(".del").length > 0)
            {
                $("#noroom").remove();
            }
            else if($("#noroom").length === 0)
            {
                $('.roomlist').append('<span id=\"noroom\" >请点击右上方按钮添加房间</span>');
            }
        }
    );
})