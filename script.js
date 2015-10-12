function myAjax(serverip){
    $('#test_div').html('processing...');
    document.getElementById('test_div').style.display = 'block';
    $.ajax({
		url: 'replica.php?serverip=' + serverip,
		success: function(){
			$('#test_div').html('success');
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