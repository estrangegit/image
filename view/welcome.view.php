<?php
/*
 * On vérifie que les données nécessaires à l'affichage de cette vue
 * sont bien définies.
 */
session_destroy();

?>

<div id="connexionForm">
    <form class="form-horizontal col-lg-offset-1 col-lg-4" method="get" action="index.php">
        <div class="form-group">
            <legend>S'inscrire</legend>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="login" class="col-lg-3 control-label">Nom : </label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="login" id="login">
                </div>
            </div>
        </div>
        <?php
        //permet d'afficher un message d'erreur en cas d'échec de la création de compte
        if(isset($data->registrationError))
        {
            ?>
            <div class="row">
                <div class="has-error col-lg-12">
                    <span class="help-block"><?= $data->registrationError ?></span>
                </div>
            </div>
            <?php
        }
        //permet d'afficher un message d'erreur en cas de réussite de la création de compte
        if(isset($data->registrationSuccess))
        {
        ?>
        <div class="row">
            <div class="has-success col-lg-12">
                <span class="help-block"><?= $data->registrationSuccess ?></span>
            </div>
        </div>
        <?php
        }
        ?>
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="controller" value="welcome.ctrl">

        <div class="form-group">
            <button class="pull-right btn btn-default">Envoyer</button>
        </div>
    </form>

    <form class="form-horizontal col-lg-offset-1 col-lg-4" method="get" action="index.php">
        <div class="form-group">
            <legend>Se connecter</legend>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="login" class="col-lg-3 control-label">Nom : </label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="login" id="login">
                </div>
            </div>
        </div>
        <?php
        //permet d'afficher un message d'erreur en cas d'échec de la connexion
        if(isset($data->connexionError))
        {
            ?>
            <div class="row">
                <div class="has-error col-lg-12">
                    <span class="help-block"><?= $data->connexionError ?></span>
                </div>
            </div>
            <?php
        }
        ?>
        <input type="hidden" name="action" value="login">
        <input type="hidden" name="controller" value="welcome.ctrl">

        <div class="form-group">
            <button class="pull-right btn btn-default">Envoyer</button>
        </div>
    </form>

</div>