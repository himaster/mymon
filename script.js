function myAjax(serverip) {
    $('#test_div').html('processing...');
    document.getElementById('test_div').style.display = 'block';
    $.ajax({
		url: 'index.php?task=replica&serverip=' + serverip,
		success: function(html){
			$('#test_div').html('success' + html);
            document.getElementById('test_div').style.display = 'block';
			setTimeout("document.getElementById('test_div').style.display = 'none'", 5000);
		},
		error: function(){
			$('#test_div').html('error');
            document.getElementById('test_div').style.display = 'block';
			setTimeout("document.getElementById('test_div').style.display = 'none'", 5000);
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
            window.setTimeout(function() { $("#" + server + "_" + task).addClass('timeout'); }, 100);
            $("#" + server + "_" + task).html(html);
        },
        error: function(){
            $("#" + server + "_" + task).addClass('forceTimeout');
        }
    });
}
