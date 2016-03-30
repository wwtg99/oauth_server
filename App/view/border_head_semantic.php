<div class="ui fixed inverted menu">
    <div class="ui container">
        <a href="/" class="header item">
            <i class="android icon"></i>Flight2wwu
        </a>
        <a href="#" class="item" id="sidebar_toggle"><i class="sidebar icon"></i><?php TP('Menu'); ?></a>
        <div class="ui simple dropdown item">
            Dropdown
            <i class="dropdown icon"></i>
            <div class="menu">
                <a class="item">1</a>
                <a class="item">2</a>
            </div>
        </div>
        <div class="item">
            <div class="ui inverted left icon input">
                <input type="text" placeholder="<?php TP('Search'); ?>" id="txt_search">
                <i class="search icon"></i>
            </div>
        </div>
        <?php if (getAuth()->isLogin()): ?>
            <div class="ui right floated simple dropdown item">
                <?php TIP('Hello'); ?>, <?php echo getUser('username'); ?>
                <i class="dropdown icon"></i>
                <div class="menu">
                    <a href="#" class="item"><i class="user icon"></i><?php TP('User Center'); ?></a>
                    <a href="/auth/password" class="item"><i class="privacy icon"></i><?php TP('Change Password'); ?></a>
                    <a href="/auth/logout" class="item"><i class="sign out icon"></i><?php TP('Logout'); ?></a>
                </div>
            </div>
        <?php else: ?>
            <a href="/auth/login" class="right floated item"><i class="sign in icon"></i><?php TP('Login'); ?></a>
        <?php endif; ?>
    </div>
</div>
<script language="JavaScript">
    function init_head() {
        $('#sidebar_toggle').on('click', function() {
            $('.ui.left.sidebar').sidebar('toggle');
        });
        $('#txt_search').bindEnter(function() {
            alert('search');
        });
    }
</script>