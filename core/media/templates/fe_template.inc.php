<!DOCTYPE html>
<html lang="en">
<head>
    <title> <?= $title ?> </title>

    <?php
        require_once(ROOT.'core/content/system/head.view.php');
    ?>

	<style>
		#content {
			padding: 4rem 0 0 0 !important;
		}
		.min-h-100 {
		min-height:100% !important;
}
	</style>
</head>
<body>
    
    <?php
        require_once(ROOT.'core/content/system/fe_navbar.view.php');
    ?>

    <div id="content" class="row m-0 h-100">
        <div class="col-md-3"></div>
        <div class="col-md-6 min-h-100 bg-white">
            <div class="container">
            <div class="main">
                <?=$content?>
            </div>
           </div>
        </div>
        <div class="col-md-3"></div>
    </div>
    
     <!--footer class="p-4 bg-white footer">
        <div class="container footer-copyright text-center py-3">© 2020 Copyright:
            <a href="http://tr.rem/"> tr.rem</a>
          </div>
    </footer-->

    <?php
        require_once(ROOT.'core/content/system/js.view.php');
    ?>
</body>
</html>
