<div class="center_align">
    <h1><?php TP('server error'); if (isset($code)) echo " ($code)"; ?></h1>
    <br>
    <p><?php if (isset($message)) {
            echo $message;
        } ?>
    </p>
    <br><br><br>
    <div>
        <button class="ui button" onclick="toHome();"><?php TP('Return'); ?></button>
    </div>
</div>
