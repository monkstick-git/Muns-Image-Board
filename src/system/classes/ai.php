<?php

# Generic AI class to be used for AI logic. This class will be extended by the AI classes.
# Authentication, cleanup, execution etc will be done in this class.
# Class will create their own prompts and logic by extending this class.

class Ai
{

    public $prompt;
    public $systemPrompt;
    public $response;
    public $payload;
    public $url;
    public $model;


    public function __construct()
    {
        $url = Registry::get('settings')['ai']['url'];
        $model = Registry::get('settings')['ai']['model'];
        $this->url = $url;
        $this->model = $model;
    }

    public function setPrompt($prompt)
    {
        $this->prompt = $prompt;
    }

    public function setSystemPrompt($prompt)
    {
        $this->systemPrompt = $prompt;
    }

    public function execute()
    {
        // Send request to Ollama using PHP cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $raw_response = curl_exec($ch);
        if (curl_error($ch)) {
            return false;
        }
        curl_close($ch);

        // Extract response
        $response = json_decode($raw_response)->message->content;

        # Check if the response is empty, null or false
        if (empty($response) || is_null($response) || $response == false) {
            # If the response is empty, null or false, return false
            # This allows the calling function to handle the error
            # and not take the site down.
            # Log the error
            Registry::get('logger')->log('AI response is empty, null or false');
            Registry::get('logger')->log(print_r($raw_response));
            return false;
        } else {
            $this->response = $response;
            return true;
        }
    }

    public function createPayload()
    {
        $payload = array(
            'model' => $this->model,
            'messages' => array(
                array('role' => 'system', 'content' => $this->systemPrompt),
                array('role' => 'user', 'content' => $this->prompt)
            ),
            'stream' => false
        );

        $json_payload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $this->payload = $json_payload;
    }


}