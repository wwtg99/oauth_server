<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('APIs'); ?></h1>
    </div>
    <div class="row">
        <p class="h4">基本参数：</p>
        <ul>
            <li><strong>access_token</strong></li>
            <li><strong>app_key</strong> (optional)</li>
        </ul>
    </div>
    <div class="row">
        <p class="h4">例子：</p>
        <p>/user/info?access_token=XXXXX&app_key=XXXXX</p>
    </div>
    <div class="row">
        <p class="h4">API列表：</p>
        <table class="table" id="tb_apis"></table>
    </div>
    <script>
        function init_center() {
            var head = <?php echo json_encode($api_head); ?>;
            $('#tb_apis').bootstrapTable({
                columns: head,
                data: <?php echo json_encode($apis); ?>,
                sortable: true,
                striped: true,
                search: true,
                pagination: true
            });
        }
    </script>
</div>