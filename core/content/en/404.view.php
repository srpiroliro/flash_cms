<!DOCTYPE html>
<html lang="en">
<head>
    <title>Oops! 404</title>

    <?php
        require_once(ROOT.'core/content/system/head.view.php');
    ?>

    <style>
        .outer {
            display: table;
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
        }

        .middle {
            display: table-cell;
            vertical-align: middle;
        }

        .inner {
            margin-left: auto;
            margin-right: auto;
            width: 20%;
        }
    </style>

</head>
<body>
    <?php
        require_once(ROOT.'core/content/system/fe_navbar.view.php');
    ?>


    <div class="outer">
        <div class="middle">
            <div class="inner text-center text-white">
                <h1 style="font-weight:bold;">404 Oops!</h1>
                <p>The page you were looking for, doesn't exists. </p>
                <p>We are sorry :(</p>
            </div>
        </div>
    </div>
    

    <footer class="p-4 bg-white footer">
        <div class="container footer-copyright text-center py-3">© 2020 Copyright:
            <a href="http://tr.rem/"> tr.rem</a>
          </div>
    </footer>

    <?php
        require_once(ROOT.'core/content/system/js.view.php');
    ?>
</body>
</html>