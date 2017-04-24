<?php

require_once("./model/image.class.php");
require_once("./model/imageDAO.class.php");
require_once("./model/user.class.php");
require_once("./model/userDAO.class.php");
require_once("./model/data.class.php");
require_once("./model/state.class.php");

class Edit
{
    protected $imageDAO;
    protected $userDAO;
    protected $database_path;
    protected $data_path;

    function __construct()
    {
        $config = parse_ini_file('./config/config.ini');
        $this->database_path = $config['database_path'];
        $this->data_path = $config['data_path'];
        $this->imageDAO = new ImageDAO($this->database_path);
        $this->userDAO = new UserDAO($this->database_path);
    }

    function queryLink($controller, $action, $size, $category, $imgId, $zoom){
        $query = array(
            'controller' => $controller,
            'action' => $action,
            'size' => $size,
            'category' => $category,
            'imgId' => $imgId,
            'zoom' => $zoom
        );

        return "index?".http_build_query($query);
    }

    function initLikeInfo($state, $data){

        $id_user = $_SESSION['id'];
        $id_image = $state->imgId;

        if($this->userDAO->doesUserLikeImage($id_user, $id_image))
        {
            $data->urlLikeImage = './model/data/Icons/thumb_up_blue.png';
        }
        else
        {
            $data->urlLikeImage = './model/data/Icons/thumb_up_grey.png';
        }
        $data->nbLikeImage = $this->imageDAO->getNbLikeByImageId($id_image);

        if(isset($state->size))
            $size = $state->size;
        else
            $size = null;

        if(isset($state->category))
            $category = $state->category;
        else
            $category = null;

        if(isset($state->imgId))
            $imgId = $state->imgId;
        else
            $imgId = null;

        $data->likeLink =  $this->queryLink("like.ctrl", "index", $size, $category, $imgId, null);
    }

    function initPageLink($state, $data){

        $data->nextLink = $this->queryLink("photo.ctrl", "next", $state->size, $state->category, $state->imgId, null);
        $data->prevLink = $this->queryLink("photo.ctrl", "prev", $state->size, $state->category, $state->imgId, null);
        $data->photoLink = $this->queryLink("photo.ctrl", "zoom", $state->size, $state->category, $state->imgId, 1.25);
        $data->editLink = $this->queryLink("edit.ctrl", "index", null, null, $state->imgId, null);
    }

    function initUser($data){
        $data->login = $_SESSION['login'];
    }

    function index()
    {
        global $data;
        global $state;

        $data = new Data();
        $state = new State();

        $img = $this->imageDAO->getImage($_GET['imgId']);

        $state->imgId = $img->id();
        $state->category = $img->category();
        $data->comment = $img->comment();
        $data->imageURL = $this->data_path.$img->path();

        $data->listCategories = $this->imageDAO->getListCategories();

        $state->size = 480;

        $data->menu['Home'] = $this->queryLink("home.ctrl", "index", null, null, null, null);
        $data->menu['A propos'] = $this->queryLink("home.ctrl", "apropos", null, null, null, null);
        $data->menu['Voir photos'] = $this->queryLink("photo.ctrl", "index", null, null, null, null);

        //Initialisation du lien d'annulation du formulaire
        $data->resetLink = $this->queryLink("photo.ctrl", "index", null, $state->category, $state->imgId, null);

        //Initialisation des informations concernant l'utilisateur
        $this->initUser($data);

        $data->disconnect = "disconnect.view.php";
        $data->content = "edit.view.php";

        require_once("/view/main.view.php");
    }

    function update(){

        global $data;
        global $state;

        $data = new Data();
        $state = new State();

        //Mise à jour de l'image
        $comment = trim(htmlspecialchars($_GET['comment']));
        if(strlen($comment) == 0)
            $comment = "No comment";

        $category = $_GET['category'];

        $id = $_GET['imgId'];

        $img = $this->imageDAO->getImage($id);

        $img->setComment($comment);
        $img->setCategory($category);

        $this->imageDAO->updateCategoryComment($img);

        //Affichage de l'image modifiée
        $img = $this->imageDAO->getImage($id);
        $category = 'All';
        $imgId = $img->id();

        $state->imgId = $imgId;
        $state->category = $category;
        $state->size = 480;

        //Construction du menu

        $data->menu['Home'] = $this->queryLink("home.ctrl", "index", null, null, null, null);
        $data->menu['A propos'] = $this->queryLink("home.ctrl", "apropos", null, null, null, null);
        $data->menu['First'] = $this->queryLink("photo.ctrl", "first", $state->size, $state->category, null, null);
        $data->menu['Random'] = $this->queryLink("photo.ctrl", "random", $state->size, $state->category, null, null);
        $data->menu['More'] = $this->queryLink("photoMatrix.ctrl", "index", $state->imgId, $state->category, null, null);
        $data->menu['Zoom +'] = $this->queryLink("photo.ctrl", "zoom", $state->size, $state->category, $state->imgId, 1.25);
        $data->menu['Zoom -'] = $this->queryLink("photo.ctrl", "zoom", $state->size, $state->category, $state->imgId, 0.8);

        //Définition des caractèristiques de l'image
        $data->imageURL = $this->data_path.$img->path();
        $data->imageCategory = $img->category();
        $data->imageComment = $img->comment();

        $data->listCategories = $this->imageDAO->getListCategories();

        //Initialisation des informations concernant l'utilisateur
        $this->initUser($data);

        //Initialisation des liens de la page
        $this->initPageLink($state, $data);
        $this->initLikeInfo($state, $data);

        $data->content = "photo.view.php";
        $data->disconnect = "disconnect.view.php";
        $data->formCategory ="formCategory.view.php";

        require_once("/view/main.view.php");
    }
}