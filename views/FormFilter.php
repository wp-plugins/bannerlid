
<form action="<?php echo get_admin_url();?>admin.php" name="filter-form" method="get">
    
    <ul class="inline-form" id="bannerlid-stat-filter">    
        <li class="label"><label>Timeframe</label></li>
        <li class="value">
            <select name="timeframe">
                <option value="daily" <?php echo !isset($_GET['timeframe']) || $_GET['timeframe'] == "daily" ? 'selected' : '';?>>Daily</option>
                <option value="monthly" <?php echo isset($_GET['timeframe']) && $_GET['timeframe'] == "monthly" ? 'selected' : '';?>>Monthly</option>
            </select>
        </li>                                                                               
        
        <li class="label"><label>Time length</label></li>
        <li class="value">
            <input type="text" value="<?php echo isset($_GET['timelength']) ? $_GET['timelength'] : "14";?>" name="timelength" />
        </li>
        
        <li class="label"><label>Up until</label></li>
        <li class="value">
            <input type="text" id="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");?>" name="end_date" />
        </li>
       
        <li class="label"><label>Unique IPs</label></li>
        <li class="value">
            <input type="checkbox" name="unique_ip" value="1" <?php echo isset($_GET['unique_ip']) ? "checked" : "";?> />
        </li>

        <li class="value">
             <input type="submit" name="submit" id="filter_submit" class="submit-button" value="Filter" />
        </li>
    </ul>

    <input type="hidden" name="page" value="<?php echo $_GET['page'];?>" />
    <input type="hidden" name="subpage" value="<?php echo $_GET['subpage'];?>" />
    <input type="hidden" name="id" value="<?php echo $_GET['id'];?>" />
   
</form>

