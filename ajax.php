<script>
    function show(serverip, server){
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
    <?php if (isset($db)) { ?>
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
    <?php } ?>
    <?php if ($errs == 1) { ?>
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
    <?php } ?>
    <?php if ($elastic == 1) { ?>
        var data_elastic = "&serverip=" + serverip + 
                           "&task=elastic";
        $.ajax({
            url: "index.php",
            data: data_elastic,
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

    $(document).ready(function(){
        show("<?php echo $serverip; ?>", "<?php echo $server; ?>");
        setInterval('show("<?php echo $serverip; ?>", "<?php echo $server; ?>")',15000);
    });
</script>