<div id="corps">
    <div id="linkImageEdit">
            <img src="<?= $data->imageURL ?>" width = "<?= $state->size ?>px" class="img-rounded">
    </div>
    <div id="modifCategoryForm">
        <form class="form-horizontal col-lg-12" method="get" action="index.php?&controller=edit.ctrl&action=update">
                <div class="row">
                    <div class="form-group">
                    <label for="category" class="col-lg-12 control-label">Choisissez votre nouvelle catégorie</label><br />
                    <div class="col-lg-12">
                        <select name="category" id="category" class="form-control">
                            <?php
                            foreach($data->listCategories as $category) {
                                if($category == $state->category) {
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
            <div class="row">
                <div class="form-group">
                    <label for="comment" class="col-lg-12 control-label">Entrez votre commentaire</label>
                    <div class="col-lg-12">
                        <input type="text" name="comment" value="<?= $data->comment ?>" class="form-control"/>
                    </div>
                </div>
            </div>

            <input type="hidden" name="imgId" value="<?= $state->imgId ?>">
            <input type="hidden" name="controller" value="edit.ctrl">
            <input type="hidden" name="action" value="update">
            <div class="form-group">
                <a href="<?= $data->resetLink ?>" class="btn btn-default">Annuler</a>
                <input class="btn btn-default" type="submit" value="Envoyer" />
            </div>
        </form>
    </div>
</div>