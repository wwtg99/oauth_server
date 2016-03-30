<div>
    <h1>Components</h1>
    <div>
        <h3>Normal input</h3>
        <?php
        $comp1 = new \HtmlObject\Input('text', 'id1');
        echo $comp1->render();
        echo ' ';
        $comp2 = new \HtmlObject\Input('text', 'id2', 'text');
        echo $comp2->render();
        ?>
    </div>
    <br>
    <div>
        <h3>Radio and checkbox</h3>
        <?php
        $comp5 = new \HtmlObject\Input('radio', 'radio1');
        echo $comp5->render();
        echo ' ';
        $comp5 = new \HtmlObject\Input('radio', 'radio1');
        echo $comp5->render();
        echo ' ';
        $comp5 = new \HtmlObject\Input('radio', 'radio1');
        echo $comp5->render();
        echo ' ';
        $comp6 = new \HtmlObject\Input('checkbox', 'id6');
        echo $comp6->render();
        echo ' ';
        $comp6 = new \HtmlObject\Input('checkbox', 'id7', null, ['checked'=>1]);
        echo $comp6->render();
        ?>
    </div>
    <br>
    <div>
        <h3>Buttons</h3>
        <?php
        $comp10 = new \HtmlObject\Input('button', 'button', 'button');
        echo $comp10->render();
        echo ' ';
        $comp11 = new \HtmlObject\Input('submit', 'submit', 'submit');
        echo $comp11->render();
        echo ' ';
        $comp12 = new \HtmlObject\Input('reset', 'reset', 'reset');
        echo $comp12->render();
        ?>
    </div>
    <br>
    <div>
        <h3>Other inputs</h3>
        <?php
        $comp13 = new \HtmlObject\Input('password', 'password', 'password');
        echo $comp13->render();
        echo ' ';
        $comp14 = new \HtmlObject\Input('file', 'file', 'file');
        echo $comp14->render();
        echo ' ';
        $comp15 = new \HtmlObject\Input('number', 'number', 'number');
        echo $comp15->render();
        echo ' ';
        $comp16 = new \HtmlObject\Input('date', 'date', 'date');
        echo $comp16->render();
        ?>
    </div>
    <br>
    <div>
        <h3>Steps</h3>
        <?php
        echo $steps->view(['current'=>2]);
        ?>
    </div>
    <div class="clear"></div>
    <br>
    <div>
        <h3>List view</h3>
        <p>1 column left align</p>
        <?php
        echo $listview1->view(['data'=>['field1'=>'value1', 'field2'=>'value2']]);
        ?>
        <div class="clear"></div>
        <p>2 columns right align</p>
        <?php
        echo $listview2->view(['data'=>['field3'=>'value3', 'field4'=>'value4', 'field5'=>'value5']]);
        ?>
        <div class="clear"></div>
        <p>3 columns center align</p>
        <?php
        echo $listview3->view(['data'=>['field6'=>'value6', 'field7'=>'value7', 'field8'=>'value8', 'field9'=>'value9', 'field10'=>'value10', 'field11'=>'value11']]);
        ?>
    </div>
    <div class="clear"></div>
    <br>
    <div>
        <h3>Alert</h3>
        <?php
        $a1 = new \Components\Comp\AlertView('success');
        echo $a1->view(['message'=>'success']);
        $a1 = new \Components\Comp\AlertView('info');
        echo $a1->view(['message'=>'info']);
        $a1 = new \Components\Comp\AlertView('warning');
        echo $a1->view(['message'=>'warning']);
        $a1 = new \Components\Comp\AlertView('danger');
        echo $a1->view(['message'=>'danger']);
        ?>
    </div>
    <br>
    <div>
        <h3>Pagination</h3>
        <p>First</p>
        <?php
        \Flight2wwu\Component\Database\Pagination::clearPage('test');
        $page = \Flight2wwu\Component\Database\Pagination::getAutoPage('test');
        echo "limit " . $page->getLimit() . " offset " . $page->getOffset();
        ?>
        <p>Second</p>
        <?php
        $page = \Flight2wwu\Component\Database\Pagination::getAutoPage('test');
        echo "limit " . $page->getLimit() . " offset " . $page->getOffset();
        ?>
        <p>Third</p>
        <?php
        $page = \Flight2wwu\Component\Database\Pagination::getAutoPage('test');
        echo "limit " . $page->getLimit() . " offset " . $page->getOffset();
        ?>
    </div>
    <br>
    <div>
        <h3>iCheck</h3>
        <p><label><input class="icheck" type="checkbox">check1</label><label><input class="icheck" type="radio">check2</label><label><input class="icheck square" type="checkbox">check3</label></p>
    </div>
    <div>
        <h3>Bootstrap Switch</h3>
        <p><input class="switch" type="checkbox" checked /></p>
    </div>
    <div>
        <h3>Buttons</h3>
        <div>
            <a href="#" class="button button-action">Go</a>
            <a href="#" class="button button-action button-rounded">Go</a>
            <a href="#" class="button button-action button-pill">Go</a>
            <button class="button button-action button-square"><i class="fa fa-plus"></i></button>
            <button class="button button-action button-box"><i class="fa fa-plus"></i></button>
            <button class="button button-action button-circle"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <script>
        function init_center() {
            $('.icheck').iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal'
            });
            $('.icheck.square').iCheck({
                checkboxClass: 'icheckbox_square-red',
                radioClass: 'iradio_square',
                increaseArea: '20%' // optional
            });
            $('.switch').bootstrapSwitch();
        }
    </script>
</div>