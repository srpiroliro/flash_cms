<div class="row min-height min-width m-0">
        <div class="col-md-2 min-height sidebar-wrapper p-0 ">
            <nav id="sidebar" class="h-100 w-100">
                <div class="sidebar-header text-center">
                    <h3 class="text-bold">
                        Control Panel
                    </h3>

                    <p class="p-1">
                        Logged in as:<br>
                        <span class="text-bold">
                            <?= 
                                $_SESSION['mod_name'].
                                "<br><br>". 
                                "<span class='text-gold'>".ucfirst($this->conf_array["roles"][$_SESSION["mod_role"]])."</span>"
                            ?>
                        </span>
                    </p>
                                    
                </div>


                <ul class="nav flex-column components">
                    <li class="nav-item">
                        <a href="/<?= $this->conf_array['backend']?>/" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="/<?= $this->conf_array['backend']?>/content/" class="nav-link">Manage Content</a>
                    </li>
                    <?php
                        if($_SESSION['mod_role']=='1'){
                    ?>

                    <li class="nav-item">
                        <a href="/<?= $this->conf_array['backend']?>/users/" class="nav-link">View all users</a>
                    </li>

                    <?php
                        }
                    ?>

                    <li class="nav-item">
                        <a href="/" class="nav-link">Return to website</a>
                    </li>

                    <li class="nav-item mt-5">
                        <a href="/<?= $this->conf_array['backend']?>/logout/"  class="nav-link">Log Out</a>
                    </li>
                </ul>
                <div class="bottom w-100">
                    <div class="logout-div  ">
                        
                    </div>
                </div>
            </nav>
        </div>