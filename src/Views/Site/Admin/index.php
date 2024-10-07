<?php
$Users = $arguments['Users'];
ob_start();
?>



<div>
    <h2>Admin Area</h2>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    <?php foreach ($Users as $User):
        $Username = $User->username;
        $Email = $User->email;
        $Name = $User->name;
        $Surname = $User->surname;
        $ID = $User->id;
        ?>

        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title   "><?= $Username ?></h5>
                    <p class="card-text">
                        <strong>Email:</strong> <?= $Email ?><br>
                        <strong>Name:</strong> <?= $Name ?><br>
                        <strong>Surname:</strong> <?= $Surname ?><br>
                        <strong>ID:</strong> <?= $ID ?><br>
                    </p>
                </div>
            </div>


        </div>
    <?php endforeach; ?>
</div>



<?php
$template = ob_get_contents();
ob_end_clean();
