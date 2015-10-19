<script>
    $(document).ready(function(){
        show_la("<?php echo $serverip; ?>", "<?php echo $server; ?>");
        <?php if (isset($db)) { ?>
            show_db("<?php echo $serverip; ?>", "<?php echo $server; ?>");
        <?php } ?>
        <?php if ($errs == 1) { ?>
            show_500("<?php echo $serverip; ?>", "<?php echo $server; ?>");
        <?php } ?>
        <?php if ($elastic == 1) { ?>
            show_elastic("<?php echo $serverip; ?>", "<?php echo $server; ?>");
        <?php } ?>    
        setInterval('show("<?php echo $serverip; ?>", "<?php echo $server; ?>")',15000);
    });
</script>