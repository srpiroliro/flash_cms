<!DOCTYPE html>
<html lang="en">
<head>
    <title> <?= $title ?> </title>

    <?php
        require_once(ROOT.'core/content/system/head.view.php');
    ?>


    <style>
        .welcome {
            position:relative;
            width: 100%;
            height: 100%;
        }
        .center {
            position: absolute;
            top: 50%;
            left: 50%;

            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%,-50%); 
        }
	#continue {
		padding: 6rem 2rem  !important;
	}
    </style>

</head>
<body>

    

        <?php
            require_once(ROOT.'core/content/system/fe_navbar.view.php');
        ?>
    

        <div class="welcome">
            <div class="center text-center text-white">
                <h1 class="text-bold">Welcome!</h1>
                <p>A wonderful project by me. </p>
                <a class="btn btn-dark" href="#continue">Continue</a>
            </div>
        </div>


        <div id="continue" class="content bg-white w-100 pt-5">
            <div class="row m-0 pt-4">
		<div class="col-md-3"></div>
                <div class="col-md-5 text-justify">
                    <h1>Example text</h1>
                    <p>The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence.</p>
                    <p>The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence.</p>
                    <p>The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence.</p>
                    <p>The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence.</p>
                    <p>The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence.</p>
                    <p>The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence.</p>
                    <p>The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence.</p>
                    <p>The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence.</p>
                    <p>The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence,The quick brown fox jumped over the fence.</p>
                </div>
                <div class="col-md-1">
                    <?=$recent?>
                </div>
		<div class="col-md-3"></div>
            </div>
        </div>
    
        
    
    


    <?php
        require_once(ROOT.'core/content/system/js.view.php');
    ?>

</body>
</html>
