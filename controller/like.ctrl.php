<?php

require_once("./model/userDAO.class.php");
require_once("./model/data.class.php");
require_once("./model/state.class.php");
require_once("./model/image.class.php");
require_once("./model/imageDAO.class.php");


class Like
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

    function queryLink($url, $controller, $action, $size, $category, $imgId){
        $query = array(
            'controller' => $controller,
            'action' => $action,
            'size' => $size,
            'category' => $category,
            'imgId' => $imgId
        );

        return $url."?".http_build_query($query);
    }

    function initImageFeature($data, $img){
        $data->imageURL = $this->data_path.$img->path();
        $data->imageCategory = $img->category();
        $data->imageComment = $img->comment();
    }

    function initView($data){
        $data->listCategories = $this->imageDAO->getListCategories();

        $data->content = "photo.view.php";

        $data->disconnect = "disconnect.view.php";
        $data->formCategory ="formCategory.view.php";
    }

    function initMenu($state, $data)
    {
        $data->menu['Home'] = $this->queryLink("index.php", "home.ctrl", "index", null, null, null, null);
        $data->menu['A propos'] = $this->queryLink("index.php", "home.ctrl", "apropos", null, null, null, null);
        $data->menu['First'] = $this->queryLink("index.php", "photo.ctrl", "first", $state->size, $state->category, null, null);
        $data->menu['Random'] = $this->queryLink("index.php", "photo.ctrl", "random", $state->size, $state->category, null, null);
        $data->menu['More'] = $this->queryLink("index.php", "photoMatrix.ctrl", "index", $state->imgId, $state->category, null, null);
        $data->menu['Zoom +'] = $this->queryLink("index.php", "photo.ctrl", "zoom", $state->size, $state->category, $state->imgId, 1.25);
        $data->menu['Zoom -'] = $this->queryLink("index.php", "photo.ctrl", "zoom", $state->size, $state->category, $state->imgId, 0.8);
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

        $data->likeLink =  $this->queryLink("index.php", "like.ctrl", "index", $size, $category, $imgId, null);
    }

    function initPageLink($state, $data){

        $data->nextLink = $this->queryLink("index.php", "photo.ctrl", "next", $state->size, $state->category, $state->imgId, null);
        $data->prevLink = $this->queryLink("index.php", "photo.ctrl", "prev", $state->size, $state->category, $state->imgId, null);
        $data->photoLink = $this->queryLink("index.php", "photo.ctrl", "zoom", $state->size, $state->category, $state->imgId, 1.25);
        $data->editLink = $this->queryLink("index.php", "edit.ctrl", "index", null, null, $state->imgId, null);
        $data->likeLink = $this->queryLink("index.php", "like.ctrl", "index", null, null, $state->imgId, null);
    }

    function initUser($data){
        $data->login = $_SESSION['login'];
    }

    function index(){

        global $data;
        global $state;

        $data = new Data();
        $state = new State();

        $id_image = $_GET['imgId'];
        $id_user = $_SESSION['id'];

        $img = $this->imageDAO->getImage($id_image);

        $state->category = $_GET['category'];
        $state->imgId = $_GET['imgId'];
        $state->size = $_GET['size'];

        if(!($this->userDAO->doesUserLikeImage($id_user, $id_image)))
        {
            $this->imageDAO->addUserImageLike($id_user, $id_image);
        }
        else{
            $this->imageDAO->deleteUserImageLike($id_user, $id_image);
        }

        $this->initMenu($state, $data);
        $this->initImageFeature($data, $img);
        $this->initPageLink($state, $data);
        $this->initLikeInfo($state, $data);
        $this->initUser($data);
        $this->initView($data);

        require_once("/view/main.view.php");
    }
}