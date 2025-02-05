<?php
# User Permissions are handed to us from the controller
$Permissions = $arguments['Permissions'];
$allPermissions = $arguments['PermissionMatrix']; // Dynamically loaded

ob_start();
?>

<!-- OVER-THE-TOP STYLING -->
<style>
    body {
        background: #050505;
        color: #fff;
        font-family: 'Orbitron', sans-serif;
    }

    /* Grid Background Animation */
    .grid-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-image: linear-gradient(rgba(0, 255, 204, 0.1) 1px, transparent 1px),
                          linear-gradient(90deg, rgba(0, 255, 204, 0.1) 1px, transparent 1px);
        background-size: 40px 40px;
        animation: move-grid 10s linear infinite;
        z-index: -1;
    }

    @keyframes move-grid {
        from { background-position: 0 0, 0 0; }
        to { background-position: 40px 40px, 40px 40px; }
    }

    /* Permission Grid */
    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 40px;
    }

    .permission-category {
        background: rgba(0, 255, 204, 0.1);
        border: 2px solid #00ffcc;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 0 20px #00ffcc;
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        position: relative;
    }

    .permission-category:hover {
        transform: scale(1.03);
        box-shadow: 0 0 40px #00ffcc, 0 0 60px #00ffcc;
    }

    /* Permission Item */
    .permission-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #00ffcc;
        border-radius: 5px;
        background: rgba(0, 255, 204, 0.2);
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .permission-item:hover {
        background: rgba(0, 255, 204, 0.4);
    }

    .permission-item input {
        transform: scale(1.5);
    }

    /* Select All Toggle */
    .select-all {
        display: block;
        margin-bottom: 10px;
        padding: 5px;
        border-radius: 5px;
        background: #00ffcc;
        color: #111;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
    }

    .select-all:hover {
        background: #ff00ff;
        box-shadow: 0 0 15px #ff00ff;
    }

    /* Save Button */
    .btn-submit {
        display: block;
        width: 100%;
        font-size: 1.5rem;
        padding: 10px;
        border-radius: 10px;
        background: linear-gradient(45deg, #00ffcc, #ff00ff);
        color: #111;
        font-weight: bold;
        box-shadow: 0 0 20px #00ffcc;
        transition: 0.3s;
    }

    .btn-submit:hover {
        background: linear-gradient(45deg, #ff00ff, #00ffcc);
        box-shadow: 0 0 30px #ff00ff, 0 0 50px #00ffcc;
    }
</style>

<!-- Grid Background -->
<div class="grid-bg"></div>

<!-- Permissions Form -->
<form method="post" action="SavePermissions">
    <div class="container">
        <div class="permissions-header">⚡ Cyber-Grid Permission System ⚡</div>

        <?php foreach ($allPermissions as $category => $perms): ?>
            <div class="permission-category">
                <h4><?= htmlspecialchars($category) ?></h4>
                <span class="select-all" onclick="toggleCategory('<?= htmlspecialchars($category) ?>')">[Select All]</span>
                <div class="permission-grid">
                    <?php foreach ($perms as $perm): ?>
                        <div class="permission-item" onclick="toggleCheckbox('<?= htmlspecialchars($category) ?>', '<?= htmlspecialchars($perm) ?>')">
                            <span><?= htmlspecialchars($perm) ?></span>
                            <input type="checkbox"
                                   class="form-check-input <?= htmlspecialchars($category) ?>"
                                   name="permissions[<?= htmlspecialchars($category) ?>][<?= htmlspecialchars($perm) ?>]"
                                   value="1"
                                   <?= isset($Permissions[$category][$perm]) && $Permissions[$category][$perm] == 1 ? 'checked' : '' ?>>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- User ID (Hidden) -->
        <input type="hidden" name="user_id" value="<?= $arguments['UserID'] ?>">

        <button type="submit" class="btn btn-submit">Save Permissions 🔥</button>
    </div>
</form>

<!-- JavaScript -->
<script>
    // Toggle an entire category
    function toggleCategory(category) {
        let checkboxes = document.querySelectorAll('.' + category);
        let allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }

    // Toggle individual checkboxes by clicking anywhere in the row
    function toggleCheckbox(category, perm) {
        let checkbox = document.querySelector(`[name="permissions[${category}][${perm}]"]`);
        if (checkbox) checkbox.checked = !checkbox.checked;
    }
</script>

<?php
$template = ob_get_contents();
ob_end_clean();
?>
