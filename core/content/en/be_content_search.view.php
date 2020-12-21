<div class="row m-0 min-width">

    <form action="/<?= $this->conf_array['backend']?>/content/" method="post">
        <input type="hidden" name="check" value="ok">
        <input type="text" name="keyword" placeholder="Keyword" value="<?=(isset($keyword)?$keyword:'')?>">
        <select name="status">
            
            <?php
            if(isset($status)) {
                echo "<option value='$status'>".$this->conf_array['statuses'][$status]."</option>";
                $sdata['excp1']=$status;
            } else echo "<option value=''>Status</option>";
            $sdata=array();
            $sdata['data']=$this->conf_array['statuses'];
            $sdata['type']='key2value';
            $sdata['excp']=2;
            $sdata['id']=$this->lang_id;
            echo Html::selectList($sdata);
            ?>
        </select>
        <select name="page_custom">
            <?php
            if(isset($custom)) {
                echo "<option value='$custom'>".$this->conf_array['custom_ids'][$custom]."</option>";
                $sdata['excp']=$custom;
            } else echo "<option value=''>Type</option>";
            $sdata=array();
            $sdata['data']=$this->conf_array['custom_ids'];
            $sdata['type']='key2value';
            $sdata['id']=$this->lang_id;
            echo Html::selectList($sdata);     
            ?>
        </select>
        <select name="page_month">
            
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
            
            if(isset($month)){
                echo "<option value='$month'>$months[$month]</option>";
                $sdata['excp']=$month;
            } else echo '<option value="">Month</option>';
            $sdata=array();
            $sdata['data']=$months;
            $sdata['type']='key2value';
            $sdata['id']=$this->lang_id;
            echo Html::selectList($sdata);     
            ?>
        </select>
        <select name="page_year">
            <?php
            if(isset($year)){
                echo "<option value='$year'>$year</option>";
                $sdata['excp']=$year;
            } else echo '<option value="">Year</option>';
            $sdata=array();
            $sdata['data']=range(date("Y"),(date("Y")-16));
            $sdata['type']='value2value';
            $sdata['id']=$this->lang_id;
            echo Html::selectList($sdata); 
            ?>
        </select>
        <input type="submit" value="Submit">
    </form>

    <a class="ml-auto" href="/<?= $this->conf_array['backend'] ?>/content/add/" >Add an article</a>

</div>
