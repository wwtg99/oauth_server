<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('User Center'); ?></h1>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <?php
            $msg = getOld('user_msg');
            if ($msg) {
                $ac = new \Components\Comp\AlertView($msg['type']);
                echo $ac->view(['message'=>T($msg['message'])]);
            }
            ?>
            <form class="form" role="form" id="form_user" method="post" action="/auth/user_edit">
                <div class="form-group">
                    <label class="control-label" for="txt_id" id="lb_id"><?php TP('user_id'); ?></label>
                    <input class="form-control" name="user_id" type="text" id="txt_id" value="<?php echo $user['user_id'] ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_name"><?php TP('name'); ?></label>
                    <input class="form-control" name="name" type="text" id="txt_name" value="<?php echo $user['name'] ?>" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_label"><?php TP('label'); ?></label>
                    <input class="form-control" name="label" type="text" id="txt_label" value="<?php echo $user['label'] ?>">
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_email"><?php TP('email'); ?></label>
                    <input class="form-control" name="email" type="text" id="txt_email" value="<?php echo $user['email']; ?>">
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_descr"><?php TP('descr'); ?></label>
                    <input class="form-control" name="descr" type="text" id="txt_descr" value="<?php echo $user['descr'] ?>">
                </div>
                <button class="btn btn-primary" type="submit"><?php TP('Submit'); ?></button>
                <button class="btn btn-default" type="reset"><?php TP('Reset'); ?></button>
                <a href="/auth/info" class="btn btn-default" type="button"><?php TP('Return'); ?></a>
            </form>
        </div>
    </div>
    <script>
        function init_center() {

        }

    </script>
</div>