<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('Role Management'); ?></h1>
    </div>
    <div class="row">
        <div class="btn-group" role="group">
            <a class="btn btn-default" href="/admin/add_role" role="button"><span class="glyphicon glyphicon-plus"></span> <?php TP('New Role'); ?></a>
            <a class="btn btn-default" href="/admin/home" role="button"><span class="glyphicon glyphicon-home"></span> <?php TP('Return'); ?></a>
        </div>
    </div>
    <br>
    <div class="row">
        <table class="table" id="tb_role"></table>
    </div>
    <script>
        function init_center() {
            var head = <?php echo json_encode($roles_head); ?>;
            for (var i in head) {
                if (head[i]['field'] == 'role_id') {
                    head[i]['formatter'] = function(val, row, index) {
                        return '<a href="/admin/roles?role_id=' + val + '">' + val + '</a>';
                    };
                }
            }
            head.push({
                field: 'operation',
                title: '<?php TP('operation'); ?>',
                formatter: function(val, row, index) {
                    var opts = [];
                    if (row['role_id'] != 1 && row['role_id'] != 2) {
                        opts.push('<button class="btn btn-link del"><?php TP('Delete'); ?></button>');
                    }
                    return opts.join(' ');
                },
                'events': {
                    'click .del': function(ent, val, row, index) {
                        BootstrapDialog.confirm('<?php TP('confirm delete'); ?>', function(re) {
                            if (re) {
                                var url = '/admin/delete_role';
                                var pdata = {"role_id": row['role_id']};
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
                    }
                }
            });
            $('#tb_role').bootstrapTable({
                columns: head,
                data: <?php echo json_encode($roles); ?>,
                sortable: true,
                striped: true,
                search: true,
                pagination: true
            });
        }
    </script>
</div>