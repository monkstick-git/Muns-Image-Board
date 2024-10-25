<?php
$Sitename = Registry::get('settings')['SiteName'];
$MenuItems = [
    'NavBar' => [
        'Home' => Registry::get("RouteTranslations")['HomePage'],
        'Upload' => Registry::get("RouteTranslations")['UploadPage'],
        'My Files' => Registry::get("RouteTranslations")['MyFilesPage'],
        'Gallery' => Registry::get("RouteTranslations")['GalleryPage'],
        "URL Shortener" => Registry::get("RouteTranslations")['ShortNew'],
        // Admin Menu Only visible to admins via some logic below. TODO: Implement this better
    ],
    'RightBar' => [
        'loggedOut' => [
            'Register' => Registry::get("RouteTranslations")['RegisterPage'],
            'Login' => Registry::get("RouteTranslations")['LoginPage']
        ],
        'loggedIn' => [
            'Profile' => Registry::get("RouteTranslations")['ProfilePage'],
            'My Files' => Registry::get("RouteTranslations")['MyFilesPage'],
            'Logout' => Registry::get("RouteTranslations")['LogoutPage']
        ]
    ]
];

$User = Registry::get('User'); // Get the user object from the registry
$Username = $User->username ?? 'Guest'; // Get the username if it exists
$LoggedIn = $User->loggedIn ?? false; // Check if the user is logged in
$IsAdmin = $User->is_admin() ?? false; // Check if the user is an admin
#print_r($User);

$currentUrl = $_SERVER['REQUEST_URI']; // Get the current URL

// Function to add 'active' class if the menu item matches the current page
function isActive($url)
{
    global $currentUrl;
    return ($currentUrl === $url) ? 'active' : '';
}

ob_start();
?>
<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><?= $Sitename ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Left Side (Main Nav) -->
                <ul class="navbar-nav me-auto">
                    <?php foreach ($MenuItems['NavBar'] as $key => $value): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= isActive($value); ?>" href="<?= $value; ?>"><?= $key; ?></a>
                        </li>
                    <?php endforeach; ?>
                    <?php if ($IsAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= isActive(Registry::get("RouteTranslations")['AdminPage']); ?>"
                                href="<?= Registry::get("RouteTranslations")['AdminPage']; ?>">Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- Right Side (Account Nav) -->
                <ul class="navbar-nav ms-auto">
    <?php if (!$LoggedIn): ?>
        <?php foreach ($MenuItems['RightBar']['loggedOut'] as $key => $url): ?>
            <li class="nav-item">
                <a class="nav-link <?= isActive($url); ?>" href="<?= $url; ?>"><?= $key; ?></a>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <?= $Username; ?>
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