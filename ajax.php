<script>
    function caller_<?php echo $server; ?>(){
        show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "la");
        <?php if (isset($db)) { ?>
            show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "rep");
        <?php } ?>
        <?php if ($errs == 1) { ?>
            show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "500");
        <?php } ?>
        <?php if ($elastic == 1) { ?>
            show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "elastic");
        <?php } ?>
    }

    $(document).ready(function(){
        caller_<?php echo $server; ?>();
        setInterval('caller<?php echo $server; ?>()',15000);
    });
</script>