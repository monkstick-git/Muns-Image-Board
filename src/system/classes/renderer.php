<?php

# This class will render templates from the templates directory.
class render
{
    public $buffer = "";
    public $responseCode = "200"; # Default response code is 200 OK

    public function __construct()
    {
        $this->render_template('Core/header');
    }

    public function setResponseCode($code)
    {
        $this->responseCode = $code;
    }

    public function __destruct()
    {
        http_response_code($this->responseCode);
        $this->render_flush();
    }

    public function render_template($templateName, $arguments = array())
    {
        #echo "rendering template: $templateName\n";
        # Arguments can be used to pass variables to the template if it accepts any.
        include ROOT . '/Views/' . $templateName . '.php';
        $this->buffer = $this->buffer . " " . $template;
    }

    public function render_flush()
    {
        $this->render_template('Core/footer');
        $this->buffer .= "</html>";
        echo $this->buffer;
    }
}
