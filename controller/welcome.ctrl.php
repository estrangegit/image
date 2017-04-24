<?php

    require_once("./model/userDAO.class.php");
    require_once("./model/data.class.php");


class Welcome
{
    protected $imageDAO;
    protected $database_path;

    function __construct()
    {
        $config = parse_ini_file('./config/config.ini');
        $this->database_path = $config['database_path'];

        $this->userDAO = new UserDAO($this->database_path);
    }

    function queryLink($url, $controller, $action){

        $query = array(
            'controller' => $controller,
            'action' => $action
        );

        return $url."?".http_build_query($query);
    }

    function index()
    {

        global $data;
        $data = new Data();

        $data->content = "welcome.view.php";
        require_once("/view/main.view.php");
    }

    function login()
    {
        global $data;
        $data = new Data();

        $login = $_GET['login'];

        $user = $this->userDAO->getUserByLogin($login);

        if($user == null)
        {
            $data->connexionError = "Erreur : utilisateur non enregistré...";

            $data->content = "welcome.view.php";
            require_once("/view/main.view.php");
        }
        else
        {
            $_SESSION['id'] = $user->id();
            $_SESSION['login'] = $user->login();

            $data->menu['Home'] = $this->queryLink("index.php", "home.ctrl", "index");
            $data->menu['A propos'] = $this->queryLink("index.php", "home.ctrl", "apropos");
            $data->menu['Voir photos'] = $this->queryLink("index.php", "photo.ctrl", "index");

            $data->login = $_SESSION['login'];

            $data->content = "home.view.php";
            $data->disconnect = "disconnect.view.php";

            require_once("/view/main.view.php");
        }
    }

    function register()
    {

        global $data;
        $data = new Data();

        $login = $_GET['login'];

        $user = $this->userDAO->getUserByLogin($login);

        if($user == null)
        {
            $this->userDAO->createUser($login);

            $data->registrationSuccess = "Vous pouvez vous connecter!";
            $data->content = "welcome.view.php";
            require_once("/view/main.view.php");
        }
        else
        {
            $data->registrationError = "Erreur : login déjà utilisé...";
            $data->content = "welcome.view.php";
            require_once("/view/main.view.php");
        }
    }

}