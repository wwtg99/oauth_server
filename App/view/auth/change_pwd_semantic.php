<div class="ui middle aligned center aligned three column grid">
    <div class="column">
        <h1 class="ui teal image header">
            <div class="content"><?php TIP('change password'); ?></div>
        </h1>
        <?php
        $errmsg = getOld('auth_error');
        ?>
        <form class="ui large form <?php if ($errmsg) echo 'error'; ?>" id="form_pwd" role="form" action="/auth/password" method="post">
            <div class="ui stacked segment">
                <div class="field">
                    <div class="ui left icon input">
                        <i class="privacy icon"></i>
                        <input type="password" name="old" placeholder="<?php TIP('Old Password'); ?>" tabindex="1">
                    </div>
                </div>
                <div class="field">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input type="password" name="new1" placeholder="<?php TIP('New Password'); ?>" tabindex="2">
                    </div>
                </div>
                <div class="field">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input type="password" name="new2" placeholder="<?php TIP('Retype Password'); ?>" tabindex="3">
                    </div>
                </div>
                <button class="ui teal submit button" type="submit" id="submit" tabindex="4"><?php TIP('Submit'); ?></button>
                <button class="ui teal button" type="reset" id="reset" tabindex="4"><?php TIP('Reset'); ?></button>
            </div>
            <div class="ui error message">
                <?php TIP($errmsg); ?>
            </div>
            <div class="ui center aligned" id="sec"></div>
        </form>
    </div>
</div>
<script>
    function init_center() {
        <?php if ($errmsg == 'password changed'): ?>
        redirectAfter('/', 3, $('#sec'));
        <?php endif; ?>
    }
</script>
<div class="ui middle aligned center aligned three column grid">
    <div class="column">
        <h1 class="ui teal image header">
            <div class="content"><?php TIP('change password'); ?></div>
        </h1>
        <?php
        $errmsg = getOld('auth_error');
        ?>
        <form class="ui large form <?php if ($errmsg) echo 'error'; ?>" id="form_pwd" role="form" action="/auth/password" method="post">
            <div class="ui stacked segment">
                <div class="field">
                    <div class="ui left icon input">
                        <i class="privacy icon"></i>
                        <input type="password" name="old" placeholder="<?php TIP('Old Password'); ?>" tabindex="1">
                    </div>
                </div>
                <div class="field">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input type="password" name="new1" placeholder="<?php TIP('New Password'); ?>" tabindex="2">
                    </div>
                </div>
                <div class="field">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input type="password" name="new2" placeholder="<?php TIP('Retype Password'); ?>" tabindex="3">
                    </div>
                </div>
                <button class="ui teal submit button" type="submit" id="submit" tabindex="4"><?php TIP('Submit'); ?></button>
                <button class="ui teal button" type="reset" id="reset" tabindex="4"><?php TIP('Reset'); ?></button>
            </div>
            <div class="ui error message">
                <?php TIP($errmsg); ?>
            </div>
            <div class="ui center aligned" id="sec"></div>
        </form>
    </div>
</div>
<script>
    function init_center() {
        <?php if ($errmsg == 'password changed'): ?>
        redirectAfter('/', 3, $('#sec'));
        <?php endif; ?>
    }
</script>
