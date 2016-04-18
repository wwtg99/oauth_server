<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('Department Management'); ?></h1>
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
            <form class="form" role="form" id="form_department" method="post" action="/admin/add_department">
                <div class="form-group">
                    <label class="control-label" for="txt_id" id="lb_id"><?php TP('department_id'); ?></label>
                    <input class="form-control" name="department_id" type="text" id="txt_id" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_name"><?php TP('name'); ?></label>
                    <input class="form-control" name="name" type="text" id="txt_name" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_descr"><?php TP('descr'); ?></label>
                    <input class="form-control" name="descr" type="text" id="txt_descr">
                </div>
                <input name="new" id="txt_new" type="hidden" value="1">
                <button class="btn btn-primary" type="submit"><?php TP('Submit'); ?></button>
                <button class="btn btn-default" type="reset"><?php TP('Reset'); ?></button>
                <a href="/admin/departments" class="btn btn-default" type="button"><?php TP('Return'); ?></a>
            </form>
        </div>
    </div>
    <script>
        function init_center() {
            <?php if(isset($department_id)): ?>
            loadForm(<?php echo json_encode($department); ?>);
            $('#txt_new').val('');
            <?php endif; ?>
        }

        function loadForm(department) {
            $('#txt_id').val(department['department_id']);
            $('#txt_id').prop('readonly', 1);
            $('#txt_name').val(department['name']);
            $('#txt_descr').val(department['descr']);
        }
    </script>
</div>