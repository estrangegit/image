<?php
  
  # Notion d'image
  class Image {
    private $id=0;
    private $path="";
    private $category="";
    private $comment="";
    private $nbLike;


    public function __construct($valeurs = [])
    {
      if (!empty($valeurs)) // Si on a spécifié des valeurs, alors on hydrate l'objet.
      {
          $this->hydrate($valeurs);
      }
    }

    public function hydrate($donnees)
    {
      foreach ($donnees as $attribut => $valeur)
      {
          $methode = 'set'.ucfirst($attribut);

          if (is_callable([$this, $methode]))
          {
              $this->$methode($valeur);
          }
      }
    }

    # Retourne l'URL de cette image
    public function id() {
      return $this->id;
    }
    public function path() {
      return $this->path;
    }
    public function category(){
        return $this->category;
    }
    public function comment(){
        return $this->comment;
    }
    public function nbLike(){
        return $this->nbLike;
    }

    public function setId($id){
        $this->id = (int)$id;
    }
    public function setCategory($category)
    {
        $this->category = $category;
    }
    public function setPath($path)
    {
        $this->path = $path;
    }
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
    public function setNbLike($nbLike)
    {
        $this->nbLike = $nbLike;
    }
  }
?>