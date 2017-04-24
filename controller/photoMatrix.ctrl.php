<?php

    require_once("./model/image.class.php");
    require_once("./model/imageDAO.class.php");
    require_once("./model/data.class.php");
    require_once("./model/state.class.php");


    class PhotoMatrix{

        protected $imageDAO;
        protected $database_path;
        protected $data_path;

        function __construct()
        {
            $config = parse_ini_file('./config/config.ini');
            $this->database_path = $config['database_path'];
            $this->data_path = $config['data_path'];
            $this->imageDAO = new ImageDAO($this->database_path);
        }

        function queryLink($controller, $action, $nbImg , $category, $imgId){
            $query = array(
                'controller' => $controller,
                'action' => $action,
                'nbImg' => $nbImg,
                'category' => $category,
                'imgId' => $imgId,
            );

            return "index.php?".http_build_query($query);
        }


        function initMenu($state, $data){

            $data->menu['Home'] = $this->queryLink("home.ctrl", "index", null, null, null);
            $data->menu['A propos'] = $this->queryLink("home.ctrl", "apropos", null, null, null);
            $data->menu['First'] = $this->queryLink("photoMatrix.ctrl", "first", $state->nbImg, $state->category, $state->imgId);
            $data->menu['Random'] = $this->queryLink("photoMatrix.ctrl", "random", $state->nbImg, $state->category, null);
            $data->menu['More'] = $this->queryLink("photoMatrix.ctrl", "more", $state->nbImg, $state->category, $state->imgId);
            $data->menu['Less'] = $this->queryLink("photoMatrix.ctrl", "less", $state->nbImg, $state->category, $state->imgId);
        }

        function initImgList(Image $img, $state, $data){
            //Construction de la liste d'images à afficher
            $imgList= $this->imageDAO->getImageListCategory($img, $state->category, $state->nbImg);

            //Construction du tableau de données à passer à la vue.
            //Pour chaque image $data->imgMatrixURL[0] est l'URL de l'image à afficher et $data->imgMatrixURL[1] le lien vers l'affichage de la photo en vue seule
            foreach ($imgList as $i) {
                $iId=$i->id();
                $data->imgMatrixURL[] = array($this->data_path.$i->path(), $this->queryLink("photo.ctrl", "index", null, $state->category, $iId));
            }

            $state->size = 480 / sqrt($state->nbImg);
        }

        function initPageLink($state, $data){
            $data->nextLink = $this->queryLink("photoMatrix.ctrl", "next", $state->nbImg, $state->category, $state->imgId);
            $data->prevLink = $this->queryLink("photoMatrix.ctrl", "prev", $state->nbImg, $state->category, $state->imgId);
        }

        function getNbImg($state){
            //Récupération du nombre d'images à afficher
            if (isset($_GET["nbImg"])) {
                $state->nbImg = $_GET["nbImg"];
            } else {
                $state->nbImg = 2;
            }
        }

        function getImgIdCategory($state)
        {
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

            return $img;
        }

        function initView($data){
            $data->content = "photoMatrix.view.php";

            $data->listCategories = $this->imageDAO->getListCategories();
            $data->disconnect = "disconnect.view.php";
            $data->formCategory ="formCategory.view.php";
        }

        function initUser($data){
            $data->login = $_SESSION['login'];
        }

        function index(){
            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            $this->getNbImg($state);

            //Initialisation de l'id courant et de la categorie
            $img = $this->getImgIdCategory($state);

            //Initialisation de la liste des images à afficher
            $this->initImgList($img, $state, $data);

            //Initalisation du menu
            $this->initMenu($state, $data);

            //Initialisation des liens de la page
            $this->initPageLink($state, $data);

            //Initialisation des informations concernant l'utilisateur
            $this->initUser($data);

            //Initialisation de la vue
            $this->initView($data);

            require_once("/view/main.view.php");
        }

        function first(){
            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            $this->getNbImg($state);

            //La première image à afficher est la première image du lot fourni
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

            $state->imgId = $imgId;
            $state->category = $category;

            //Initialisation de la liste des images à afficher
            $this->initImgList($img, $state, $data);

            //Initalisation du menu
            $this->initMenu($state, $data);

            //Initialisation des liens de la page
            $this->initPageLink($state, $data);

            //Initialisation des informations concernant l'utilisateur
            $this->initUser($data);

            //Initialisation de la vue
            $this->initView($data);

            require_once("/view/main.view.php");

        }

        function next(){
            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            $this->getNbImg($state);

            $img = $this->getImgIdCategory($state);

            //La première image à afficher est celle qui est $data->nbImg images plus loin que celle reçue en paramètre
            $img = $this->imageDAO->jumpToImageCategory($img, $state->nbImg, $state->category);
            $state->imgId = $img->id();

            //Initialisation de la liste des images à afficher
            $this->initImgList($img, $state, $data);

            //Initalisation du menu
            $this->initMenu($state, $data);

            //Initialisation des liens de la page
            $this->initPageLink($state, $data);

            //Initialisation des informations concernant l'utilisateur
            $this->initUser($data);

            //Initialisation de la vue
            $this->initView($data);

            require_once("/view/main.view.php");
        }

        function prev(){
            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            $this->getNbImg($state);

            $img = $this->getImgIdCategory($state);

            //La première image à afficher est celle qui est $data->nbImg images avant celle reçue en paramètre
            $img = $this->imageDAO->jumpToImageCategory($img, (-1)*($state->nbImg), $state->category);
            $state->imgId = $img->id();

            //Initialisation de la liste des images à afficher
            $this->initImgList($img, $state, $data);

            //Initalisation du menu
            $this->initMenu($state, $data);

            //Initialisation des liens de la page
            $this->initPageLink($state, $data);

            //Initialisation des informations concernant l'utilisateur
            $this->initUser($data);

            //Initialisation de la vue
            $this->initView($data);

            require_once("/view/main.view.php");
        }

        function random(){
            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            $this->getNbImg($state);

            $img = $this->imageDAO->getRandomImage();

            //Si une catégorie a été sélectionnée on récupère une image demandée de la catégorie demandée
            if(isset($_GET["category"]) && $_GET["category"] !== 'All'){
                $category = $_GET['category'];
                $img = $this->imageDAO->getRandomImageCategory($category);
            }
            else{
                $category = "All";
            }

            $state->imgId = $img->id();
            $state->category = $category;

            //Initialisation de la liste des images à afficher
            $this->initImgList($img, $state, $data);

            //Initalisation du menu
            $this->initMenu($state, $data);

            //Initialisation des liens de la page
            $this->initPageLink($state, $data);

            //Initialisation des informations concernant l'utilisateur
            $this->initUser($data);

            //Initialisation de la vue
            $this->initView($data);

            require_once("/view/main.view.php");
        }

        function more(){

            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            $this->getNbImg($state);

            $img = $this->getImgIdCategory($state);

            //Calcul du nouveau nombre d'images à afficher
            $newNbImg = ($state->nbImg)*2;

            if($newNbImg > $this->imageDAO->getSizeCategory($state->category))
            {
                $newNbImg = $this->imageDAO->getSizeCategory($state->category);
            }
            $state->nbImg = $newNbImg;

            //Initialisation de la liste des images à afficher
            $this->initImgList($img, $state, $data);

            //Initalisation du menu
            $this->initMenu($state, $data);

            //Initialisation des liens de la page
            $this->initPageLink($state, $data);

            //Initialisation des informations concernant l'utilisateur
            $this->initUser($data);

            //Initialisation de la vue
            $this->initView($data);

            require_once("/view/main.view.php");
        }

        function less(){
            global $data;
            global $state;

            $data = new Data();
            $state = new State();

            $this->getNbImg($state);

            $img = $this->getImgIdCategory($state);

            //Calcul dun nouveau nombre d'images à afficher
            $newNbImg = ceil(($state->nbImg)/2);

            $state->nbImg = $newNbImg;

            //Initialisation de la liste des images à afficher
            $this->initImgList($img, $state, $data);

            //Initalisation du menu
            $this->initMenu($state, $data);

            //Initialisation des liens de la page
            $this->initPageLink($state, $data);

            //Initialisation des informations concernant l'utilisateur
            $this->initUser($data);

            //Initialisation de la vue
            $this->initView($data);

            require_once("/view/main.view.php");
        }
    }