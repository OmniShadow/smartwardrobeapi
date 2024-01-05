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
            $imageUrl = $this->uploadImageToImgur($imageFilepath);
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
            $imageUrl,
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
        $query = "SELECT
        clothing_drawer.clothing, clothing_drawer.drawer, address
        FROM clothing_drawer 
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

        $imageUrl = $result[0]['image'];
        $imageId = $this->extractImgurImageId($imageUrl);
        $imageData = json_decode(file_get_contents('uploads/' . $imageId . '.json'), true);

        $this->deleteImageFromImgur($imageData['deletehash']);
        unlink($imageData['path']);
        unlink('uploads/' . $imageId . '.json');

        $query = "DELETE FROM clothing_features WHERE clothing = ?";
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

    private function getImgurImageInfo($imageId)
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



    private function deleteImageFromImgur($deleteHash)
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

    private function uploadImageToImgur($imagePath)
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

    private function writeArrayToJsonFile($filename, $data)
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

    private function extractImgurImageId($imageUrl)
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

    private function saveBase64ImageToFile($base64String, $savePath = 'uploads/', )
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