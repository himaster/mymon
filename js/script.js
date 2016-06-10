function replica_restart(serverip) {
    $('#status_div').html('processing...');
    document.getElementById('status_div').style.display = 'block';
    $.ajax({
        url: 'index.php?task=replica_restart&serverip=' + serverip,
        success: function(html){
            status(html);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            status("Error!");
            console.log("error: " + thrownError);
        }
    });
}

function replica_repair(serverip) {
    $('#status_div').html('processing...');
    document.getElementById('status_div').style.display = 'block';
    $.ajax({
        url: 'index.php?task=replica_repair&serverip=' + serverip,
        success: function(html){
            status(html);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            status("Error!");
            console.log("error: " + thrownError);
        }
    });
}

function buttonshowhide(button, popup) {
    var popstate = document.getElementById(popup).style;
    if (detectmob()) window.animation = [{'top' : '5px'}, {'top' : '-3px'}];
    else window.animation = [{'left' : '5px'}, {'left' : '-3px'}];
    if (popstate.display !== "block") {
        $('#' + button).animate(window.animation[0], {duration : 200, easing: 'swing'});
    } else {
        $('#' + button).animate(window.animation[1], {duration: 200, easing: 'swing'});
    }
}
function toggle_visibility(id) {
    try{
        var e = document.getElementById(id);
        if(e.style.display == 'block')
            e.style.display = 'none';
        else
            e.style.display = 'block';
    } catch(err) {console.log(err + "id=" + id)}
}

