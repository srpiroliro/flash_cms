<div class="row m-0 min-width">

    <form action="<?= $_SERVER['REQUEST_URI']?>" method="post">

        <input type="hidden" name="check" value="ok">
        <input type="text" name="name" placeholder="User's name" id="">

        <select name="status">
            <option value="">Status</option>
            <?php

            $sdata=array();
            $sdata['data']=$this->conf_array['statuses'];
            $sdata['type']='key2value';
            $sdata['id']=$this->lang_id;
            echo Html::selectList($sdata);

            ?>
        </select>


        <select name="role">
            <option value="">Type</option>
            <?php
            $sdata=array();
            $sdata['data']=$this->conf_array['roles'];
            $sdata['type']='key2value';
            $sdata['id']=$this->lang_id;

            echo Html::selectList($sdata);     
            ?>
        </select>

        <select name="month">
            <option value="">Month</option>
            <?php

            $months=[
                1=>'January',
                2=>'February',
                3=>'March',
                4=>'April',
                5=>'May',
                6=>'June',
                7=>'July',
                8=>'August',
                9=>'September',
                10=>'October',
                11=>'November',
                12=>'December'
            ];
            
            $sdata=array();
            $sdata['data']=$months;
            $sdata['type']='key2value';
            $sdata['id']=$this->lang_id;
            echo Html::selectList($sdata);     
            ?>
        </select>

        <select name="year">
            <option value="">Year</option>
            <?php

            $sdata=array();
            $sdata['data']=range(date("Y"),(date("Y")-16));
            $sdata['type']='value2value';
            $sdata['id']=$this->lang_id;
            echo Html::selectList($sdata); 

            ?>
        </select>

        <input type="submit" value="Submit">

    </form>

    <a class="ml-auto" href="add/">Add user</a>
</div>