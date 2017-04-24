<?php


    require_once("./model/user.class.php");
    require_once("./model/userDAO.class.php");

    $config = parse_ini_file('./config/config.ini');
    $database_path = $config['database_path'];
    $data_path = $config['data_path'];
    $userDAO = new UserDAO($database_path);

//    $userDAO->createUser("login1");
//    $user = $userDAO->getUserByLogin("login1");
//    $user = $userDAO->getUserById(1);
//    var_dump($user->login());

    var_dump($userDAO->doesUserLikeImage(1,1));

