<div class="formCategory row">
    <form method="get" action="index.php?" class="col-lg-offset-3 col-lg-6 col-lg-offset-3">
        <div id="choiceCategoryForm" class="row">
            <div class="form-group">
                <label for="category" class="col-lg-6 control-label">Choisissez votre catégorie : </label><br />
                <div class="col-lg-6">
                <select name="category" id="category" class="form-control">
                    <?php
                    if($state->category == 'All')
                    {
                        ?>
                        <option value="All" selected>All</option>
                        <?php
                    }
                    else
                    {
                        ?>
                        <option value="All">All</option>
                        <?php
                    }
                    foreach($data->listCategories as $category) {
                        if($category == urldecode($state->category)) {
                            ?>
                            <option value="<?= $category ?>" selected><?= $category ?></option>
                            <?php
                        }else
                        {
                            ?>
                            <option value="<?= $category ?>"><?= $category ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
                </div>
            </div>
        </div>
        <input type="hidden" name="action" value="index">
        <input type="hidden" name="size" value="<?= $state->size ?>">
        <?php
        if(isset($state->nbImg))
        {
            ?>
            <input type="hidden" name="controller" value="photoMatrix.ctrl">
        <?php
        }
        else {
            ?>
            <input type="hidden" name="controller" value="photo.ctrl">
            <?php
        }
        ?>
        <div class="form-group">
            <input class="pull-right btn btn-default" type="submit" value="Envoyer" />
        </div>
    </form>
</div>