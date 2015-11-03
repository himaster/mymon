function myAjax(serverip) {
    $('#test_div').html('processing...');
    document.getElementById('test_div').style.display = 'block';
    $.ajax({
		url: 'index.php?task=replica&serverip=' + serverip,
		success: function(html){
			status(html);
		},
		error: function(){
			status('error');
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


function show(serverip, server, task) {
    var data = "&serverip=" + serverip + 
                  "&task=" + task;
    $.ajax({
        url: "index.php",
        data: data,
        cache: false,
        success: function(html){
            $("#" + server + "_" + task).removeClass('timeout');
            $("#" + server + "_" + task).removeClass('forceTimeout');
            timeout = setTimeout(function() { $("#" + server + "_" + task).addClass('timeout'); }, 100);
            $("#" + server + "_" + task).html(html);
        },
        error: function(){
            console.log(serverip + " - reconnecting...");
            $("#" + server + "_" + task).addClass('forceTimeout');
        }
    });
}

function status(text) {
    $('#test_div').html(text);
    document.getElementById('test_div').style.display = 'block';
    setTimeout("document.getElementById('test_div').style.display = 'none'", 5000);
}