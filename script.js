function myAjax(serverip) {
    $('#test_div').html('processing...');
    document.getElementById('test_div').style.display = 'block';
    $.ajax({
        url: 'index.php?task=replica&serverip=' + serverip,
        success: function(html){
            status(html);
        },
        error: function(){
            status("Error");
        }
    });
}

function toggle_visibility(id) {
    try{
        var e = document.getElementById(id);
        if(e.style.display == 'block')
            e.style.display = 'none';
        else
            e.style.display = 'block';
    } catch(err) {console.log(err)}
}

function toggle_visibility_msg(id) {
    try{
        var e = document.getElementById(id);
        if(e.style.display == 'block')
            e.style.display = 'none';
        else
            e.style.display = 'block';
            document.getElementById('umessage').value = '';
    } catch(err) {console.log(err)}
}

function show() {
    $.ajax({
        url: 'index.php?task=getdata',
        dataType: 'json',
        success: function(json) {
            json.data.forEach(function(item) {
                var nowTime = ~~(new Date().getTime() / 1000);
                if (Math.abs(nowTime - item['timestamp']) > 20) {
                    $("#" + item['servername'] + "_name").addClass('forceTimeout');
                    //var t = setTimeout("conn_prob = notify('Connection problems!')", 20000);
                }
                else {
                    $("#" + item['servername'] + "_name").removeClass('timeout');
                    $("#" + item['servername'] + "_name").removeClass('forceTimeout');
                    //clearTimeout(t);
                    setTimeout(function(){ $("#" + item['servername'] + "_name").addClass('timeout') }, 100);
                }
                $("#" + item['servername'] + "_name").html(item['servername']);
                $("#" + item['servername'] + "_la").html(item['la']);
                $("#" + item['servername'] + "_rep").html(item['rep']);
                $("#" + item['servername'] + "_locks").html(item['locks']);
                $("#" + item['servername'] + "_500").html(item['500']);
                $("#" + item['servername'] + "_elastic").html(item['elastic']);
                $("#" + item['servername'] + "_mongo").html(item['mongo']);
                $("#" + item['servername'] + "_redis").html(item['redis']);
            });
            if (typeof json.msg === 'undefined') {
                document.getElementById("messagebox").style.display = "none";
            } else {
                $("#message").html(json.msg.message);
                $("#message_title").html('Message from ' + json.msg.login);
                if (document.getElementById("messagebox").style.display !== "block") {
                    notify("New message from " + json.msg.login + "\n" + json.msg.message, 10000, true);
                }
                document.getElementById("messagebox").style.display = "block";
            }
        },
        error: function() {
            console.log("error");
        }
    });
}


function status(text) {
    $('#test_div').html(text);
    document.getElementById('test_div').style.display = 'block';
    setTimeout("document.getElementById('test_div').style.display = 'none'", 5000);
}

function editor(name, val) {
    var servername = name.split('^')[0];
    var columnname = name.split('^')[1];
    if (val == true) var columnval = '1';
    else if (val == false) var columnval = '0';
    else var columnval = val;
    console.log(columnval);
    if (columnname == 'servername') {
        document.getElementById(servername + "^servername").id = columnval + "^servername";
        document.getElementById(servername + "^ip").id = columnval + "^ip";
        document.getElementById(servername + "^role").id = columnval + "^role";
        document.getElementById(servername + "^db").id = columnval + "^db";
        document.getElementById(servername + "^mysql").id = columnval + "^mysql";
        document.getElementById(servername + "^err").id = columnval + "^err";
        document.getElementById(servername + "^el").id = columnval + "^el";
        document.getElementById(servername + "^mon").id = columnval + "^mon";
        document.getElementById(servername + "^red").id = columnval + "^red";
    }
    var dataString = '&task=editor_save&servername=' + servername +'&columnname=' + columnname + '&val=' + columnval;
    $.ajax({
        url: 'index.php',
        data: dataString,
        cache: false,
        success: function(html) {
            console.log("Ajax: " + html);
        },
        error: function() {
            console.log("error");
        }
    });

}

