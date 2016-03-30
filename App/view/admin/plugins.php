<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('Plugins'); ?></h1>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <table class="table" id="tb_plugin"></table>
        </div>
    </div>
    <script>
        function init_center() {
            var head = <?php echo json_encode($plugins_head); ?>;
            for (var i in head) {
                if (head[i]['field'] == 'enabled') {
                    head[i]['formatter'] = booleanFormatter;
                }
            }
            head.push({
                field: 'operation',
                title: '<?php TIP('Operation') ?>',
                events: {
                    'click .bt_dis': function(evt, val, row, index) {
                        $.post('/admin/disable_plugin', {id: row['id']}, function(data) {
                            location.reload();
                        });
                    },
                    'click .bt_enb': function(evt, val, row, index) {
                        $.post('/admin/enable_plugin', {id: row['id']}, function(data) {
                            location.reload();
                        });
                    }
                },
                formatter: function(val, row, index) {
                    if (row['enabled']) {
                        var bt = '<div class="text-center"><button class="btn btn-link bt_dis"><span class="glyphicon glyphicon-minus-sign"></span></button></div>';
                    } else {
                        var bt = '<div class="text-center"><button class="btn btn-link bt_enb"><span class="glyphicon glyphicon-plus-sign"></span></button></div>';
                    }
                    return bt;
                }
            });
            $('#tb_plugin').bootstrapTable({
                columns: head,
                data: <?php echo json_encode($plugins); ?>,
                sortable: true,
                striped: true,
                search: true
            });
        }
    </script>
</div>