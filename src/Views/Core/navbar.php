<?php

$MenuItems = [
    'NavBar' => [
        'Home' => '/',
        'Upload' => '/File/Upload',
        'My Files' => '/User/Files',
        'Gallery' => '/Home/Gallery'
    ],
    'RightBar' => [
        'loggedOut' => [
            'Register' => '/User/register',
            'Login' => '/User/login'
        ],
        'loggedIn' => [
            'Profile' => '/User/profile',
            'My Files' => '/User/Files',
            'Logout' => '/User/logout'
        ]
    ]
];

ob_start();
?>
<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MunBoard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Left Side (Main Nav) -->
                <ul class="navbar-nav me-auto">
                    <?php foreach ($MenuItems['NavBar'] as $key => $value): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $value; ?>"><?= $key; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Right Side (Account Nav) -->
                <ul class="navbar-nav ms-auto">
                    <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
                        <?php foreach ($MenuItems['RightBar']['loggedOut'] as $key => $url): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $url; ?>"><?= $key; ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $GLOBALS['User']->username; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <?php foreach ($MenuItems['RightBar']['loggedIn'] as $key => $url): ?>
                                    <li><a class="dropdown-item" href="<?= $url; ?>"><?= $key; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main role="main" class="main">

    <?php
    $template = ob_get_contents();
    ob_end_clean();
    ?>