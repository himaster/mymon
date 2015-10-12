<script>
function show_<?php echo $server; ?>(){
        $.ajax({
            url: "server.php?serverip=<?php echo $serverip;?>&task=la",
            cache: false,
            success: function(html){
    	        $("#<?php echo $server. '_la'; ?>").html(html);
            },
            error: function(){
        		$.ajax(this);
        	}
        });
    <?php if (isset($db)) { ?>
    	$.ajax({
            url: "server.php?serverip=<?php echo $serverip;?>&task=rep",
            cache: false,
            success: function(html){
                $("#<?php echo $server. '_rep'; ?>").html(html);
            },
            error: function(){
                $.ajax(this);
            }
        });
    <?php } ?>
    <?php if ($errs == 1) { ?>
        $.ajax({
            url: "server.php?serverip=<?php echo $serverip;?>&task=500",
            cache: false,
            success: function(html){
                    $("#<?php echo $server. '_500'; ?>").html(html);
            },
            error: function(){
                    $.ajax(this);
            }
        });
    <?php } ?>
    <?php if ($elastic == 1) { ?>
        $.ajax({
            url: "server.php?serverip=<?php echo $serverip;?>&task=elastic",
            cache: false,
            success: function(html){
                    $("#<?php echo $server. '_elastic'; ?>").html(html);
            },
            error: function(){
                    $.ajax(this);
            }
        });
    <?php } ?>
    }
</script>