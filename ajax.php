<script>
    $(document).ready(function(){
        show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "la");
        setInterval('show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "la")',15000);
        <?php if (isset($db)) { ?>
            show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "rep");
            setInterval('show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "rep")',15000);
        <?php } ?>
        <?php if ($errs == 1) { ?>
            show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "500");
            setInterval('show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "500")',15000);
        <?php } ?>
        <?php if ($elastic == 1) { ?>
            show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "elastic");
            setInterval('show("<?php echo $serverip; ?>", "<?php echo $server; ?>", "elastic")',15000);
        <?php } ?>
    });
</script>