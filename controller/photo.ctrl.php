<?php

    require_once("./model/image.class.php");
    require_once("./model/imageDAO.class.php");
    require_once("./model/user.class.php");
    require_once("./model/userDAO.class.php");
    require_once("./model/data.class.php");
    require_once ("./model/state.class.php");

    class Photo{

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

            return "index.php?".http_build_query($query);
        }

        function initMenu($state, $data)
        {
            $data->menu['Home'] = $this->queryLink("home.ctrl", "index", null, null, null, null);
            $data->menu['A propos'] = $this->queryLink("home.ctrl", "apropos", null, null, null, null);
            $data->menu['First'] = $this->queryLink("photo.ctrl", "first", $state->size, $state->category, null, null);
            $data->menu['Random'] = $this->queryLink("photo.ctrl", "random", $state->size, $state->category, null, null);
            $data->menu['More'] = $this->queryLink("photoMatrix.ctrl", "index", $state->imgId, $state->category, null, null);
            $data->menu['Zoom +'] = $this->queryLink("photo.ctrl", "zoom", $state->size, $state->category, $state->imgId, 1.25);
            $data->menu['Zoom -'] = $this->queryLink("photo.ctrl", "zoom", $state->size, $state->category, $state->imgId, 0.8);
        }

        function getSize($state)
        {
            if (isset($_GET["size"])) {
                $state->size = $_GET["size"];
            } else {
                $state->size = 480;
            }
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
            $data->likeLink = $this->queryLink("like.ctrl", "index", null, null, $state->imgId, null);
        }

        function initUser($data){
            $data->login = $_SESSION['login'];
        }

        function index(){
            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            if (isset($_GET["imgId"])) {
                if(isset($_GET["category"]))
                {
                    $imgId = $_GET["imgId"];
                    $category = $_GET["category"];
                    $img = $this->imageDAO->getImageCategory($imgId, $category);
                }
                else{
                    $imgId = $_GET["imgId"];
                    $category = 'All';
                    $img = $this->imageDAO->getImageCategory($imgId, $category);
                }
            } else {
                if(isset($_GET["category"]))
                {
                    $category = $_GET["category"];
                    $img = $this->imageDAO->getFirstImageCategory($category);
                    $imgId = $img->id();
                }
                else{
                    $img = $this->imageDAO->getFirstImage();
                    $imgId = $img->id();
                    $category = 'All';
                }
            }

            $state->imgId = $imgId;
            $state->category = $category;

            //Récupération de la taille en paramètres
            $this->getSize($state);

            //Construction du menu
            $this->initMenu($state, $data);

            //Définition des caractèristiques de l'image
            $this->initImageFeature($data, $img);

            //Initialisation des liens de la page
            $this->initPageLink($state, $data);

            //Initialisation des paramètres pour la fonction like
            $this->initLikeInfo($state, $data);

            //Initialisation des informations de l'utilisateur
            $this->initUser($data);

            //Définition du contenu de la vue
            $this->initView($data);

            require_once("/view/main.view.php");
        }

        function first(){
            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            if(isset($_GET["category"]))
            {
                $category = $_GET["category"];
                $img = $this->imageDAO->getFirstImageCategory($category);
                $imgId = $img->id();
            }
            else{
                $img = $this->imageDAO->getFirstImage();
                $imgId = $img->id();
                $category = 'All';
            }

            $this->getSize($state);

            $state->imgId = $imgId;
            $state->category = $category;

            $this->initMenu($state, $data);

            $this->initImageFeature($data, $img);

            $this->initPageLink($state, $data);

            $this->initLikeInfo($state, $data);

            $this->initUser($data);

            $this->initView($data);

            require_once("/view/main.view.php");
        }

        function next(){

            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            if (isset($_GET["imgId"])) {
                if(isset($_GET["category"]))
                {
                    $imgId = $_GET["imgId"];
                    $category = $_GET["category"];
                    $img = $this->imageDAO->getImageCategory($imgId, $category);
                    $img = $this->imageDAO->getNextImageCategory($img, $category);
                    $imgId = $img->id();
                }
                else{
                    $imgId = $_GET["imgId"];
                    $category = 'All';
                    $img = $this->imageDAO->getImageCategory($imgId, $category);
                    $img = $this->imageDAO->getNextImageCategory($img, $category);
                    $imgId = $img->id();
                }
            } else {
                if(isset($_GET["category"]))
                {
                    $category = $_GET["category"];
                    $img = $this->imageDAO->getFirstImageCategory($category);
                    $imgId = $img->id();
                }
                else{
                    $img = $this->imageDAO->getFirstImage();
                    $imgId = $img->id();
                    $category = 'All';
                }
            }

            $state->imgId = $imgId;
            $state->category = $category;

            $this->getSize($state);

            $this->initMenu($state, $data);

            $this->initImageFeature($data, $img);

            $this->initPageLink($state, $data);

            $this->initLikeInfo($state, $data);

            $this->initUser($data);

            $this->initView($data);

            require_once("/view/main.view.php");

        }

        function prev(){
            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            if (isset($_GET["imgId"])) {
                if(isset($_GET["category"]))
                {
                    $imgId = $_GET["imgId"];
                    $category = $_GET["category"];
                    $img = $this->imageDAO->getImageCategory($imgId, $category);
                    $img = $this->imageDAO->getPrevImageCategory($img, $category);
                    $imgId = $img->id();
                }
                else{
                    $imgId = $_GET["imgId"];
                    $category = 'All';
                    $img = $this->imageDAO->getImageCategory($imgId, $category);
                    $img = $this->imageDAO->getPrevImageCategory($img, $category);
                    $imgId = $img->id();
                }
            } else {
                if(isset($_GET["category"]))
                {
                    $category = $_GET["category"];
                    $img = $this->imageDAO->getFirstImageCategory($category);
                    $imgId = $img->id();
                }
                else{
                    $img = $this->imageDAO->getFirstImage();
                    $imgId = $img->id();
                    $category = 'All';
                }
            }

            $state->imgId = $imgId;
            $state->category = $category;

            $this->getSize($state);

            $this->initMenu($state, $data);

            $this->initImageFeature($data, $img);

            $this->initPageLink($state, $data);

            $this->initLikeInfo($state, $data);

            $this->initUser($data);

            $this->initView($data);

            require_once("/view/main.view.php");
        }

        function random(){
            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            $this->getSize($state);

            //L'image à afficher est choisie au hasard
            $img = $this->imageDAO->getRandomImage();

            //Si une catégorie a été sélectionnée on récupère une image demandée de la catégorie demandée
            if(isset($_GET["category"]) && $_GET["category"] !== 'All'){
                $category = $_GET['category'];
                $img = $this->imageDAO->getRandomImageCategory($category);
            }
            else{
                $category = "All";
            }

            $imgId = $img->id();
            $state->imgId = $imgId;
            $state->category = $category;

            $this->initMenu($state, $data);

            $this->initImageFeature($data, $img);

            $this->initPageLink($state, $data);

            $this->initLikeInfo($state, $data);

            $this->initUser($data);

            $this->initView($data);

            require_once("/view/main.view.php");
        }

        function zoom(){

            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            if (isset($_GET["imgId"])) {
                $imgId = $_GET["imgId"];
                $img = $this->imageDAO->getImage($imgId);
            } else {
                $img = $this->imageDAO->getFirstImage();
            }

            $this->getSize($state);

            //Récupération du coefficient de zoom
            if (isset($_GET["zoom"])) {
                $zoom = $_GET["zoom"];
            } else {
                $zoom = 1;
            }

            //La taille de l'image est multiplier par le coefficient caractérisant le zoom
            $state->size = $state->size * $zoom;

            //Si une catégorie a été sélectionnée on récupère le nom de la catégorie
            if(isset($_GET["category"]) && $_GET["category"] !== 'All'){
                $category = $_GET['category'];
            }
            else{
                $category = "All";
            }

            $imgId = $img->id();
            $state->imgId = $imgId;
            $state->category = $category;

            $this->initMenu($state, $data);

            $this->initImageFeature($data, $img);

            $this->initPageLink($state, $data);

            $this->initLikeInfo($state, $data);

            $this->initUser($data);

            $this->initView($data);

            require_once("/view/main.view.php");
        }
    }