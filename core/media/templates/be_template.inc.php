

<!DOCTYPE html>
<html lang="en">
<head>
    <title> <?= $title ?> </title>

    <?php
        require_once(ROOT.'core/content/system/head.view.php');
    ?>

</head>
<body>
    
    <?php
        require_once(ROOT.'core/content/system/be_navsidebar.view.php');
    ?>


        <div class="col-md-10 min-height float-right p-5">
            <?= $content ?>
        </div>
    </div>
    <?php
        require_once(ROOT.'core/content/system/js.view.php');
    ?>
</body>
</html>