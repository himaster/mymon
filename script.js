function myAjax(serverip){
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


function show(serverip, server, task){
    var data = "&serverip=" + serverip + 
                  "&task=" + task;
    $.ajax({
        url: "index.php",
        data: data,
        cache: false,
        success: function(html){
            addTimeout($("#" + server + "_" + task));
            $("#" + server + "_" + task).html(html);
        },
        error: function(){
            $("#" + server + "_" + task).html("Error");
        }
    });
}

function addTimeout(Object) {
    Object.removeClass('timeout');
    window.setTimeout(function(){ Object.addClass('timeout'); }, 100);
}