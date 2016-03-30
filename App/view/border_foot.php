<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 col-md-offset-1">
                <h4><?php TP('Help'); ?></h4>
                <p><a href="/changelog" class="item" target="_blank"><?php TP('Change Log') ?></a></p>
                <p><a href="mailto:wwu@genowise.com" class="item"><?php TP('Bug Report') ?></a></p>
            </div>
            <div class="col-md-3 col-md-offset-3">
                <h4><?php TP('Version'); ?>: <?php echo Flight::get('version'); ?></h4>
            </div>
        </div>
    </div>
</nav>