function mbclose() {
    document.getElementById('messagebox').style.display = 'none';
    $.ajax({
        url: 'index.php?task=msgred',
        cache: false,
        success: function(html) {
            new_mes.close();
            console.log(html);
        },
        error: function() {
            console.log("error");
        }
    });
}

function msg_submit(){
    var Serialized =  $("#message_form").serialize();
    var a=document.forms["message_form"]["umessage"].value;
    
    if (a==null || a=="") {
      alert("Please Fill All Required Field");
      return false;
    }
    $.ajax({
       type: "POST",
        url: "index.php?task=sendmsg",
        data: Serialized,
        success: function(data) {
            status(data);
            document.getElementById('message_div').style.display = 'none';
        },
   error: function(){
        alert('error handling here');
      }
    });
}

function on_top(id) {
    var e = document.getElementById(id);
    $(".ontop").removeClass("ontop");
    $( "#" + id ).addClass("ontop");
}

function notify(message, time, override) {
    time = time || 3000;
    override = override || false;
    if (!("Notification" in window)) {
        //alert("This browser does not support desktop notification");
    }
    else if (Notification.permission === "granted" && (window.create_new_mes === 1 || override)) {
        window.create_new_mes=0;
        setTimeout("create_new_mes=1;", 30000);
        new_mes = new Notification(message);
        setTimeout(new_mes.close.bind(new_mes), time);
        return new_mes;
    }
    else if (Notification.permission !== 'denied' && (window.create_new_mes === 1 || override)) {
        Notification.requestPermission(function (permission) {
            if (permission === "granted") {
                window.create_new_mes=0;
                setTimeout("create_new_mes=1;", 30000);
                new_mes = new Notification(message);
                setTimeout(new_mes.close.bind(new_mes), time);
                return new_mes;
            }
        });
    }
}

function detectmob() { 
    if (( navigator.userAgent.match(/Android/i)
    || navigator.userAgent.match(/webOS/i)
    || navigator.userAgent.match(/iPhone/i)
    || navigator.userAgent.match(/iPad/i)
    || navigator.userAgent.match(/iPod/i)
    || navigator.userAgent.match(/BlackBerry/i)
    || navigator.userAgent.match(/Windows Phone/i)
    ) && (window.innerHeight > window.innerWidth)) {
        return true;
    } else {
        return false;
    }
}

function reverst() {
    var el = document.getElementById('main_table');
    var newFontSize = Math.round((window.innerWidth-200)/32);
    if (detectmob()) $("#main_table").removeAttr("style");
    else if (newFontSize < 15) el.style.fontSize = newFontSize + 'px';
    else el.style.fontSize = '15px';
    console.log(el.style.fontSize);
}

$(window).resize(function() {
    reverst();
});

$(document).ready(function() {
    console.log("Page ready.");
    if ((window.outerHeight - window.innerHeight) > 100) setTimeout("console.log(\"Looking in console? Are You developer may be? ;)\")", 5000);
    window.create_new_mes=1;

    if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i)) {
        var viewportmeta = document.querySelector('meta[name="viewport"]');
        if (viewportmeta) {
            viewportmeta.content = 'width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0';
            document.body.addEventListener('gesturestart', function () {
                viewportmeta.content = 'width=device-width, minimum-scale=0.25, maximum-scale=1.6';
            }, false);
        }
    }
    show();
    setInterval('show()', 5000);
    reverst();
    $("#my_div").click(function() { on_top("my_div"); });
    $("#message_div").click(function() { on_top("message_div"); });
    $('#left_button').click(function() {
        var popstate = document.getElementById("my_div").style;
        if (detectmob()) window.animation = [{'top' : '5px'}, {'top' : '-3px'}];
        else window.animation = [{'left' : '5px'}, {'left' : '-3px'}];
        if (popstate.display !== "block") {
            console.log(window.animation[0]);
            $(this).animate(window.animation[0], {duration : 200, easing: 'swing'});
            expanded = true;
        } else {
            console.log(window.animation[1]);
            $(this).animate(window.animation[1], {duration: 200, easing: 'swing'});
            setTimeout($(this).removeAttr("style"),200);
            expanded = false;
        }
    });
});


$(window).bind('orientationchange', function(e) {
    window.location.reload();
});

