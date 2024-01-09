<?php
class DrawerModel extends Database
{
    public function listDrawers()
    {
        $query = "SELECT * FROM drawer";
        return $this->select($query, []);
    }

    public function listControllers()
    {
        $query = "SELECT * FROM drawer_controller";
        return $this->select($query, []);
    }

    public function getDrawerData($id)
    {
        $query = "SELECT * FROM drawer WHERE serial_id = ?";
        $params = [$id];
        return $this->select($query, $params);
    }
    public function insertClothingData($data)
    {
        $query = "INSERT INTO clothing_drawer(clothing, drawer) VALUES (?,?)";
        $params = [$data['clothing'], $data['drawer']];
        return $this->createUpdateDelete($query, $params);
    }

    public function updateClothingData($data)
    {
        $query = "UPDATE clothing_drawer SET drawer = ? WHERE clothing = ?";
        $params = [$data['drawer'], $data['clothing']];
        return $this->createUpdateDelete($query, $params);
    }
    public function getControllerData($id)
    {
        if ($id == "") {
            $query = "SELECT * FROM drawer_controller";
            $params = [];
        } else {
            $query = "SELECT * FROM drawer_controller WHERE id = ?";
            $params = [$id];
        }

        return $this->select($query, $params);
    }

    public function insertDrawerData($data)
    {
        $query = "INSERT IGNORE INTO drawer (serial_id, address, controller,last_operation) VALUES (?,?,?,?)";
        $params = [$data['serial_id'], $data['address'], $data['controller'], $data['last_operation']];
        return $this->createUpdateDelete($query, $params);

    }

    public function updateDrawerName($data)
    {
        $query = "UPDATE drawer SET name = ? WHERE serial_id = ?";
        $params = [$data['name'], $data['serial_id']];
        return $this->createUpdateDelete($query, $params);

    }


    public function deleteDrawerData($data)
    {
        $id = $data['serial_id'];
        $query = "DELETE FROM drawer_contents WHERE drawer = ?;
        DELETE FROM drawer WHERE serial_id = ?";
        $params = [$id, $id];
        return $this->createUpdateDelete($query, $params);

    }

    public function updateDrawerStatus($data)
    {
        $query = "UPDATE drawer SET status = ?, address = ?, last_operation = ? WHERE address = ? AND controller = ?";
        $params = [$data['status'], $data['address'], $data['last_operation'], $data['address'], $data['controller'],];
        return $this->createUpdateDelete($query, $params);

    }

    public function insertControllerData($data)
    {
        $query = "INSERT INTO drawer_controller (local_ip) VALUES (?)";
        $params = [
            $data['local_ip'],
        ];
        $response = array();
        $response["status"] = $this->createUpdateDelete($query, $params);
        $response["id"] = $this->connection->insert_id;
        return $response;
    }
    public function updateControllerData($data)
    {
        $query = "UPDATE  drawer_controller (local_ip) VALUES (?) WHERE id = ?";
        $params = [
            $data['local_ip'],
            $data['id'],
        ];
        $response = array();
        $response["status"] = $this->createUpdateDelete($query, $params);

        return $response;
    }



}