function toggle_visibility_menu(id) {
    try{
        var e = document.getElementById(id);
        var i = document.getElementById('col1');
        if(e.style.display == 'block') {
            e.style.display = 'none';
            if (i.style.display == 'block')
                document.getElementById('profile_edit').click();
        } else
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

function show_all() {
    $("div#loader").addClass("wheel");
    $("div#loader").show();
    window.loading = true;
    $.ajax({
        url: 'index.php?task=getdata',
        dataType: 'json',
        timeout: 5000,
        success: function(json) {
            json.data.forEach(function(item) {
                var nowTime = ~~(new Date().getTime() / 1000);
                if (Math.abs(nowTime - item['timestamp']) > 20) {
                    $("#" + item['servername'] + "_name").addClass('forceTimeout');
                }
                else {
                    $("#" + item['servername'] + "_name").removeClass('timeout');
                    $("#" + item['servername'] + "_name").removeClass('forceTimeout');
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
                $("#message_title").html('<h4><b>Message from ' + json.msg.login + '</b></h4>');
                if (document.getElementById("messagebox").style.display !== "block") {
                    notify("New message from " + json.msg.login + "\n" + json.msg.message, 10000);
                }
                document.getElementById("messagebox").style.display = "block";
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            status("Error!");
            console.log("error: " + thrownError);
        },
        complete: function() {
            setTimeout(function() {
                $("div#loader").hide();
                $("div#loader").removeClass("wheel");
                window.loading = false;
            }, 500);
            $("#load_fade").hide();
        }
    });
}

function status(text) {
    $('#status_div').html(text);
    document.getElementById('status_div').style.display = 'block';
    setTimeout("document.getElementById('status_div').style.display = 'none'", 5000);
}

function ban_ip(ip, comment) {
    var dataString = '&task=ban_ip&ip_addr=' + ip + '&comment=' + comment;
    $.ajax({
        url: 'index.php',
        data: dataString,
        cache: false,
        success: function(html) {
            status(html);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            status("Error!");
            console.log("error: " + thrownError);
        }
    });
}

function unban_ip(ip) {
    var dataString = '&task=unban_ip&ip_addr=' + ip;
    $.ajax({
        url: 'index.php',
        data: dataString,
        cache: false,
        success: function(html) {
            status(html);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            status("Error!");
            console.log("error: " + thrownError);
        }
    });
}

function editor(name, val) {
    var servername = name.split('^')[0];
    var columnname = name.split('^')[1];
    if (val === true) var columnval = '1';
    else if (val === false) var columnval = '0';
    else var columnval = escape(val);
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
        document.getElementById(servername + "^color").id = columnval + "^color";
    }
    var dataString = '&task=editor_save&servername=' + servername +'&columnname=' + columnname + '&val=' + columnval;
    $.ajax({
        url: 'index.php',
        data: dataString,
        cache: false,
        success: function(html) {
            status(html);
            console.log("Ajax: " + html);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            status("Error!");
            console.log("error: " + thrownError);
        }
    });
}

function users_editor(name, val) {
    var username = name.split('^')[0];
    var columnname = name.split('^')[1];
    if (val === true) var columnval = '1';
    else if (val === false) var columnval = '0';
    else var columnval = escape(val);
    if (columnname == 'login') {
        document.getElementById(username + "^uid").id = columnval + "^uid";
        document.getElementById(username + "^login").id = columnval + "^login";
        document.getElementById(username + "^email").id = columnval + "^email";
        document.getElementById(username + "^role").id = columnval + "^role";
        document.getElementById(username + "^approvied").id = columnval + "^approvied";
    }
    var dataString = '&task=users_editor_save&username=' + username +'&columnname=' + columnname + '&val=' + columnval;
    $.ajax({
        url: 'index.php',
        data: dataString,
        cache: false,
        success: function(html) {
            status(html);
        },
        error: function(html) {
            status("Error!");
            console.log("Error:" + html);
        }
    });
}

function user_remove(id) {
    var dataString = '&task=user_remove&user_id=' + id;
    $.ajax({
        url: 'index.php',
        data: dataString,
        cache: false,
        success: function(html) {
            status(html);
        },
        error: function(html) {
            status("Error!");
            console.log("Error:" + html);
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
        error: function(xhr, ajaxOptions, thrownError) {
            status("Error!");
            console.log("error: " + thrownError);
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
        error: function(xhr, ajaxOptions, thrownError) {
            status("Error!");
            console.log("error: " + thrownError);
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
        console.log("This browser does not support desktop notification");
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
    if ( navigator.userAgent.match(/Android/i)
    || navigator.userAgent.match(/webOS/i)
    || navigator.userAgent.match(/iPhone/i)
    || navigator.userAgent.match(/iPad/i)
    || navigator.userAgent.match(/iPod/i)
    || navigator.userAgent.match(/BlackBerry/i)
    || navigator.userAgent.match(/Windows Phone/i)
    ) {
        return true;
    } else {
        return false;
    }
}

function reverst() {
    var el = document.body;
    var status = document.getElementById("main_table");
    var newFontSize = Math.round((window.innerWidth-150)/32);
    if (detectmob()) $("#main_table").removeAttr("style");
    else if (newFontSize < 15) el.style.fontSize = newFontSize + 'px';
    else el.style.fontSize = '15px';
    $(".ban_comment").not(".hidden").offset({left: $("#users_table").offset().left + $("#users_table").width()});
    try {
        document.getElementById('status_div').style.width = document.getElementById('main_table').clientWidth + "px";
    }
    catch (err) {}
}

function is_touch_device() {
  return 'ontouchstart' in window || navigator.maxTouchPoints;
};

function implode( glue, pieces ) {
    return ( ( pieces instanceof Array ) ? pieces.join ( glue ) : pieces );
}

function delete_cookie(name) {
  document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

$(window).resize(function() {
    reverst();
});

$(document).ready(function() {
    $("#users_table select").multiselect();
    if (document.getElementById("notify") !== null ) {
        if (document.getElementById("notify").checked === true) window.create_new_mes = 1;
        else window.create_new_mes = 0;
    }
    if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i)) {
        var viewportmeta = document.querySelector('meta[name="viewport"]');
        if (viewportmeta) {
            viewportmeta.content = 'width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0';
            document.body.addEventListener('gesturestart', function () {
                viewportmeta.content = 'width=device-width, minimum-scale=0.25, maximum-scale=1.6';
            }, false);
        }
    }
    show_all();
    setInterval(function() { if(!window.swiped) { show_all()}}, 10000);
    reverst();
    $("#my_div").click(function() { on_top("my_div"); });
    $("#message_div").click(function() { on_top("message_div"); });
    $("#left_button").click(function() {
        buttonshowhide(this.id, "my_div");
        toggle_visibility_menu('my_div');
        if ($('#message_div').is(':visible') && $('#my_div').is(':visible')) {
            $('#left_button2').trigger('click');;
        }
    });
    $("#left_button2").click(function() {
        buttonshowhide(this.id, "message_div");
        toggle_visibility_msg('message_div');
        if ($('#message_div').is(':visible') && $('#my_div').is(':visible')) {
            $('#left_button').trigger('click');;
        }
    });
    $("#message_submit").click(function() {
        var popstate = document.getElementById("message_div").style;
        if (detectmob()) window.animation = [{'top' : '5px'}, {'top' : '-3px'}];
        else window.animation = [{'left' : '5px'}, {'left' : '-3px'}];
        $("#left_button2").animate(window.animation[1], {duration: 200, easing: 'swing'});
    });
    $("#refresher").click(function() {
        show_all();
    });
});

$(window).bind('orientationchange', function(e) {
    window.location.reload();
});

document.body.addEventListener('touchstart', function(e) {
    if (!window.loading) {
        window.swiped = true;
        startY = e.touches[0].screenY;
        $('div#loader img').css('transform','rotate(0deg)');
        $("div#loader").show();
    }
});
document.body.addEventListener('touchmove', function(e) {
    if (!window.loading) {
        swipeY = startY - e.changedTouches[0].screenY;
        $('div#loader img').css('transform','rotate(' + (360 - (swipeY * 5)) + 'deg)');
    }
});
document.body.addEventListener('touchend', function(e) {
    if (!window.loading) {
        $("div#loader").hide();
        if (swipeY<=-70) {
            show_all();
        }
        window.swiped = false;
    }
});
