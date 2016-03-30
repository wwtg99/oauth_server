<div class="container">
    <div class="page-header text-center">
        <h1><?php TIP('login to have our service'); ?></h1>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form class="form-horizontal" role="form" id="form_login" action="/auth/login" method="post">
                <div class="form-group">
                    <label class="control-label col-md-2"><span class="glyphicon glyphicon-user"></span></label>
                    <div class="col-md-10">
                        <input class="form-control" type="text" name="username" placeholder="<?php TIP('Username'); ?>" value="<?php echo getOld('username'); ?>" tabindex="1">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2"><span class="glyphicon glyphicon-lock"></span></label>
                    <div class="col-md-10">
                        <input class="form-control" type="password" name="password" placeholder="<?php TIP('Password'); ?>" tabindex="2">
                    </div>
                </div>
                <div class="checkbox text-center">
                    <label><input type="checkbox" name="remember"><?php TP('Remember me'); ?></label>
                </div>
                <div class="form-group text-center">
                    <button class="btn btn-primary" type="submit" id="submit" tabindex="3"><?php TIP('Login'); ?></button>
                </div>
                <?php if(isset($msg)): ?>
                    <?php
                    $ac = new \Components\Comp\AlertView($status);
                    echo $ac->view(['message'=>TI($msg)]);
                    ?>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
<script>
    function init_center() {

    }
</script>
