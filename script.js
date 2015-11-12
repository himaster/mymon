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
    var e = document.getElementById(id);
    if(e.style.display == 'block')
        e.style.display = 'none';
    else
        e.style.display = 'block';
}

function show() {
    $.ajax({
        url: 'index.php?task=getdata',
        dataType: 'json',
        success: function(json) {
            json.forEach(function(item, i, arr) {
                $("#" + item.servername + "_la").html(item['la']);
                $("#" + item.servername + "_rep").html(item['rep']);
                $("#" + item.servername + "_500").html(item['500']);
                $("#" + item.servername + "_elastic").html(item['elastic']);
                $("#" + item.servername + "_locks").html(item['locks']);
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
    var servername = name.split('_')[0];
    var columnname = name.split('_')[1];
    if (columnname == 'servername') {
        document.getElementById(servername + "_servername").id = val + "_servername";
        document.getElementById(servername + "_ip").id = val + "_ip";
        document.getElementById(servername + "_db").id = val + "_db";
        document.getElementById(servername + "_mysql").id = val + "_mysql";
        document.getElementById(servername + "_err").id = val + "_err";
        document.getElementById(servername + "_el").id = val + "_el";
    }
    var dataString = '&task=editor_save&servername=' + servername +'&columnname=' + columnname + '&val=' + val;
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

$(document).ready(function() {
    show();
    setInterval('show()', 5000);
});
