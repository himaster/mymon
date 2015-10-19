function myAjax(serverip){
    $('#test_div').html('processing...');
    document.getElementById('test_div').style.display = 'block';
    $.ajax({
		url: 'index.php?task=replica&serverip=' + serverip,
		success: function(html){
			$('#test_div').html('success' + html);
			setTimeout("document.getElementById('test_div').style.display = 'none'", 5000);
		},
		error: function(){
			$('#test_div').html('error');
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
            $("#" + server + "_" + task).html(html);
        },
        error: function(){
            $.ajax(this);
        }
    });
}

function show_la(serverip, server){
    var data_la = "&serverip=" + serverip + 
                  "&task=la";
    $.ajax({
        url: "index.php",
        data: data_la,
        cache: false,
        success: function(html){
            $("#" + server + "_la").html(html);
        },
        error: function(){
            $.ajax(this);
        }
    });
}

function show_db(serverip, server){
    var data_db = "&serverip=" + serverip + 
                  "&task=rep";
    $.ajax({
        url: "index.php",
        data: data_db,
        cache: false,
        success: function(html){
            $("#" + server + "_rep").html(html);
        },
        error: function(){
            $.ajax(this);
        }
    });
}

function show_500(serverip, server){
    var data_500 = "&serverip=" + serverip + 
                   "&task=500";
    $.ajax({
        url: "index.php",
        data: data_500,
        cache: false,
        success: function(html){
                $("#" + server + "_500").html(html);
        },
        error: function(){
                $.ajax(this);
        }
    });
}

function show_elastic(serverip, server){
    var data_elastic = "&serverip=" + serverip + 
                       "&task=elastic";
    $.ajax({
        url: "index.php",
        data: data_elastic,
        cache: false,
        success: function(html){
                $("#" + server + "_elastic").html(html);
        },
        error: function(){
                $.ajax(this);
        }
    });
}
