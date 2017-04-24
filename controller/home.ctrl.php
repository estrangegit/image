<?php

class Home
{
    function queryLink($url, $controller, $action){

        $query = array(
            'controller' => $controller,
            'action' => $action
        );

        return $url."?".http_build_query($query);
    }

    function initUser($data){
        $data->login = $_SESSION['login'];
    }

    function index()
    {
        global $data;

        $data->menu['Home'] = $this->queryLink("index.php", "home.ctrl", "index");
        $data->menu['A propos'] = $this->queryLink("index.php", "home.ctrl", "apropos");
        $data->menu['Voir photos'] = $this->queryLink("index.php", "photo.ctrl", "index");

        $this->initUser($data);

        $data->content = "home.view.php";
        $data->disconnect = "disconnect.view.php";

        require_once("/view/main.view.php");
    }

    function apropos()
    {
        global $data;

        $data->menu['Home'] = $this->queryLink("index.php", "home.ctrl", "index");
        $data->menu['A propos'] = $this->queryLink("index.php", "home.ctrl", "apropos");
        $data->menu['Voir photos'] = $this->queryLink("index.php", "photo.ctrl", "index");

        $this->initUser($data);

        $data->content = "aPropos.view.php";
        $data->disconnect = "disconnect.view.php";

        require_once("/view/main.view.php");
    }
}