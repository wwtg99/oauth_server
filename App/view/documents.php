<div class="container">
    <div class="page-header text-center">
        <h1><?php TP('Document'); ?></h1>
    </div>
    <div class="row">
        <p class="h3">OAuth介绍</p>
        <p>请参见 <a href="https://en.wikipedia.org/wiki/OAuth" target="_blank">https://en.wikipedia.org/wiki/OAuth</a> 以及 <a href="http://wiki.open.qq.com/wiki/website/OAuth2.0%E7%AE%80%E4%BB%8B" target="_blank">http://wiki.open.qq.com/wiki/website/OAuth2.0%E7%AE%80%E4%BB%8B</a></p>
    </div>
    <div class="row">
        <p class="h3">SDK下载</p>
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <li role="presentation"><a href="javascript:change_panel(1);">Python</a></li>
                <li role="presentation"><a href="javascript:change_panel(2);">PHP</a></li>
            </ul>
        </div>
        <div class="col-md-10">
            <div id="div_1" class="div_panel">
                <div><button class="btn btn-default" onclick="window.open('/sdk/python/oauth.zip')"><span class="glyphicon glyphicon-download-alt"></span> 下载SDK</button></div>
                <?php
                $md = new \Parsedown();
                echo $md->text($readme['python']);
                ?>
            </div>
            <div id="div_2" class="div_panel">
                <div><button class="btn btn-default" onclick="window.open('/sdk/php/oauth.zip')"><span class="glyphicon glyphicon-download-alt"></span> 下载SDK</button></div>
                <?php
                $md = new \Parsedown();
                echo $md->text($readme['php']);
                ?>
            </div>
        </div>
    </div>
    <script>
        function init_center() {
            change_panel(1);
        }

        function change_panel(id) {
            $('.div_panel').hide();
            $('#div_' + id).show();
        }
    </script>
</div>