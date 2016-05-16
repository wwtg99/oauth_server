<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('User Management'); ?></h1>
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
            <form class="form" role="form" id="form_user" method="post" action="/admin/add_user">
                <div class="form-group">
                    <label class="control-label" for="txt_id" id="lb_id"><?php TP('user_id'); ?></label>
                    <input class="form-control" name="user_id" type="text" id="txt_id">
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_name"><?php TP('name'); ?></label>
                    <input class="form-control" name="name" type="text" id="txt_name" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_label"><?php TP('label'); ?></label>
                    <input class="form-control" name="label" type="text" id="txt_label">
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_email"><?php TP('email'); ?></label>
                    <input class="form-control" name="email" type="text" id="txt_email">
                </div>
                <div class="form-group">
                    <label class="control-label" for="sel_dep"><?php TP('department'); ?></label>
                    <select name="department_id" class="selectpicker form-control" id="sel_dep">
                        <?php foreach($departments as $d): ?>
                            <option value="<?php echo $d['department_id'] ?>"><?php echo $d['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label" for="txt_descr"><?php TP('descr'); ?></label>
                    <input class="form-control" name="descr" type="text" id="txt_descr">
                </div>
                <div class="form-group">
                    <label class="control-label"><?php TP('roles'); ?></label>
                    <input name="roles" type="hidden" id="txt_roles">
                </div>
                <div class="form-group">
                    <?php foreach($roles as $r): ?>
                        <label><input class="ch_roles" type="checkbox" id="ch_<?php echo $r['name']; ?>"> <?php echo $r['name']; ?> </label>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-primary" type="submit"><?php TP('Submit'); ?></button>
                <button class="btn btn-default" type="reset"><?php TP('Reset'); ?></button>
                <button class="btn btn-default" type="button" id="bt_reset_pwd"><?php TP('Reset Password'); ?></button>
                <a href="/admin/users" class="btn btn-default" type="button"><?php TP('Return'); ?></a>
            </form>
        </div>
    </div>
    <script>
        function init_center() {
            $('input').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
            $('.selectpicker').selectpicker();
            window.roles = [];
//            $('.ch_roles').click(changeRoles);
            $('.ch_roles').on('ifChanged', changeRoles);
            <?php if(isset($user_id)): ?>
            loadForm(<?php echo json_encode($user); ?>);
            $('#bt_reset_pwd').click(reset_pwd);
            <?php else: ?>
            $('#lb_id').hide();
            $('#txt_id').hide();
            <?php endif; ?>
        }

        function loadForm(user) {
            $('#txt_id').val(user['user_id']);
            $('#txt_id').prop('readonly', 1);
            $('#txt_name').val(user['name']);
            $('#txt_label').val(user['label']);
            $('#txt_email').val(user['email']);
            $('#txt_descr').val(user['descr']);
            $('#sel_dep').selectpicker('val', user['department_id']);
            var roles = user['roles'];
            roles = roles.split(',');
            window.roles = roles;
            for(var i in roles) {
                $('#ch_' + roles[i]).iCheck('check');
            }
        }

        function changeRoles()
        {
            var role = $(this).prop('id');
            role = role.substr(3);
//            console.log(role);
            if ($(this).prop('checked')) {
                if (_.indexOf(window.roles, role) == -1) {
                    window.roles.push(role);
                }
            } else {
                if (_.indexOf(window.roles, role) >= 0) {
                    window.roles = _.pull(window.roles, role);
                }
            }
            window.roles = _.uniq(window.roles);
            $('#txt_roles').val(window.roles.join(','));
        }

        function reset_pwd() {
            var user_id = '<?php echo isset($user_id) ? $user_id: ''; ?>';
            BootstrapDialog.confirm('<?php TP('confirm to reset password') ?>', function(result) {
                if (result) {
                    var url = '/admin/reset_pwd';
                    var pdata = {user_id: user_id};
                    $.post(url, pdata, function (data) {
                        if (data['result']) {
                            BootstrapDialog.alert('<?php TP('reset successfully') ?>');
                        } else {
                            BootstrapDialog.alert('<?php TP('reset failed') ?>');
                        }
                    });
                }
            });
        }
    </script>
</div>