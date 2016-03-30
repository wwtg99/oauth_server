<div class="container">
    <div class="row text-center">
        <h1>
            <?php TIP('Dear %name%', ['%name%'=>getUser('name')]); echo ', '; TP('confirm logout'); ?>
        </h1>
        <div class="row"></div>
        <form class="form" id="form_logout" role="form" action="/auth/logout" method="post">
            <br><br><br><br><br>
            <button class="btn btn-default" type="submit" id="submit"><?php TIP('Logout'); ?></button>
        </form>
    </div>
</div>
