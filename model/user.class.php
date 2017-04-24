<?php
# Notion d'utilisateur
class User
{
    private $id = 0;
    private $login = "";

    public function __construct($valeurs = [])
    {
        if (!empty($valeurs)) // Si on a spécifié des valeurs, alors on hydrate l'objet.
        {
            $this->hydrate($valeurs);
        }
    }

    public function hydrate($donnees)
    {
        foreach ($donnees as $attribut => $valeur) {
            $methode = 'set' . ucfirst($attribut);

            if (is_callable([$this, $methode])) {
                $this->$methode($valeur);
            }
        }
    }

    # Retourne l'URL de cette image
    public function id()
    {
        return $this->id;
    }

    public function login()
    {
        return $this->login;
    }

    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }
}
?>