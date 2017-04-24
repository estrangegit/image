<div id="corps">
    <div id="prevNextLink" class="row col-lg-offset-3 col-lg-6 col-lg-offset-3">
        <ul class="pager col-lg-12">
            <li class="previous"><a href="<?= $data->prevLink ?>"><span class="glyphicon glyphicon-backward"></span> Previous</a></li>
            <li class="next"><a href="<?= $data->nextLink ?>">Next <span class="glyphicon glyphicon-forward"></span></a></li>
        </ul>
    </div>

    <section class="row col-lg-12">
        <?php
        foreach ($data->imgMatrixURL as $i) {
        ?>
        <a href="<?= $i[1] ?>">
            <img src="<?= $i[0] ?>" width="<?= $state->size ?>" height="<?= $state->size ?>" class=" imageMatrix img-rounded">
        </a>
        <?php
        };
        ?>
    </section>
</div>

