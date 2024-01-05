<?php
class BookModel extends Database
{

    //handle api connection and requests to GoogleBooks API
    public function getBooksFromApi($q, $limit)
    {
        if($limit > 40)
            $limit = 40;

        /*******QUERY A DATABASE DI GOOGLE*********/
        $q = str_replace(" ", "+", $q);
        if ($limit > 0) {
            $limitString = "&maxResults=$limit";
        } else
            $limitString = "";
        $query = "volumes?q=$q$limitString&fields=items(id,volumeInfo(title,authors,publisher,publishedDate,imageLinks,language,categories))&orderBy:relevance&key=".API_KEY;



        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_URL, BOOK_API_URL . $query);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $results = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $results["items"];

        /*****************************************/
    }



    public function getBook($bookId)
    {
        //example id = K9g_BAAAQBAJ
        $query = "SELECT * FROM libro WHERE id = ?";
        $params = [$bookId];
        return $this->select($query, $params);
    }
    public function getBookFromGoogleApi($id)
    {
        //example id = K9g_BAAAQBAJ
        $query = "volumes/$id";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_URL, BOOK_API_URL . $query);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $result;
    }

    public function getBookOwners($id)
    {

        $query = "SELECT * FROM possesso WHERE libro = ?";
        $params = [$id];
        return $this->select($query, $params);
    }

    public function isBookOwner($userId, $possessoId){
        $query = "SELECT * FROM possesso WHERE id = ? AND proprietario = ?";
        $params = [$possessoId, $userId];
        return $this->select($query, $params);
        
    }

    public function getOwnedBooks($q, $limit)
    {
        $query = "SELECT possesso.id as possesso_id, libro.titolo,libro.editore,libro.copertina,libro.anno,libro.lingua,utente.id as proprietario,libro.id,possesso.descrizione,username,email,avatar,autore,categoria FROM libro 
        INNER JOIN possesso ON possesso.libro = libro.id 
        INNER JOIN utente ON possesso.proprietario = utente.id
        INNER JOIN scrittura ON scrittura.libro = libro.id
        INNER JOIN categoria ON categoria.libro = libro.id
        WHERE (titolo LIKE ? OR scrittura.autore LIKE ? OR categoria.categoria LIKE ?) GROUP BY possesso.id ORDER BY libro.titolo ASC LIMIT ? ";
        $params = ["%$q%","%$q%","%$q%",$limit];
        return $this->select($query, $params);
    }

    public function getOwnedBook($possessoId){
        $query = "SELECT * FROM possesso WHERE id = ?";
        $params = [$possessoId];
        return $this->select($query,$params);
    }

    public function getBookAuthors($bookId){
        $query = "SELECT DISTINCT autore FROM scrittura WHERE libro = ?";
        $params = [$bookId];
        return $this->select($query,$params);
    }

    public function getBookCategories($bookId){
        $query = "SELECT DISTINCT categoria FROM categoria WHERE libro = ?";
        $params = [$bookId];
        return $this->select($query,$params);
    }

    public function addBook($userId, $bookData, $description)
    {
        $insertBookQuery = "INSERT IGNORE INTO libro (id, titolo, editore, copertina, anno, lingua) VALUES (?, ?, ?, ?, ?, ?)";
        $insertBookParams = [$bookData["id"], $bookData["titolo"], $bookData["editore"], $bookData["copertina"], $bookData["anno"], $bookData["lingua"]];

        $insertAutoreQuery = "INSERT IGNORE INTO autore (id) VALUES (?)";
        $insertScritturaQuery = "INSERT IGNORE INTO scrittura (autore,libro) VALUES (?, ?)";
        $this->createUpdateDelete($insertBookQuery, $insertBookParams);

        foreach ($bookData["autori"] as $autore) {

            $insertAutoreParams = [$autore];
            $this->createUpdateDelete($insertAutoreQuery, $insertAutoreParams);


            $insertScritturaParams = [$autore, $bookData["id"]];
            $this->createUpdateDelete($insertScritturaQuery, $insertScritturaParams);
        }

        $insertCategoriaQuery = "INSERT IGNORE INTO categoria (categoria,libro) VALUES (?,?)";
        foreach ($bookData["categorie"] as $categoria) {

            $insertCategoriaParams = [$categoria, $bookData["id"]];
            $this->createUpdateDelete($insertCategoriaQuery, $insertCategoriaParams);
        }

        $insertPossessoQuery = "INSERT INTO possesso (proprietario, libro, descrizione) VALUES (?,?,?)";
        $insertPossessoParams = [$userId, $bookData["id"], $description];
        $this->createUpdateDelete($insertPossessoQuery, $insertPossessoParams);
        return true;

    }

    public function removeBookOwnership($possessoId, $userId)
    {
        $query = "DELETE FROM possesso WHERE id = ? AND proprietario = ?";
        $params = [$possessoId, $userId];
        return $this->createUpdateDelete($query, $params);
    }
}