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
    } catch(err) {}
}

function show() {
    $.ajax({
        url: 'index.php?task=getdata',
        dataType: 'json',
        success: function(json) {
            json.forEach(function(item, i, arr) {
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
                if (!item['message']) { 
                    document.getElementById('messagebox').style.display = 'none';
                } else {
                    $("#message").html(item['message'] + "<a class=\"mbclose2\" href=\"javascript: mbclose();\">Close</a>");
                    document.getElementById('messagebox').style.display = 'block';
                }

            });
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
}

$(document).ready(function() {
    show();
    setInterval('show()', 5000);
});
