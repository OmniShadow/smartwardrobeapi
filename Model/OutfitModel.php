<?php
class OutfitModel extends Database
{


    public function getOutfitComponents($id)
    {
        $query = "SELECT clothing FROM outfit_components WHERE outfit = ?";
        $params = [$id];

        $components = $this->select_not_assoc($query, $params);
        $components_array = array();

        foreach ($components as $component) {
            $components_array[] = $component[0];
        }

        return $components_array;
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
        $query = "";
        $params = [];
        return $this->createUpdateDelete($query, $params);

    }

    public function delete($id)
    {
        $query = "";
        $params = [];
        return $this->createUpdateDelete($query, $params);
    }
}