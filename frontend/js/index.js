$(function(){
    $("#loginBtn").click(
        function() {
            $("#login").css('display','block');
            $(this).addClass('focus');
            $("#register").css('display','none');
            $("#registerBtn").removeClass('focus');
        }
    )

    $("#registerBtn").click(
        function() {
            $("#register").css('display','block');
            $(this).addClass('focus');;
            $("#login").css('display','none');
            $("#loginBtn").removeClass('focus');
        }
    )

    if (location.hash === '#userexists')
    {
        alert('用户名已存在，请换其他用户名注册');
        location.hash = '';
    }
    else if(location.hash === '#loginerror')
    {
        alert('登陆失败，请检查用户名和密码');
        location.hash = '';
    }
    else if(location.hash === 'cookieexpire')
    {
        alert('账号已过期，请重新登陆');
        location.hash = '';
    }
})