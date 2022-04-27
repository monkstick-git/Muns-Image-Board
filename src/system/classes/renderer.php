<?php

# This class will render templates from the templates directory.
class render
{
    public $buffer = "";

    public function __construct()
    {
        $this->render_template('header');
    }

    public function __destruct()
    {
        $this->render_flush();
    }

    public function render_template($templateName, $arguments = array())
    {
        #echo "rendering template: $templateName\n";
        # Arguments can be used to pass variables to the template if it accepts any.
        include ROOT . '/views/' . $templateName . '.php';
        $this->buffer = $this->buffer . " " . $template;
    }

    public function render_flush()
    {
        $this->buffer .= "
        <script>
        (function(w,d,t,u,n,a,m){w['MauticTrackingObject']=n;
            w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
            m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
        })(window,document,'script','http://munboard.localhost:8880/mtc.js','mt');
    
        mt('send', 'pageview');
    </script>
        ";
        $this->buffer .= "</body></html>";
        echo $this->buffer;
    }
}
