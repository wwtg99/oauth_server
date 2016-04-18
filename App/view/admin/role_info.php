<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('Role Management'); ?></h1>
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
            <form class="form" role="form" id="form_role" method="post" action="/admin/add_role">
                <div class="form-group">
                    <label class="control-label" for="txt_id" id="lb_id"><?php TP('role_id'); ?></label>
                    <input class="form-control" name="role_id" type="text" id="txt_id">
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_name"><?php TP('name'); ?></label>
                    <input class="form-control" name="name" type="text" id="txt_name" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_descr"><?php TP('descr'); ?></label>
                    <input class="form-control" name="descr" type="text" id="txt_descr">
                </div>
                <button class="btn btn-primary" type="submit"><?php TP('Submit'); ?></button>
                <button class="btn btn-default" type="reset"><?php TP('Reset'); ?></button>
                <a href="/admin/roles" class="btn btn-default" type="button"><?php TP('Return'); ?></a>
            </form>
        </div>
    </div>
    <script>
        function init_center() {
            <?php if(isset($role_id)): ?>
            loadForm(<?php echo json_encode($role); ?>);
            <?php else: ?>
            $('#lb_id').hide();
            $('#txt_id').hide();
            <?php endif; ?>
        }

        function loadForm(role) {
            $('#txt_id').val(role['role_id']);
            $('#txt_id').prop('readonly', 1);
            $('#txt_name').val(role['name']);
            $('#txt_descr').val(role['descr']);
        }
    </script>
</div>