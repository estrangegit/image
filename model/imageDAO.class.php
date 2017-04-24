<?php
	require_once("image.class.php");

	class ImageDAO {

        function __construct($path) {
            $dsn = 'sqlite:'.$path; // Data source name
            $user= ''; // Utilisateur
            $pass= ''; // Mot de passe
            try {
                $this->db = new PDO($dsn, $user, $pass);
            } catch (PDOException $e) {
                die ("Erreur : ".$e->getMessage());
            }
        }
		
		# Retourne le nombre d'images référencées dans le DAO
		function size() {
            $s = $this->db->query('SELECT COUNT(*) FROM image ORDER BY id');
            if ($s) {
                $result = $s->fetchAll();
                return $result[0][0];
            } else {
                print "Error in size<br/>";
                $err = $this->db->errorInfo();
                print $err[2] . "<br/>";
            }
		}

		# Retourne le nombre d'images d'une catégorie
        function getSizeCategory($category){
            if($category == 'All')
            {
                return $this->size();
            }
            else {
                try {
                    $s = $this->db->prepare('SELECT COUNT(*) FROM image WHERE category=:category');
                    $s->bindParam(':category', $category);
                    $s->execute();
                } catch (Exception $e) {
                    die('Erreur: ' . $e->getMessage());
                }
                $result = $s->fetchAll();
                return $result[0][0];
            }
        }

    function getSizeCategoryFromImageId($id, $category){
        if($category == 'All')
        {
            try {
                $s = $this->db->prepare('SELECT COUNT(*) FROM image WHERE id >=:id');
                $s->bindParam(':id', $id);
                $s->execute();
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
            $result = $s->fetchAll();
            return $result[0][0];
        }
        else {
            try {
                $s = $this->db->prepare('SELECT COUNT(*) FROM image WHERE category=:category AND id >= :id');
                $s->bindParam(':category', $category);
                $s->bindParam(':id', $id);
                $s->execute();
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
            $result = $s->fetchAll();
            return $result[0][0];
        }
    }

		#Retourne toutes les catégories présentes dans la base de données
        function getListCategories(){
            $s = $this->db->query('SELECT DISTINCT(category) FROM image ORDER BY category');
            if ($s) {
                $categories = $s->fetchAll(PDO::FETCH_COLUMN, 0);
                return $categories;
            } else {
                print "Error in size<br/>";
                $err = $this->db->errorInfo();
                print $err[2] . "<br/>";
            }
        }

        #Retourne toutes les images d'une catégorie
        function getListImagesByCategory($category){
            try{
                $s = $this->db->prepare('SELECT * FROM image WHERE category=:category ORDER BY id');
                $s->bindParam(':category', $category);
                $s->execute();
            }
            catch(Exception $e)
            {
                die('Erreur: '.$e->getMessage());
            }
            $s->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'image');
            $images = $s->fetchAll();
            return $images;
        }

        function getImage($id)
        {
            $s = $this->db->query('SELECT * FROM image WHERE id=' . $id);
            if ($s) {
                $s->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'image');
                $images = $s->fetchAll();
                return $images[0];
            } else {
                print "Error in getImage. id=" . $id . "<br/>";
                $err = $this->db->errorInfo();
                print $err[2] . "<br/>";
            }
        }

        #Retourne l'image d'identifiant $id si elle est de la categorie $categorie, la première image de la catégorie sinon
        function getImageCategory($id, $category)
        {
            if($category == 'All')
            {
                return $this->getImage($id);
            }
            else{
                $imgTest = $this->getImage($id);
                if($imgTest->category() == $category)
                    return $imgTest;
                else{
                    return $this->getFirstImageCategory($category);
                }
            }
        }


        # Retourne la première image d'une catégorie
        function getFirstImageCategory($category){

            if($category != 'All'){
                $imageListCategory = $this->getListImagesByCategory($category);
                return $imageListCategory[0];
            }
            else{
                return $this->getFirstImage();
            }
        }
		
		# Retourne une image au hazard
		function getRandomImage() {
            $idMin = 1;
            $idMax = $this->size();

            $newId = rand($idMin, $idMax);

            return $this->getImage($newId);
		}

		# Retourne une image au hasard dans une catégorie
        function getRandomImageCategory($category)
        {
            $imageListCategory = $this->getListImagesByCategory($category);
            $sizeCategory = count($imageListCategory);

            $indexMin = 0;
            $indexMax = $sizeCategory - 1;

            $newIndex = rand($indexMin, $indexMax);

            return $imageListCategory[$newIndex];
        }
		
		# Retourne l'objet de la premiere image
		function getFirstImage() {
			return $this->getImage(1);
		}
		
		# Retourne l'image suivante d'une image
		function getNextImage(Image $img) {
			$id = $img->id();
			if ($id < $this->size()) {
				$img = $this->getImage($id+1);
			}
			return $img;
		}

		# Retourne l'image suivante d'une catégorie donnée
        function getNextImageCategory(Image $image, $category)
        {
            if($category == "All")
            {
                return $this->getNextImage($image);
            }
            else {
                try {
                    $id = $image->id();
                    $s = $this->db->prepare('SELECT * FROM image WHERE id>:id AND category=:category ORDER BY id');
                    $s->bindParam(':id', $id);
                    $s->bindParam(':category', $category);
                    $s->execute();
                } catch (Exception $e) {
                    die('Erreur: ' . $e->getMessage());
                }
                $s->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'image');
                $images = $s->fetchAll();
                if (count($images) != 0) {
                    return $images[0];
                } else
                    return $image;
            }
        }

		# Retourne l'image précédente d'une image
		function getPrevImage(Image $img) {
            $id = $img->id();
            if ($id > 1) {
                $img = $this->getImage($id-1);
            }
            return $img;
		}

		# Retourne l'image précédente d'une image en restant dans la même catégorie
        function getPrevImageCategory(Image $image, $category)
        {
            if($category == "All")
            {
                return $this->getPrevImage($image);
            }
            else{
                try{
                    $id = $image->id();
                    $s = $this->db->prepare('SELECT * FROM image WHERE id<:id AND category=:category ORDER BY id');
                    $s->bindParam(':id', $id);
                    $s->bindParam(':category', $category);
                    $s->execute();
                }
                catch(Exception $e)
                {
                    die('Erreur: '.$e->getMessage());
                }
                $s->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'image');
                $images = $s->fetchAll();
                if(count($images) != 0)
                    return $images[count($images)-1];
                else
                    return $image;
            }
        }

        # saute en avant ou en arrière de $nb images
		# Retourne la nouvelle image
		function jumpToImage(Image $img,$nb) {
            $id = $img->id();
            $newId = $id + $nb;

            if ($newId < 1){
                $newId = 1;
            }
            if ($newId > $this->size())
            {
                $newId = $id;
            }

            $img = $this->getImage($newId);

            return $img;
		}

        # saute en avant ou en arrière de $nb images
        # Retourne la nouvelle image
        function jumpToImageCategory(Image $img,$nb, $category){
            if($category == 'All')
            {
                return $this->jumpToImage($img, $nb);
            }
            else {
                $img = $this->getImageCategory($img->id(), $category);
                $imageList = $this->getListImagesByCategory($category);

                foreach ($imageList as $key => $image) {
                    if ($image->id() == $img->id()) {
                        $indexImg = $key;
                        break;
                    }
                }

                $newIndex = $indexImg + $nb;

                if ($newIndex < 0)
                    $newIndex = 0;
                if ($newIndex > (count($imageList) - 1))
                    $newIndex = $indexImg;

                return $imageList[$newIndex];
            }
        }

		# Retourne la liste des images consécutives à partir d'une image
		function getImageList(Image $img,$nb) {
			# Verifie que le nombre d'image est non nul
			if (!$nb > 0) {
				debug_print_backtrace();
				trigger_error("Erreur dans ImageDAO.getImageList: nombre d'images nul");
			}

            try{
                $id = $img->id();
                $s = $this->db->prepare('SELECT * FROM image WHERE id>=:id ORDER BY id');
                $s->bindParam(':id', $id);
                $s->execute();
            }
            catch(Exception $e)
            {
                die('Erreur: '.$e->getMessage());
            }
            $s->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'image');
            $imgListFromIdAsc = $s->fetchAll();

            try{
                $id = $img->id();
                $s = $this->db->prepare('SELECT * FROM image WHERE id < :id ORDER BY id DESC');
                $s->bindParam(':id', $id);
                $s->execute();
            }
            catch(Exception $e)
            {
                die('Erreur: '.$e->getMessage());
            }
            $s->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'image');
            $imgListFromIdDesc = $s->fetchAll();

            $index = 0;

            while($index < $nb && $index < count($imgListFromIdAsc)){
                $res[] = $imgListFromIdAsc[$index];
                $index = $index + 1;
            }

            $diff = $nb-$index;

            $index = 0;
            while($index < $diff && $index < count($imgListFromIdDesc))
            {
                array_unshift($res, $imgListFromIdDesc[$index]);
                $index = $index + 1;
            }
            return $res;
		}

		#Retourne la liste des images consécutives d'une même catégorie à partie d'une image
        function getImageListCategory(Image $img, $category, $nb)
        {
            if (!$nb > 0) {
                debug_print_backtrace();
                trigger_error("Erreur dans ImageDAO.getImageList: nombre d'images nul");
            }
            if ($category == 'All') {
                return $this->getImageList($img, $nb);
            } else {
                $img = $this->getImageCategory($img->id(), $category);
                $imageList = $this->getListImagesByCategory($category);

                foreach ($imageList as $key=>$image) {
                    if($image->id() == $img->id())
                        $minKey = $key;
                }

                foreach ($imageList as $key=>$image) {
                    if ($image->id() >= $img->id()) {
                        $resTemp[] = $image;
                    }
                }

                $index = 0;
                while ($index < $nb && $index < count($resTemp)) {
                    $res[] = $resTemp[$index];
                    $index++;
                }

                $minKey = $minKey - 1;
                while($minKey >= 0 && count($res) <= ($nb-1))
                {
                    array_unshift($res,$imageList[$minKey]);
                    $minKey = $minKey - 1;
                }

                return $res;
            }
        }

        #Permet de mettre à jour les données d'une image
        function updateCategoryComment(Image $img){
            $id = $img->id();
            $comment = $img->comment();
            $category = $img->category();

            try{
                $s = $this->db->prepare('UPDATE image set comment=:comment, category=:category WHERE id=:id');
                $s->bindParam(':id', $id);
                $s->bindParam(':comment', $comment);
                $s->bindParam(':category', $category);
                $s->execute();
            }
            catch(Exception $e)
            {
                die('Erreur: '.$e->getMessage());
            }
        }

        function updateNbLike(Image $img){
            $id = $img->id();
            $nbLike = $img->nbLike();
            try{
                $s = $this->db->prepare('UPDATE image set nbLike=:nbLike WHERE id=:id');
                $s->bindParam(':id', $id);
                $s->bindParam(':nbLike', $nbLike);
                $s->execute();
            }
            catch(Exception $e)
            {
                die('Erreur: '.$e->getMessage());
            }
        }

        #Permet d'obtenir le nombre de like par photo
        function getNbLikeByImageId($image_id){
            $image = $this->getImage($image_id);
            return $image->nbLike();
        }

        function addNbLikeByImageId($image_id){
            $image = $this->getImage($image_id);
            $image->setNbLike($image->nbLike() + 1);
            $this->updateNbLike($image);
        }

        function substractNbLikeByImageId($image_id){
            $image = $this->getImage($image_id);

            if($image->nbLike() > 0)
                $image->setNbLike($image->nbLike() - 1);

            $this->updateNbLike($image);
        }

        public function addUserImageLike($id_user, $id_image){
            try {
                $req = $this->db->prepare('INSERT INTO like_user_image(id_user, id_image) VALUES (:id_user, :id_image)');
                $req->bindParam(':id_user', $id_user);
                $req->bindParam(':id_image', $id_image);
                $req->execute();
            }catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
            $this->addNbLikeByImageId($id_image);
        }

        public function deleteUserImageLike($id_user, $id_image){
            try {
                $req = $this->db->prepare('DELETE FROM like_user_image WHERE id_user=:id_user AND id_image=:id_image');
                $req->bindParam(':id_user', $id_user);
                $req->bindParam(':id_image', $id_image);
                $req->execute();
            }catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
            $this->substractNbLikeByImageId($id_image);
        }
	}
	?>