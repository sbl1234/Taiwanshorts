<?php
//date_default_timezone_set("America/North_Dakota/Beulah"); 	
date_default_timezone_set('Asia/Taipei');
require_once ('dbconnect.php');

    $data = null;

   $conn1 = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
   
   $sblarr = null;
   
   $result_sbl_ticks = $conn1->query("SELECT max(sbl_qty) as sbl_qty, date FROM stock_data.sbl_info WHERE `date` > DATE( CURDATE( ) ) - INTERVAL 1 DAY AND ticker = '".$_GET['ticker']."' group by date order by date asc");

   //echo ("SELECT max(sbl_qty) as sbl_qty, date FROM stock_data.sbl_info WHERE `date` > DATE( CURDATE( ) ) - INTERVAL 1 DAY AND ticker = '".$_GET['ticker']."' group by date order by date asc");


   
   while ($sbl = $result_sbl_ticks->fetch_assoc()) {
       
       $sblarr[$sbl['date']] = $sbl['sbl_qty'];
   }
   
   //echo("SELECT sr_no, avail, created_at, date FROM stock_data.avail_info_history WHERE `date` > DATE( CURDATE( ) ) - INTERVAL 1 DAY AND ticker = '".$_GET['ticker']."' order by sr_no asc");

   //exit;end;

   $result_avail_ticks = $conn1->query("SELECT sr_no, avail, created_at, date FROM stock_data.avail_info_history WHERE `date` > DATE( CURDATE( ) ) - INTERVAL 1 DAY AND ticker = '".$_GET['ticker']."' order by sr_no asc");

   
   $lastsod = 0;
   
   while ($avail = $result_avail_ticks->fetch_assoc()) {
       $subdata = null;

       $tt = preg_split('/[- :]/', $avail['created_at']);
       
       list($year, $month, $day, $hour, $minute, $second) = $tt; 
//echo $hour.', '.$minute.', '.$second.', '.$month.', '.$day.', '.$year;
        $timestamp = (mktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year));
//echo date("Hi", $timestamp)."\n";
        if(date("Hi", $timestamp) > "0800" && date("Hi", $timestamp) < "1830") {
        //if($hour.$minute > "0800" && $hour.$minute < "1830") {

            //$subdata[] = ((int)$timestamp - 5*60*60)*1000;
            $subdata[] = ((int)$timestamp + 8*60*60)*1000;
            
            if (isset($sblarr[$avail['date']])) $lastsod = (int)$sblarr[$avail['date']];
            $used = $lastsod - (int)$avail['avail'];
            
            if ($used<0) $used = 0;
            $subdata[] = $used;
            $data[] = $subdata;
            
        }
   }
   
   echo json_encode($data);
   
?>
