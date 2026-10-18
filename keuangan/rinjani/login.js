$(document).ready(function ()
{
    $(document).bgStretcher({
        images: ['../../images/background15.jpg'], imageWidth: 1680, imageHeight: 1050
    });

    $("#login").focus();
});

function onResize()
{
    let WinHeight = 0;
    let WinWidth = 0;

    if( typeof( window.innerWidth ) === 'number' )
    {
        WinHeight = window.innerHeight;
        WinWidth = window.innerWidth;
    } else if( document.documentElement &&
        ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
        WinHeight = document.documentElement.clientHeight;
        WinWidth = document.documentElement.clientWidth;
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        WinHeight = document.body.clientHeight;
        WinWidth = document.body.clientWidth;
    }

    let left = (parseInt(WinWidth) / 2 - 200) + "px";
    let top = (parseInt(WinHeight) / 2 - 80) + "px";
    $("#Main").css({"left" : left, "top" : top});
}

function processLogin()
{
    let login = $.trim($("#login").val());
    let password = $.trim($("#password").val());

    if (login.length === 0 || password.length === 0)
        return;

    let qsb = new QsBuilder();
    qsb.add("op", "login");
    qsb.add("login", login);
    qsb.add("password", password);

    $.ajax({
        url: "login.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                showToast(ls[1], 3000, "error", "bottom");

                alert(ls[1]);
                return;
            }

            document.location.href = "main.php";
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}