<div class="ui middle aligned center aligned three column grid">
    <div class="column">
        <h1 class="ui teal image header">
            <i class="android icon"></i>
            <div class="content"><?php TIP('login to have our service'); ?></div>
        </h1>
        <?php
        $errmsg = getOld('login_error');
        ?>
        <form class="ui large form <?php if ($errmsg) echo 'error'; ?>" id="form_login" role="form" action="/auth/login" method="post">
            <div class="ui center aligned stacked segment">
                <div class="field">
                    <div class="ui left icon input">
                        <i class="user icon"></i>
                        <input type="text" name="name" placeholder="<?php TIP('Username'); ?>" value="<?php echo getOld('username'); ?>" tabindex="1">
                    </div>
                </div>
                <div class="field">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input type="password" name="password" placeholder="<?php TIP('Password'); ?>" tabindex="2">
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="remember">
                        <label><?php TP('Remember me'); ?></label>
                    </div>
                </div>
                <button class="ui fluid large teal submit button" type="submit" id="submit" tabindex="3"><?php TIP('Login'); ?></button>
            </div>
            <div class="ui error message">
                <?php TIP($errmsg); ?>
            </div>
        </form>
    </div>
</div>
<script>
    function init_center() {

    }
</script>
