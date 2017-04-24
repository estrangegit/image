<div id="corps">
    <div id="prevNextLink" class="row col-lg-offset-3 col-lg-6 col-lg-offset-3">
        <ul class="pager col-lg-12">
            <li class="previous"><a href="<?= $data->prevLink ?>"><span class="glyphicon glyphicon-backward"></span> Previous</a></li>
            <li class="next"><a href="<?= $data->nextLink ?>">Next <span class="glyphicon glyphicon-forward"></span></a></li>
        </ul>
    </div>
    <div id="onePagePicture">
        <div>
            <a href="<?= $data->photoLink ?>" class="thumbnail">
                <img src="<?= $data->imageURL ?>" width = "<?= $state->size ?>px" class="img-rounded">
            </a>
        </div>
        <div>
            <a href="<?= $data->likeLink ?>" class="thumbnail">
                <img src="<?= $data->urlLikeImage ?>" class="img-rounded">
            </a>
            <span class="badge"><?= $data->nbLikeImage ?></span>
        </div>
    </div>
    <div class="metaData">
        <?= $data->imageCategory ?>
    </div>
    <div class="metaData">
        <?= $data->imageComment ?>
    </div>
    <div id="editLink">
        <a href="<?= $data->editLink ?>" class="btn btn-default">Edit</a>
    </div>
</div>