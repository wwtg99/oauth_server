<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('Department Management'); ?></h1>
    </div>
    <div class="row">
        <div class="btn-group" role="group">
            <a class="btn btn-default" href="/admin/add_department" role="button"><span class="glyphicon glyphicon-plus"></span> <?php TP('New Department'); ?></a>
            <a class="btn btn-default" href="/admin/home" role="button"><span class="glyphicon glyphicon-home"></span> <?php TP('Return'); ?></a>
        </div>
    </div>
    <br>
    <div class="row">
        <table class="table" id="tb_role"></table>
    </div>
    <script>
        function init_center() {
            var head = <?php echo json_encode($departments_head); ?>;
            for (var i in head) {
                if (head[i]['field'] == 'department_id') {
                    head[i]['formatter'] = function(val, row, index) {
                        return '<a href="/admin/departments?department_id=' + val + '">' + val + '</a>';
                    };
                }
            }
            head.push({
                field: 'operation',
                title: '<?php TP('operation'); ?>',
                formatter: function(val, row, index) {
                    var opts = ['<button class="btn btn-link del"><?php TP('Delete'); ?></button>'];
                    return opts.join(' ');
                },
                'events': {
                    'click .del': function(ent, val, row, index) {
                        BootstrapDialog.confirm('<?php TP('confirm delete'); ?>', function(re) {
                            if (re) {
                                var url = '/admin/delete_department';
                                var pdata = {"department_id": row['department_id']};
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
                data: <?php echo json_encode($departments); ?>,
                sortable: true,
                striped: true,
                search: true,
                pagination: true
            });
        }
    </script>
</div>