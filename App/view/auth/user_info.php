<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('User Center'); ?></h1>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-1">
            <p><label><?php echo $user['name']; ?></label> (<?php echo $user['label']; ?>)</p>
            <hr>
            <p><?php echo $user['email']; ?></p>
            <p><?php TP('user_id'); ?> <?php echo $user['user_id']; ?></p>
            <p><?php TP('from department') ?> <?php echo $user['department']; ?></p>
            <p><?php TP('joined on') ?> <?php echo $user['created_at']; ?></p>
            <hr>
            <p><?php echo $user['descr']; ?></p>
            <p><a href="/auth/user_edit" class="btn btn-default"><span class="glyphicon glyphicon-pencil"> <?php TP('Edit'); ?></span></a></p>
        </div>
        <div class="col-md-6"></div>
    </div>
    <script>
        function init_center() {

        }

    </script>
</div>