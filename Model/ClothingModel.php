<?php
class ClothingModel extends Database
{
    public function list()
    {
        $query = "SELECT * FROM clothing";
        $params = [];

        $clothing_list = $this->select($query, []);
        foreach ($clothing_list as &$clothing) {
            $clothing['features'] = $this->getClothingFeatures($clothing['id']);
        }

        return $clothing_list;
    }

    public function search($q)
    {
        $query = "";
        $params = [];
        return $this->select($query, $params);
    }

    public function get($id)
    {
        $query = "SELECT * FROM clothing WHERE id = ?";
        $params = [$id];
        $result = $this->select($query, $params);
        if (!$result)
            return null;

        $clothing = $result[0];
        $clothing['features'] = $this->getClothingFeatures($id);
        return $clothing;
    }

    public function insert($data)
    {
        if ($data['image']) {
            $imageModel = new ImageGenerationModel();
            $data['image'] = $imageModel->removeBackground($data['image']);
            $imageFilepath = $this->saveBase64ImageToFile($data['image']);
        }

        $query = "INSERT INTO clothing (name, brand, category, size, material, season, sex, image, color, description) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $params = [
            $data['name'],
            $data['brand'],
            $data['category'],
            $data['size'],
            $data['material'],
            $data['season'],
            $data['sex'],
            $imageFilepath,
            $data['color'],
            $data['description'],
        ];
        $insertResult = $this->createUpdateDelete($query, $params);
        $id = $this->connection->insert_id;
        $features = $data['features'];
        foreach ($features as $feature) {
            $query = "INSERT IGNORE INTO feature (name) VALUES (?)";
            $params = [$feature];
            $this->createUpdateDelete($query, $params);
            $query = "INSERT IGNORE INTO clothing_features (clothing, feature) VALUES (?,?)";
            $params = [$id, $feature];
            $this->createUpdateDelete($query, $params);
        }
        return $id;
    }

    public function getDrawer($clothingId)
    {
        $query = "SELECT * FROM clothing_drawer 
        INNER JOIN drawer ON drawer.serial_id = clothing_drawer.drawer
        WHERE clothing = ?";

        $params = [$clothingId];
        return $this->select($query, $params)[0];
    }

    public function delete($id)
    {
        $query = "SELECT * FROM clothing WHERE id = ?";

        $params = [$id];

        $result = $this->select($query, $params);

        if (!$result)
            throw new Exception("Clothing item not found");

        unlink($result[0]['image']);

        $query = "DELETE FROM outfit_components WHERE clothing = ?";
        $params = [$id];

        $query = "DELETE FROM clothing_features WHERE clothing = ?";
        $params = [$id];



        $this->createUpdateDelete($query, $params);

        $query = "DELETE FROM clothing_drawer WHERE clothing = ?";
        $params = [$id];

        $this->createUpdateDelete($query, $params);


        $query = "DELETE FROM clothing WHERE id = ?";
        $params = [$id];

        return $this->createUpdateDelete($query, $params);
    }

    public function getClothingFeatures($id)
    {
        $query = "SELECT feature FROM clothing_features WHERE clothing = ?";
        $params = [$id];
        $features = $this->select_not_assoc($query, $params);
        $feature_array = array();

        foreach ($features as $feature) {
            $feature_array[] = $feature[0];
        }

        return $feature_array;

    }



}