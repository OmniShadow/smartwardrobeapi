<?php
class OutfitModel extends Database
{


    public function getOutfitComponents($id)
    {
        $query = "SELECT id,name,brand,category,size,material,season,sex,image,color,description,drawer FROM outfit_components
        INNER JOIN clothing ON outfit_components.clothing = clothing.id
        WHERE outfit = ?";
        $params = [$id];

        $components = $this->select($query, $params);


        return $components;
    }

    public function list()
    {
        $query = "SELECT * FROM outfit";
        $params = [];
        $outfits_list = $this->select($query, []);
        foreach ($outfits_list as &$outfit) {
            $outfit['components'] = $this->getOutfitComponents($outfit['id']);
        }

        return $outfits_list;
    }

    public function getSchema()
    {
        $query = "SHOW COLUMNS FROM outfit";
        $params = [];
        $fields = $this->select($query, []);
        $schema = array();
        foreach ($fields as $field) {
            $schema[$field['Field']] = '';
        }
        $schema['components'] = array();
        return $schema;

    }

    public function search($q)
    {
        $query = "";
        $params = [];
        return $this->select($query, $params);
    }

    public function get($id)
    {
        $query = "SELECT * FROM outfit WHERE id = ?";
        $params = [$id];

        $result = $this->select($query, $params);
        if (!$result)
            return null;
        $outfit = $result[0];
        $outfit['components'] = $this->getOutfitComponents($id);
        return $outfit;
    }

    public function insert($data)
    {
        try {
            $imageFilepath = null;
            if ($data['image']) {
                $imageFilepath = $this->saveBase64ImageToFile($data['image']);
            }
            $query = "INSERT 
            INTO outfit (name, occasion, weather, season, style, description, image)
            VALUES (?,?,?,?,?,?,?)";
            $params = [$data['name'], $data['occasion'], $data['weather'], $data['season'], $data['style'], $data['description'], $imageFilepath];
            $this->createUpdateDelete($query, $params);
            $id = $this->connection->insert_id;
            $clothingItems = $data['components'];
            foreach ($clothingItems as $clothingItem) {
                $query = "INSERT IGNORE INTO outfit_components (outfit, clothing) VALUES (? ,?)";
                $params = [$id, $clothingItem];
                $this->createUpdateDelete($query, $params);
            }
        } catch (Exception $e) {
            return false;
        }
        return true;

    }

    public function delete($data)
    {
        $query = "SELECT image FROM outfit WHERE id = ?";
        $params = [$data['id']];
        $result = $this->select($query, $params);

        $query = "DELETE FROM outfit_components WHERE outfit = ?";
        $params = [$data['id']];

        $this->createUpdateDelete($query, $params);
        $query = "DELETE FROM outfit WHERE id = ?";
        $params = [$data['id']];

        unlink($result[0]['image']);

        $this->createUpdateDelete($query, $params);
    }
}
