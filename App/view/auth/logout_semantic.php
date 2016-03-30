<div class="ui middle aligned center aligned grid">
    <div class="column">
        <h1 class="ui teal image header">
            <i class="android icon"></i>
            <div class="content"><?php TP('Dear %name%', ['%name%'=>getUser('username')]); echo ', '; TP('confirm logout'); ?></div>
        </h1>
        <div class="row"></div>
        <form class="ui form" id="form_logout" role="form" action="/auth/logout" method="post">
            <br><br><br><br><br>
            <button class="ui teal submit button" type="submit" id="submit"><?php TIP('Logout'); ?></button>
        </form>
    </div>
</div>
