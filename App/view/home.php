<div class="center_align home">
<!--    <div class="page-header"><h1>--><?php //echo Flight::get('app'); ?><!----><?php //if (isDebug()) echo ' &lt;' . T('Debug Mode') . '&gt;'; ?><!--</h1></div>-->
    <div class="logo"><img src="<?php echo getAssets()->getResource('genoauth.png'); ?>"></div>
    <div class="tools">
        <div class="btn-group-vertical" role="group">
            <a href="mailto:wwu@genowise.com" class="btn btn-default"><span class="glyphicon glyphicon-envelope"></span></a>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-globe"></span> <?php //TP('Language'); ?>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="?language=zh_CN">简体中文</a></li>
                <li><a href="?language=en_AM">English</a></li>
            </ul>
        </div>
    </div>
</div>
