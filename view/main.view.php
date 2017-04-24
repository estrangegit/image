<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="fr" >
<head>
    <title>Site SIL3</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href = "./bootstrap/css/bootstrap.css" rel = "stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="/image/view/style.css" media="screen" title="Normal" />
</head>
<body>
<div class="container">
    <?php
    if(!isset($data->menu)) {
        ?>
            <div id="topBigWhiteSpace" class="row"></div>
        <?php
    }else
    {
        ?>
        <div id="topSmallWhiteSpace" class="row"></div>
        <?php
    }
    ?>
    <div id="main" class="row">
        <div id="entete">
            <?php
            if(isset($data->disconnect)) {
                require_once("/view/$data->disconnect");
            }
            ?>
            <h1>Focus Gallery</h1>
        </div>
        <?php
        if(isset($data->menu)) {
            ?>
            <div id="menu">
                <nav class="navbar navbar-inverse">
                    <div class="container-fluid">
                        <ul class="nav navbar-nav">
                            <?php

                            foreach ($data->menu as $item => $act) {
                                ?>
                                <li><a href="<?= $act ?>"><?= $item ?></a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </nav>
            </div>
            <?php
        }
        require_once("/view/$data->content");
        ?>
    </div>

    <?php
    if(isset($data->formCategory))
        require_once("/view/$data->formCategory");
    ?>

    <div id="pied_de_page">
    </div>
</div>

<script src="./bootstrap/js/jQuery.js"></script>
<script src="./bootstrap/js/bootstrap.js"></script>
</body>
</html>


