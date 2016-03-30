<div class="center_align">
    <h1><?php echo Flight::get('app'); ?><?php if (isDebug()) echo ' &lt;' . T('Debug Mode') . '&gt;'; ?></h1>
    <div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-globe"></span> <?php TP('Language'); ?>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="?language=zh_CN">简体中文</a></li>
                <li><a href="?language=en_AM">English</a></li>
            </ul>
        </div>
    </div>
</div>
