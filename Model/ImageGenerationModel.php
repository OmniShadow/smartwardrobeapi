<?php
class ImageGenerationModel
{
    public function generate($prompt)
    {
        $endpoint = DB_HOST . ":" . IMAGE_GEN_PORT . "/sdapi/v1/txt2img";

        // Set the POST data
        $postData = array(
            "prompt" => $prompt . ' solid color background',
            "negative_prompt" => 'BadDream',
            "seed" => -1,
            "batch_size" => 1,
            "steps" => 20,
            "cfg_scale" => 20,
            "width" => 512,
            "height" => 768,
            "send_images" => true,
            "save_images" => false,
        );

        // Convert the data to JSON format
        $postDataJson = json_encode($postData);

        // Initialize cURL session
        $ch = curl_init($endpoint);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // Find the position of the base64-encoded image data
        $startPosition = strpos($response, '"images": ["') + strlen('"images": ["');
        $endPosition = strpos($response, '"', $startPosition);

        // Extract the base64-encoded image data
        $base64ImageData = substr($response, $startPosition, $endPosition - $startPosition);


        return $base64ImageData;

    }

    public function removeBackground($base64Image)
    {
        $endpoint = DB_HOST . ":" . IMAGE_GEN_PORT . "/rembg";

        $postDataJson = '
        {
            "input_image": "' . $base64Image . '",
            "model": "u2net",
            "return_mask": false,
            "alpha_matting": false,
            "alpha_matting_foreground_threshold": 240,
            "alpha_matting_background_threshold": 10,
            "alpha_matting_erode_size": 10
          }';
        // Initialize cURL session
        $ch = curl_init($endpoint);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // Find the position of the base64-encoded image data
        $startPosition = strpos($response, '"image": "') + strlen('"image": "');
        $endPosition = strpos($response, '"', $startPosition);

        // Extract the base64-encoded image data
        $base64ImageData = substr($response, $startPosition, $endPosition - $startPosition);


        return $base64ImageData;

    }

    public function getDescription($base64Image)
    {
        $endpoint = DB_HOST . ":" . IMAGE_GEN_PORT . "/interrogator/prompt";

        $postDataJson = '
        {
            "image" :' . '"' . $base64Image . '"' . ',
            "clip_model_name" :"ViT-L-14/openai",
            "mode" : "fast"
        }';
        // Initialize cURL session
        $ch = curl_init($endpoint);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        $jsonMap = json_decode($response, true);

        return $jsonMap['prompt'];
    }

    public function getSuggestion($description)
    {
        $endpoint = DB_HOST . ":" . LANGUAGE_MODEL_PORT . "/v1/chat/completions";

        $postDataJson = '
        {
            "messages": [
              {"role": "user", "content": "' . $description . '"}
            ],
            "mode": "instruct",
            "instruction_template": "01ClothingItemJson",
            "temperature": 1.31,
            "top_p": 0.14,
            "repetition_penalty": 1.17,
            "top_k": 49
          }';
        // Initialize cURL session
        $ch = curl_init($endpoint);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        $jsonMap = json_decode($response, true);

        return $jsonMap['choices'][0]['message']['content'];

    }

}