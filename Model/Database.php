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

    protected function select_not_assoc($query = "", $params = [])
    {
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

    protected function getImgurImageInfo($imageId)
    {


        $url = "https://api.imgur.com/3/image/{$imageId}.json";

        $ch = curl_init($url);

        $headers = [
            'Authorization: Client-ID ' . IMGUR_CLIENT_ID,
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response, true);
    }



    protected function deleteImageFromImgur($deleteHash)
    {


        $url = "https://api.imgur.com/3/image/$deleteHash";

        $headers = [
            'Authorization: Client-ID ' . IMGUR_CLIENT_ID,
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);


        $result = json_decode($response, true);

        // Check if the upload was successful and return the image URL
        if ($result) {
            return $result;
        } else {
            return null; // Handle error or return default value
        }
    }

    protected function uploadImageToImgur($imagePath)
    {

        $image_source = file_get_contents($imagePath);
        $url = 'https://api.imgur.com/3/image';

        $headers = [
            'Authorization: Client-ID ' . IMGUR_CLIENT_ID,
        ];

        $postData = [
            'image' => base64_encode($image_source),
            "type" => "base64",
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);

        $result = json_decode($response, true);

        $imageData = array(
            'path' => $imagePath,
            'deletehash' => $result['data']['deletehash'],
        );

        $this->writeArrayToJsonFile('uploads/' . $this->extractImgurImageId($result['data']['link']) . '.json', $imageData);

        // Check if the upload was successful and return the image URL
        if ($result && isset($result['data']['link'])) {
            return $result['data']['link'];
        } else {
            return null; // Handle error or return default value
        }
    }

    protected function writeArrayToJsonFile($filename, $data)
    {
        // Convert the array to JSON format
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        // Open the file for writing
        $file = fopen($filename, 'w');

        if ($file) {
            // Write the JSON data to the file
            fwrite($file, $jsonData);

            // Close the file
            fclose($file);

        } else {
            throw new Error("Error opening file {$filename} for writing.");
        }
    }

    protected function extractImgurImageId($imageUrl)
    {
        // Extract the path from the URL
        $urlParts = parse_url($imageUrl);
        $path = isset($urlParts['path']) ? $urlParts['path'] : '';

        // Remove the leading slash if present
        $path = ltrim($path, '/');

        // Extract the file name without extension
        $fileNameWithoutExtension = pathinfo($path, PATHINFO_FILENAME);

        return $fileNameWithoutExtension;
    }

    protected function saveBase64ImageToFile($base64String, $savePath = 'uploads/', )
    {
        // Create the directory if it doesn't exist
        if (!file_exists($savePath)) {
            mkdir($savePath, 0777, true);
        }

        // Extract image format from base64 string
        preg_match('/data:image\/(.*?);/', $base64String, $matches);
        $imageFormat = $matches[1] ?? 'png'; // Default to PNG if format is not found

        // Generate a unique filename
        $filename = uniqid('image_') . '.' . $imageFormat;
        $filePath = $savePath . $filename;

        // Remove data:image/*;base64, from the base64 string
        $base64Data = preg_replace('/data:image\/(.*?);base64,/', '', $base64String);

        // Decode the base64 string
        $imageData = base64_decode($base64Data);

        // Save the image to the specified path
        file_put_contents($filePath, $imageData);

        // Construct the URL with the server IP and "smartwardrobeapi" prefix
        $url = $filePath;


        // Return the complete URL of the saved image
        return $url;
    }

}