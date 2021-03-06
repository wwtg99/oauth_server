<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><?php echo Flight::get('app'); ?></a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav nav-pills">
                <li><a href="/"><?php TP('Home'); ?></a></li>
                <li><a href="/apis"><?php TP('API List'); ?></a></li>
                <li><a href="/document"><?php TP('Documents'); ?></a></li>
<!--                <li class="dropdown">-->
<!--                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>-->
<!--                    <ul class="dropdown-menu">-->
<!--                        <li><a href="#">Action</a></li>-->
<!--                        <li><a href="#">Another action</a></li>-->
<!--                        <li><a href="#">Something else here</a></li>-->
<!--                        <li role="separator" class="divider"></li>-->
<!--                        <li><a href="#">Separated link</a></li>-->
<!--                        <li role="separator" class="divider"></li>-->
<!--                        <li><a href="#">One more separated link</a></li>-->
<!--                    </ul>-->
<!--                </li>-->
            </ul>
<!--            <form class="navbar-form navbar-left" role="search">-->
<!--                <div class="form-group">-->
<!--                    <input type="text" class="form-control" placeholder="--><?php //TP('Search'); ?><!--">-->
<!--                </div>-->
<!--                <button type="submit" class="btn btn-default">--><?php //TP('Search'); ?><!--</button>-->
<!--            </form>-->
            <ul class="nav navbar-nav nav-pills navbar-right">
                <?php if (getAuth()->isLogin()): ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php TIP('Hello'); ?>, <?php echo getUser('name'); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if(getAuth()->hasRole('admin')): ?>
                            <li><a href="/admin/home"><span class="glyphicon glyphicon-cog"></span> <?php TP('Admin'); ?></a></li>
                        <?php endif; ?>
                        <li><a href="/auth/info"><span class="glyphicon glyphicon-user"></span> <?php TP('User Center'); ?></a></li>
                        <li><a href="/auth/password"><span class="glyphicon glyphicon-lock"></span> <?php TP('Change Password'); ?></a></li>
                        <li><a href="/auth/logout"><span class="glyphicon glyphicon-log-out"></span> <?php TP('Logout'); ?></a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li><a href="/auth/login"><?php TP('Login'); ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>