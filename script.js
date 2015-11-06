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
};

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
                $("#" + item.servername + "_lock").html(item['lock']);
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

$(document).ready(function() {
    show();
    setInterval('show()',10000);
});
