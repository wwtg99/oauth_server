<div class="container">
    <div class="page-header text-center">
        <h1><?php TIP('Login'); ?></h1>
    </div>
    <div class="row">
        <?php if(isset($error)): ?>
            <div class="col-md-6 col-md-offset-3">
                <div class="alert alert-danger text-center">
                    <h4>(<?php echo $error['code']; ?>) <?php echo $error['message']; ?></h4>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-4 col-md-offset-2">
                <br><br>
                <form class="form-horizontal" role="form" id="form_grant" method="post">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                            <input class="form-control" type="text" name="username" placeholder="<?php TIP('Username'); ?>" tabindex="1">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                            <input class="form-control" type="password" name="password" placeholder="<?php TIP('Password'); ?>" tabindex="2">
                        </div>
                    </div>
                    <input type="hidden" name="scope" value="<?php echo implode(',', $scope); ?>">
                    <?php if(isset($app_id)): ?>
                        <input type="hidden" name="client_id" value="<?php echo $app_id; ?>">
                    <?php endif; ?>
                    <input type="hidden" name="redirect_uri" value="<?php echo $redirect_uri; ?>">
                    <div class="form-group text-center">
                        <button class="btn btn-success" type="submit" id="submit" tabindex="3"><?php TIP('Grant and Login'); ?></button>
                    </div>
                    <?php if(isset($msg)): ?>
                        <?php
                        $ac = new \Components\Comp\AlertView($status);
                        echo $ac->view(['message'=>TI($msg)]);
                        ?>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-3 col-md-offset-1">
                <?php if(isset($app_id)): ?>
                    <div class="page-header"><?php TP('Grant to'); echo ' ' . $app_name; ?></div>
                    <p><?php echo $app_descr; ?></p>
                <?php else: ?>
                    <div class="page-header"><?php TP('Grant to untrusted app'); ?></div>
                <?php endif; ?>
                <p><?php TP('will have these rights'); ?>:</p>
                <ul class="list-group">
                    <?php foreach($scope as $s): ?>
                        <li class="list-group-item"><?php TP($s); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    function init_center() {

    }
</script>
