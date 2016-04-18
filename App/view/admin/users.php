<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('User Management'); ?></h1>
    </div>
    <div class="row">
        <div class="btn-group" role="group">
            <a class="btn btn-default" href="/admin/add_user" role="button"><span class="glyphicon glyphicon-plus"></span> <?php TP('New User'); ?></a>
            <a class="btn btn-default" href="/admin/home" role="button"><span class="glyphicon glyphicon-home"></span> <?php TP('Return'); ?></a>
        </div>
    </div>
    <br>
    <div class="row">
        <table class="table" id="tb_user"></table>
    </div>
    <script>
        function init_center() {
            var head = <?php echo json_encode($users_head); ?>;
            for (var i in head) {
                if (head[i]['field'] == 'superuser') {
                    head[i]['formatter'] = booleanFormatter;
                } else if (head[i]['field'] == 'user_id') {
                    head[i]['formatter'] = function(val, row, index) {
                        return '<a href="/admin/users?user_id=' + val + '">' + val + '</a>';
                    };
                }
            }
            head.push({
                field: 'operation',
                title: '<?php TP('operation'); ?>',
                formatter: function(val, row, index) {
                    var opts = [];//['<button class="btn btn-link del"><?php TP('Delete'); ?></button>'];
                    if (!row['deleted_at']) {
                        opts.push('<button class="btn btn-link inac"><?php TP('Inactive'); ?></button>');
                    } else {
                        opts.push('<button class="btn btn-link ac"><?php TP('Active'); ?></button>');
                    }
                    return opts.join(' ');
                },
                'events': {
                    'click .del': function(ent, val, row, index) {
                        BootstrapDialog.confirm('<?php TP('confirm delete'); ?>', function(re) {
                            if (re) {
                                var url = '/admin/delete_user';
                                var pdata = {"user_id": row['user_id'], "hard": 1};
                                $.post(url, pdata, function (data) {
//                                    console.log(data);
                                    if (data['result']) {
                                        location.reload();
                                    } else {
                                        BootstrapDialog.alert('<?php TP('delete failed'); ?>');
                                    }
                                });
                            }
                        });
                    },
                    'click .inac': function(evt, val, row, index) {
                        var url = '/admin/delete_user';
                        var pdata = {"user_id": row['user_id'], "active": 0};
                        $.post(url, pdata, function(data) {
//                            console.log(data);
                            if (data['result']) {
                                location.reload();
                            } else {
                                BootstrapDialog.alert('<?php TP('inactive failed'); ?>');
                            }
                        });
                    },
                    'click .ac': function(evt, val, row, index) {
                        var url = '/admin/delete_user';
                        var pdata = {"user_id": row['user_id'], "active": 1};
                        $.post(url, pdata, function(data) {
//                            console.log(data);
                            if (data['result']) {
                                location.reload();
                            } else {
                                BootstrapDialog.alert('<?php TP('active failed'); ?>');
                            }
                        });
                    }
                }
            });
            $('#tb_user').bootstrapTable({
                columns: head,
                data: <?php echo json_encode($users); ?>,
                sortable: true,
                striped: true,
                search: true,
                pagination: true
            });
        }
    </script>
</div>