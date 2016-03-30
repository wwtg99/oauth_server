<div class="ui container">
    <div class="ui two column centered grid">
        <div class="column">
            <div class="ui items">
                <div class="item">
                    <div class="image">
                        <i class="huge android icon"></i>
                    </div>
                    <div class="content">
                        <a class="header">标题</a>
                        <div class="meta">
                            <span>Description</span>
                        </div>
                        <div class="description">
                            <p></p>
                        </div>
                        <div class="extra">Additional Details </div>
                    </div>
                </div>
                <div class="item">
                    <div class="image">
                        <i class="huge android icon"></i>
                    </div>
                    <div class="content">
                        <a class="header">标题</a>
                        <div class="meta">
                            <span>Description</span>
                        </div>
                        <div class="description">
                            <p></p>
                        </div>
                        <div class="extra">Additional Details </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <p>User</p>
        <p><?php print_r(getAuth()->getUser()); ?></p>
    </div>
    <div>
        <input type="text" id="in_en">
    </div>
    <div id="sec"></div>
    <div id="lcont"></div>
    <script>
        function init_center() {
//            $('#sec').redirectAfter('/', 3);
            $('#in_en').bindEnter(function(){
                alert('aaaa');
            });
            $('#lcont').loadContent({url: '/', done_func: function(data) {
                console.log(data);
            }})
        }
    </script>
</div>