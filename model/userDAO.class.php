<?php
    require_once("user.class.php");

class UserDAO
{

    protected $imageDAO;
    protected $database_path;

    function __construct($path)
    {
        $dsn = 'sqlite:' . $path; // Data source name
        $user = ''; // Utilisateur
        $pass = ''; // Mot de passe
        try {
            $this->db = new PDO($dsn, $user, $pass);
        } catch (PDOException $e) {
            die ("Erreur : " . $e->getMessage());
        }
    }

    public function createUser($login) {
        try {
            $req = $this->db->prepare('INSERT INTO user(login) VALUES (:login)');
            $req->bindParam(':login', $login);
            $req->execute();
        }catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getUserByLogin($login){

        try {
            $reponse = $this->db->prepare("SELECT * FROM user WHERE login=:login");
            $reponse->bindParam(':login', $login);
            $reponse->execute();
        }catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }

        $reponse->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'User');

        $user = $reponse->fetchAll();

        if(count($user) > 0)
            return $user[0];
        else
            return null;
    }

    public function getUserById($id){

        try {
            $reponse = $this->db->prepare("SELECT * FROM user WHERE id=:id");
            $reponse->bindParam(':id', $id);
            $reponse->execute();
        }catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }

        $reponse->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'User');

        $user = $reponse->fetchAll();

        if(count($user) > 0)
            return $user[0];
        else
            return null;
    }

    public function doesUserLikeImage($id_user, $id_image){
        try {
            $reponse = $this->db->prepare("SELECT * FROM like_user_image WHERE id_user=:id_user AND id_image=:id_image");
            $reponse->bindParam(':id_user', $id_user);
            $reponse->bindParam(':id_image', $id_image);
            $reponse->execute();
        }catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }

        $user_image = $reponse->fetchAll();

        if(count($user_image) == 0)
            return false;
        else if(count($user_image) > 0)
            return true;
    }
}