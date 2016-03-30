<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <?php if(isDebug()):
        // debug bar
        $debugbar = Flight::get('debugbar');
        if ($debugbar):
            $debugRender = $debugbar->getJavascriptRenderer();
            ?>
        <style>
            <?php $debugRender->dumpCssAssets(); ?>
        </style>
        <script>
            <?php $debugRender->dumpJsAssets(); ?>
        </script>
        <?php endif; ?>
    <?php endif; ?>
    <?php getAssets()->dumpCss(); ?>
    <?php getAssets()->dumpJs(); ?>
    <title><?php echo (isset($title) ? T($title) : ''); echo ' '; echo ( isDebug() ? ' <' . T('Debug Mode') . '>' : ''); ?></title>
</head>
<body>
<header class="border_head"><?php echo (isset($head) ? $head: ''); ?></header>
<div class="border_body">
    <div class="border_left">
        <?php echo (isset($left) ? $left : ''); ?>
    </div>
    <div class="border_center">
        <?php echo (isset($center) ? $center : ''); ?>
    </div>
    <div class="border_right">
        <?php echo (isset($right) ? $right : ''); ?>
    </div>
</div>
<footer class="border_foot"><?php echo (isset($foot) ? $foot : ''); ?></footer>
<?php echo isDebug() ? $debugRender->render() : ''; ?>
<script>
    $(document).ready(function(){
        if (typeof init_head === 'function') {
            init_head();
        }
        if (typeof init_left === 'function') {
            init_left();
        }
        if (typeof init_center === 'function') {
            init_center();
        }
        if (typeof init_right === 'function') {
            init_right();
        }
        if (typeof init_foot === 'function') {
            init_foot();
        }
    });
</script>
</body>
</html>