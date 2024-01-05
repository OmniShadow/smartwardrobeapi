<?php

class Database
{
    protected $connection = null;
    public function __construct()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);

            if (mysqli_connect_errno()) {
                throw new Exception("Could not connect to database");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function executeStatement($query = "", $params = [])
    {
        try {
            $result = $this->connection->execute_query($query, $params);
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function select($query = "", $params = [])
    {
        try {

            $mysqli_result = $this->executeStatement($query, $params);
            $result = $mysqli_result->fetch_all(MYSQLI_ASSOC);
            $mysqli_result->close();

            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function select_not_assoc($query = "", $params = []){
        try {

            $mysqli_result = $this->executeStatement($query, $params);
            $result = $mysqli_result->fetch_all(MYSQLI_NUM);
            $mysqli_result->close();

            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function createUpdateDelete($query = "", $params = [])
    {
        try {
            $success = $this->executeStatement($query, $params);
            return $success;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}