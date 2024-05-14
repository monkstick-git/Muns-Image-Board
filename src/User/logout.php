<?php
session_start();
# clear the $_SESSION
session_unset();
# destroy the session
unset($_SESSION);
session_destroy();

header('Location: /');
