<?php
$Sitename = Registry::get('settings')['SiteName'];
$MenuItems = [
    'NavBar' => [
        'Home' => Registry::get("RouteTranslations")['HomePage'],
        'Content' => [ // New category for content-related links
            'Upload' => Registry::get("RouteTranslations")['UploadPage'],
            'Gallery' => Registry::get("RouteTranslations")['GalleryPage'],
        ],
        'Account' => [ // New category for account-related links (will be dynamic based on login status)
            // Items will be added below based on login status
        ],
        "URL Shortener" => Registry::get("RouteTranslations")['ShortNew'],
        // Admin Menu Only visible to admins via some logic below. TODO: Implement this better
    ],
    'RightBar' => [ // This seems redundant now, consider merging with 'Account' category above
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

$User = Registry::get('User');
$Username = $User->username ?? 'Guest';
$LoggedIn = $User->loggedIn ?? false;
$IsAdmin = $User->is_admin() ?? false;

// Populate 'Account' category based on login status
if ($LoggedIn) {
    $MenuItems['NavBar']['Account'] = $MenuItems['RightBar']['loggedIn'];
} else {
    $MenuItems['NavBar']['Account'] = $MenuItems['RightBar']['loggedOut'];
}

$currentUrl = $_SERVER['REQUEST_URI'];

function isActive($url) {
    global $currentUrl;
    return ($currentUrl === $url) ? 'active' : '';
}

ob_start();
?>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm"> 
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= Registry::get("RouteTranslations")['HomePage'] ?>">
                <?= $Sitename ?> 
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php foreach ($MenuItems['NavBar'] as $key => $value): ?>
                        <?php if (is_array($value)): ?> 
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="<?= $key ?>Dropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= $key ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="<?= $key ?>Dropdown">
                                    <?php foreach ($value as $subKey => $subValue): ?>
                                        <li><a class="dropdown-item <?= isActive($subValue); ?>" href="<?= $subValue; ?>"><?= $subKey; ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?= isActive($value); ?>" href="<?= $value; ?>"><?= $key; ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($IsAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= isActive(Registry::get("RouteTranslations")['AdminPage']); ?>" href="<?= Registry::get("RouteTranslations")['AdminPage']; ?>">Admin</a>
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
