<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <form class="navbar-form navbar-right inline-form" method="post" action="index.php">
            <div class="form-group">
                <input type="hidden" name="controller" value="welcome">
                <input type="hidden" name="action" value="index">
                <button type="submit" class="navbar-right btn btn-default btn-sm">
                    <span class="glyphicon glyphicon-user"></span> Déconnexion
                </button>
            </div>
        </form>
        <p class="navbar-text navbar-right">Bonjour <?= $data->login ?></p>
    </div>
</nav>