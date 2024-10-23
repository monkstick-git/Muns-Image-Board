<?php

$shortURLs = $arguments['shortURLs'] ?? [];

ob_start();
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Your Short URLs</h1>
                <a href="<?= Registry::get("RouteTranslations")['ShortNew'] ?>" class="btn btn-primary">Create New Short
                    URL</a>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Short URL</th>
                                <th scope="col">Long URL</th>
                                <th scope="col">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($shortURLs)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-3">No short URLs found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($shortURLs as $shortURL): ?>
                                    <tr>
                                        <td>
                                            <a href="/s?s=<?= htmlspecialchars($shortURL['short_url'], ENT_QUOTES) ?>"
                                                target="_blank">
                                                <?= htmlspecialchars($shortURL['short_url'], ENT_QUOTES) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($shortURL['long_url'], ENT_QUOTES) ?></td>
                                        <td><?= htmlspecialchars($shortURL['created'], ENT_QUOTES) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Uncomment if pagination or load more is needed in the future
            <div class="d-flex justify-content-center mt-4">
                <button class="btn btn-outline-primary">Load more</button>
            </div>
            -->
        </div>
    </div>
</div>

<?php
$template = ob_get_contents();
ob_end_clean();
?>