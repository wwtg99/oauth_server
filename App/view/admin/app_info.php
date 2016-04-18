<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('App Management'); ?></h1>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <?php
            $msg = getOld('admin_msg');
            if ($msg) {
                $ac = new \Components\Comp\AlertView($msg['type']);
                echo $ac->view(['message'=>T($msg['message'])]);
            }
            ?>
            <form class="form" role="form" id="form_app" method="post" action="/admin/add_app">
                <div class="form-group">
                    <label class="control-label" for="app_id" id="lb_id"><?php TP('app_id'); ?></label>
                    <input class="form-control" name="app_id" type="text" id="txt_id">
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_name"><?php TP('app_name'); ?></label>
                    <input class="form-control" name="app_name" type="text" id="txt_name" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_uri"><?php TP('redirect_uri'); ?></label>
                    <input class="form-control" name="redirect_uri" type="text" id="txt_uri" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_descr"><?php TP('descr'); ?></label>
                    <input class="form-control" name="descr" type="text" id="txt_descr">
                </div>
                <button class="btn btn-primary" type="submit"><?php TP('Submit'); ?></button>
                <button class="btn btn-default" type="reset"><?php TP('Reset'); ?></button>
                <a href="/admin/apps" class="btn btn-default" type="button"><?php TP('Return'); ?></a>
            </form>
        </div>
    </div>
    <?php if(isset($app['app_secret'])): ?>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <p><strong><?php TP('app_secret'); ?></strong></p>
            <div class="text-center">
                <div class="alert alert-info"><?php echo $app['app_secret']; ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <script>
        function init_center() {
            <?php if(isset($app_id)): ?>
            loadForm(<?php echo json_encode($app); ?>);
            <?php else: ?>
            $('#lb_id').hide();
            $('#txt_id').hide();
            <?php endif; ?>
        }

        function loadForm(user) {
            $('#txt_id').val(user['app_id']);
            $('#txt_id').prop('readonly', 1);
            $('#txt_name').val(user['app_name']);
            $('#txt_uri').val(user['redirect_uri']);
            $('#txt_descr').val(user['descr']);
        }
    </script>
</div>