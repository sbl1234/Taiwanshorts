<?php
date_default_timezone_set("Asia/Taipei");
error_reporting(0); 
    //require_once 'class/Paginator.class.php';
 	
 	require_once ('dbconnect.php');



   $conn1 = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

$starttime = time();
/*
    $clean_sbl = "DELETE from stock_data.sbl_info WHERE sr_no not in (SELECT number from (SELECT distinct(sbl.ticker) as tick, max(sbl.sr_no) as number, max(sbl.created_at) FROM stock_data.sbl_info as sbl GROUP BY tick, date(sbl.created_at)) as SBL_TT)";
    $clean_avail = "DELETE from stock_data.avail_info WHERE sr_no not in (SELECT number from (SELECT distinct(avl.ticker) as tick, max(avl.sr_no) as number, max(avl.created_at) FROM stock_data.avail_info as avl GROUP BY tick, date(avl.created_at)) as AVL_TT)";
    
    $results = $conn1->query($clean_sbl);
    $results = $conn1->query($clean_avail);
*/
//echo "Script1 - ".($starttime - time());
$starttime = time();

$result_sbl_ticks = $conn1->query("SELECT ticker, sbl_qty, created_at, `date` FROM stock_data.sbl_info WHERE `date` > DATE( CURDATE( ) ) - INTERVAL 365 DAY AND ticker IN (SELECT DISTINCT (ticker) AS tick FROM stock_data.sbl_info WHERE `date` = DATE( CURDATE( ) ) GROUP BY tick) order by ticker asc, created_at asc;");
//echo "Scrip2 - ".($starttime - time());
$starttime = time();

$result_avail_ticks = $conn1->query("SELECT ticker, avail, created_at, last_modify, `date` FROM stock_data.avail_info WHERE `date` > DATE(curdate())-INTERVAL 365 DAY AND ticker IN (SELECT DISTINCT (ticker) AS tick FROM stock_data.sbl_info WHERE `date` = DATE( CURDATE( ) ) GROUP BY tick) order by ticker asc, last_modify asc;");

//echo "Script3 - ".($starttime - time());

$datecurr = date("Y-m-d");
$datecurrddmm = date("Y-m-d H:i:s", strtotime("-1 day"));
$date2days = date("Y-m-d", strtotime("-1 day"));
$date3days = date("Y-m-d", strtotime("-2 day"));
$date4days = date("Y-m-d", strtotime("-3 day"));
$date5days = date("Y-m-d", strtotime("-4 day"));
$date7days = date("Y-m-d", strtotime("-7 day"));
$date30days = date("Y-m-d", strtotime("-30 day"));
$date91days = date("Y-m-d", strtotime("-91 day"));
$date182days = date("Y-m-d", strtotime("-182 day"));
$date365days = date("Y-m-d", strtotime("-365 day"));

//echo "date7days:".$date7days;

$stock = null;


while ($sbl = $result_sbl_ticks->fetch_assoc()) {
    $stockname = $sbl['ticker'];
    $stockcreatedat = $sbl['created_at'];
    $stockcreateddate = $sbl['date'];
    //echo date('j',strtotime($stockcreatedat))."<br/>";
    if ($stock[$stockname] ['sbl_loop_date']!=$stockcreateddate) {
    	$stock[$stockname] ['sbl_loop_date']=$stockcreateddate;
	    if ($stock[$stockname] ['sbl_last_modify']<$stockcreatedat) {
		    $stock[$stockname] ['sbl_last_modify'] = $stockcreatedat;
		    $stock[$stockname] ['sbl_sod'] = $sbl['sbl_qty'];
		}
	        
	        
	    if ($stockcreateddate==$datecurr) {
	        if (!isset($stock[$stockname] ['sum_sblqty_curr_day'][date('j',strtotime($stockcreatedat))]))
	            $stock[$stockname] ['sum_sblqty_curr_day'][date('j',strtotime($stockcreatedat))] = $sbl['sbl_qty'];
	    }
	
	    if ($stockcreateddate==$date2days) {
	        if (!isset($stock[$stockname] ['sum_sblqty_2days_day'][date('j',strtotime($stockcreatedat))]))
	            $stock[$stockname] ['sum_sblqty_2days_day'][date('j',strtotime($stockcreatedat))] = $sbl['sbl_qty'];
	            
	        //$stock[$stockname] ['sum_sblqty_2days'] += $sbl['sbl_qty'];
	    }
	
	    if ($stockcreateddate==$date3days) {
	        if (!isset($stock[$stockname] ['sum_sblqty_3days_day'][date('j',strtotime($stockcreatedat))]))
	            $stock[$stockname] ['sum_sblqty_3days_day'][date('j',strtotime($stockcreatedat))] = $sbl['sbl_qty'];
	            
	        //$stock[$stockname] ['sum_sblqty_3days'] += $sbl['sbl_qty'];
	    }
	
	    if ($stockcreateddate==$date4days) {
	        if (!isset($stock[$stockname] ['sum_sblqty_4days_day'][date('j',strtotime($stockcreatedat))]))
	            $stock[$stockname] ['sum_sblqty_4days_day'][date('j',strtotime($stockcreatedat))] = $sbl['sbl_qty'];
	            
	        //$stock[$stockname] ['sum_sblqty_4days'] += $sbl['sbl_qty'];
	    }
	
	    if ($stockcreateddate==$date5days) {
	        if (!isset($stock[$stockname] ['sum_sblqty_5days_day'][date('j',strtotime($stockcreatedat))]))
	            $stock[$stockname] ['sum_sblqty_5days_day'][date('j',strtotime($stockcreatedat))] = $sbl['sbl_qty'];
	            
	        //$stock[$stockname] ['sum_sblqty_5days'] += $sbl['sbl_qty'];
	    }
	
	    if ($stockcreateddate>=$date7days) {
	        if (!isset($stock[$stockname] ['sum_sblqty_7days_day'][$stockcreateddate]))
	            $stock[$stockname] ['sum_sblqty_7days_day'][$stockcreateddate] = $sbl['sbl_qty'];
	            
	        //$stock[$stockname] ['sum_sblqty_7days'] += $sbl['sbl_qty'];
	    }
	
	    if ($stockcreateddate>=$date30days) {
	        if (!isset($stock[$stockname] ['sum_sblqty_30days_day'][$stockcreateddate]))
	            $stock[$stockname] ['sum_sblqty_30days_day'][$stockcreateddate] = $sbl['sbl_qty'];
	            
	        //$stock[$stockname] ['sum_sblqty_30days'] += $sbl['sbl_qty'];
	    }
	
	    if ($stockcreateddate>=$date91days) {
	        if (!isset($stock[$stockname] ['sum_sblqty_91days_day'][$stockcreateddate]))
	            $stock[$stockname] ['sum_sblqty_91days_day'][$stockcreateddate] = $sbl['sbl_qty'];
	            
	        //$stock[$stockname] ['sum_sblqty_91days'] += $sbl['sbl_qty'];
	    }
	
	    if ($stockcreateddate>=$date182days) {
	        if (!isset($stock[$stockname] ['sum_sblqty_182days_day'][$stockcreateddate]))
	            $stock[$stockname] ['sum_sblqty_182days_day'][$stockcreateddate] = $sbl['sbl_qty'];
	            
	        //$stock[$stockname] ['sum_sblqty_182days'] += $sbl['sbl_qty'];
	    }
	
	    if ($stockcreateddate>=$date365days) {
	        if (!isset($stock[$stockname] ['sum_sblqty_365days_day'][$stockcreateddate]))
	            $stock[$stockname] ['sum_sblqty_365days_day'][$stockcreateddate] = $sbl['sbl_qty'];
	            
	        //$stock[$stockname] ['sum_sblqty_365days'] += $sbl['sbl_qty'];
	    }
	}
}

//echo "Script4 - ".($starttime - time());
$starttime = time();

while ($avail = $result_avail_ticks->fetch_assoc()) {
    $stockname = $avail['ticker'];
    
    $stockcreatedat = $avail['created_at'];
    $stockcreateddate = $avail['date'];
    
    if ($stock[$stockname] ['avail_loop_date']!=$stockcreateddate) {
    	$stock[$stockname] ['avail_loop_date']=$stockcreateddate;
    	
	        if ($stock[$stockname] ['avail_last_modify']<$stockcreatedat) {
	            $stock[$stockname] ['avail_last_modify'] = $stockcreatedat;
	            $stock[$stockname] ['avail_qty'] = $avail['avail'];
	            
	        }
	        
	    if ($stockcreateddate>=$date2days) {
	        if (!isset($stock[$stockname] ['sum_avail_curr_day'][date('j',strtotime($stockcreatedat))]))
	            $stock[$stockname] ['sum_avail_curr_day'][date('j',strtotime($stockcreatedat))] = $avail['avail'];
	            
	        //$stock[$stockname] ['sum_avail_curr'] += $avail['avail'];
	        //if (!isset($stock[$stockname] ['last_modify'])) 
	            $stock[$stockname] ['last_modify'] = $avail['last_modify'];
	    }
	
	    if ($stockcreateddate==$date2days) {
	        if (!isset($stock[$stockname] ['sum_avail_2days_day'][date('j',strtotime($stockcreatedat))]))
	            $stock[$stockname] ['sum_avail_2days_day'][date('j',strtotime($stockcreatedat))] = $avail['avail'];
	            
	        //$stock[$stockname] ['sum_avail_2days'] += $avail['avail'];
	    }
	
	    if ($stockcreateddate==$date3days) {
	        if (!isset($stock[$stockname] ['sum_avail_3days_day'][date('j',strtotime($stockcreatedat))]))
	            $stock[$stockname] ['sum_avail_3days_day'][date('j',strtotime($stockcreatedat))] = $avail['avail'];
	            
	        //$stock[$stockname] ['sum_avail_3days'] += $avail['avail'];
	    }
	
	    if ($stockcreateddate==$date4days) {
	        if (!isset($stock[$stockname] ['sum_avail_4days_day'][date('j',strtotime($stockcreatedat))]))
	            $stock[$stockname] ['sum_avail_4days_day'][date('j',strtotime($stockcreatedat))] = $avail['avail'];
	            
	        //$stock[$stockname] ['sum_avail_4days'] += $avail['avail'];
	    }
	
	    if ($stockcreateddate==$date5days) {
	        if (!isset($stock[$stockname] ['sum_avail_5days_day'][date('j',strtotime($stockcreatedat))]))
	            $stock[$stockname] ['sum_avail_5days_day'][date('j',strtotime($stockcreatedat))] = $avail['avail'];
	            
	        //$stock[$stockname] ['sum_avail_5days'] += $avail['avail'];
	    }
	
	    if ($stockcreateddate>=$date7days) {
	        if (!isset($stock[$stockname] ['sum_avail_7days_day'][$stockcreateddate]))
	            $stock[$stockname] ['sum_avail_7days_day'][$stockcreateddate] = $avail['avail'];
	            
	        //$stock[$stockname] ['sum_avail_7days'] += $avail['avail'];
	    }
	
	    if ($stockcreateddate>=$date30days) {
	        if (!isset($stock[$stockname] ['sum_avail_30days_day'][$stockcreateddate]))
	            $stock[$stockname] ['sum_avail_30days_day'][$stockcreateddate] = $avail['avail'];
	            
	        //$stock[$stockname] ['sum_avail_30days'] += $avail['avail'];
	    }
	
	    if ($stockcreateddate>=$date91days) {
	        if (!isset($stock[$stockname] ['sum_avail_91days_day'][$stockcreateddate]))
	            $stock[$stockname] ['sum_avail_91days_day'][$stockcreateddate] = $avail['avail'];
	            
	        //$stock[$stockname] ['sum_avail_91days'] += $avail['avail'];
	    }
	
	    if ($stockcreateddate>=$date182days) {
	        if (!isset($stock[$stockname] ['sum_avail_182days_day'][$stockcreateddate]))
	            $stock[$stockname] ['sum_avail_182days_day'][$stockcreateddate] = $avail['avail'];
	            
	        //$stock[$stockname] ['sum_avail_182days'] += $avail['avail'];
	    }
	
	    if ($stockcreateddate>=$date365days) {
	        if (!isset($stock[$stockname] ['sum_avail_365days_day'][$stockcreateddate]))
	            $stock[$stockname] ['sum_avail_365days_day'][$stockcreateddate] = $avail['avail'];
	            
	        //$stock[$stockname] ['sum_avail_365days'] += $avail['avail'];
	    }
	}
}
//echo "Script5 - ".($starttime - time());
$starttime = time();
//print_r($stock);
ob_start();
?>


<!DOCTYPE html>
    <head>
        <title>Taiwan Stock Analysis</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
                <div class="col-md-10 col-md-offset-1" id="dvData">
	            <div class='button'>
	                <ul>
	                <li><a href="#" id="downloaddata" role='button'>Download Data</a></li><?php /*
	                <li><a href="../script1/" role='button' target='_blank'>Download Availability File</a></li>
	                <li><a href="../script2/" role='button' target='_blank'>Download SBL File</a></li> */ ?>
	                </ul>
	            </div>

                <h1 style="float:left">Taiwan Stock Analysis</h1>
                <input id="searchInput" placeholder="Type To Filter" style="float:right">
                <table class="table table-striped table-condensed table-bordered table-rounded sortable">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Ticker</th>
                                <th>SOD SS Quota</th>       
                                <th>Quota Left</th>     
                                <th>Last Modified </th>     
                                <th>% SSQ Used Last</th>
                                <th>% SSQ Used T-1</th>
                                <th>% SSQ Used T-2</th>
                                <th>% SSQ Used T-3</th>
                                <th>% SSQ Used T-4</th>
                                <th>% SS Quota Used 1 Week</th>
                                <th>% SS Quota Used 1 Month</th>
                                <th>% SS Quota Used 3 Months</th>
                                <th>% SS Quota Used 6 Months</th>
                                <th>% SS Quota Used 1 Year</th>
                            </tr>
                        </thead>
                        <tbody id="fbody">

<?php
    $j = 1;
    foreach ($stock as $key => $obj) {
        
        //echo "diby".$key;
                //print_r ($obj);
                foreach ($obj ['sum_sblqty_curr_day'] as $sbl) {
                    $obj ['sum_sblqty_curr'] += $sbl;
                }
                
                foreach ($obj ['sum_sblqty_2days_day'] as $sbl) {
                    $obj ['sum_sblqty_2days'] += $sbl;
                }
                
                foreach ($obj ['sum_sblqty_3days_day'] as $sbl) {
                	$obj ['sum_sblqty_3days'] +=$sbl;
                }
                
                foreach ($obj ['sum_sblqty_4days_day'] as $sbl) {
                	$obj ['sum_sblqty_4days'] +=$sbl;
                }
                
                foreach ($obj ['sum_sblqty_5days_day'] as $sbl) {
                	$obj ['sum_sblqty_5days'] +=$sbl;
                }
                
                foreach ($obj ['sum_sblqty_7days_day'] as $sbl) {
                	$obj ['sum_sblqty_7days'] +=$sbl;
                }
                
                foreach ($obj ['sum_sblqty_30days_day'] as $sbl) {
                	$obj ['sum_sblqty_30days'] +=$sbl;
                }
                
                foreach ($obj ['sum_sblqty_91days_day'] as $sbl) {
                	$obj ['sum_sblqty_91days'] +=$sbl;
                }
                
                foreach ($obj ['sum_sblqty_182days_day'] as $sbl) {
                	$obj ['sum_sblqty_182days'] +=$sbl;
                }
                
                foreach ($obj ['sum_sblqty_365days_day'] as $sbl) {
                	$obj ['sum_sblqty_365days'] +=$sbl;
                }
                
                    
                foreach ($obj ['sum_avail_curr_day'] as $avail) {
                	$obj ['sum_avail_curr'] +=$avail;
                }
                
                foreach ($obj ['sum_avail_2days_day'] as $avail) {
                	$obj ['sum_avail_2days'] +=$avail;
                }
                
                foreach ($obj ['sum_avail_3days_day'] as $avail) {
                	$obj ['sum_avail_3days'] +=$avail;
                }
                
                foreach ($obj ['sum_avail_4days_day'] as $avail) {
                	$obj ['sum_avail_4days'] +=$avail;
                }
                
                foreach ($obj ['sum_avail_5days_day'] as $avail) {
                	$obj ['sum_avail_5days'] +=$avail;
                }
                
                foreach ($obj ['sum_avail_7days_day'] as $avail) {
                	$obj ['sum_avail_7days'] +=$avail;
                }
                
                foreach ($obj ['sum_avail_30days_day'] as $avail) {
                	$obj ['sum_avail_30days'] +=$avail;
                }
                
                foreach ($obj ['sum_avail_91days_day'] as $avail) {
                	$obj ['sum_avail_91days'] +=$avail;
                }
                
                foreach ($obj ['sum_avail_182days_day'] as $avail) {
                	$obj ['sum_avail_182days'] +=$avail;
                }
                
                foreach ($obj ['sum_avail_365days_day'] as $avail) {
                	$obj ['sum_avail_365days'] +=$avail;
                }
                
                    

                
            //print_r ($obj);
                        
            $sbl_sod=$obj['sbl_sod'];

			$avail_qty=$obj['avail_qty'];
			$day2_sbl=$obj['sum_sblqty_2days'];
			$day3_sbl=$obj['sum_sblqty_3days'];
			$day4_sbl=$obj['sum_sblqty_4days'];
			$day5_sbl=$obj['sum_sblqty_5days'];
			$week_sbl=$obj['sum_sblqty_7days'];
			$day2_qty=$obj['sum_avail_2days'];
			$day3_qty=$obj['sum_avail_3days'];
			$day4_qty=$obj['sum_avail_4days'];
			$day5_qty=$obj['sum_avail_5days'];
			$week_qty=$obj['sum_avail_7days'];
			$month_sbl=$obj['sum_sblqty_30days'];
			$month_qty=$obj['sum_avail_30days'];
			$quart_sbl=$obj['sum_sblqty_91days'];
			$quart_qty=$obj['sum_avail_91days'];
			$half_sbl=$obj['sum_sblqty_182days'];
			$half_qty=$obj['sum_avail_182days'];
			$year_sbl=$obj['sum_sblqty_365days'];
			$year_qty=$obj['sum_avail_365days'];
			$last_modify = (!(isset($obj['last_modify'])) || strtotime($obj['last_modify'])==1495929600)?"":date("H:i:s",strtotime($obj['last_modify']));
                            
            $dayquota = max(0,($sbl_sod-$avail_qty)*100/($sbl_sod+1));
            $day2quota = max(0,($day2_sbl-$day2_qty)*100/($day2_sbl+1));
            $day3quota = max(0,($day3_sbl-$day3_qty)*100/($day3_sbl+1));
            $day4quota = max(0,($day4_sbl-$day4_qty)*100/($day4_sbl+1));
            $day5quota = max(0,($day5_sbl-$day5_qty)*100/($day5_sbl+1));
            $weekquota = max(0,($week_sbl-$week_qty)*100/($week_sbl+1));
            $monthquota = max(0,($month_sbl-$month_qty)*100/($month_sbl+1));
            $quartquota = max(0,($quart_sbl-$quart_qty)*100/($quart_sbl+1));
            $halfquota = max(0,($half_sbl-$half_qty)*100/($half_sbl+1));
            $yearquota = max(0,($year_sbl-$year_qty)*100/($year_sbl+1));


            echo '<tr>
                <td> '. $j++. '</td>
                <td> '. $key. ' <a href="historychart.php?ticker='.$key.'" data-featherlight="iframe"  data-featherlight-iframe-allowfullscreen="true" data-featherlight-iframe-width="1000" data-featherlight-iframe-height="420"><img src="https://thumb1.shutterstock.com/display_pic_with_logo/2526499/592357706/stock-vector-growing-bars-graphic-sign-vector-new-year-reddish-icon-with-outside-stroke-and-gray-shadow-on-592357706.jpg" width="20" height="20"/></a></td>
                <td>'. number_format($sbl_sod). '</td>  
                <td>'. number_format($avail_qty). '</td>    
                <td>'. $last_modify. '</td> 
                <td>'. number_format($dayquota, 2, '.',','). '%</td>    
                <td>'. number_format($day2quota, 2, '.',','). '%</td>    
                <td>'. number_format($day3quota, 2, '.',','). '%</td>    
                <td>'. number_format($day4quota, 2, '.',','). '%</td>    
                <td>'. number_format($day5quota, 2, '.',','). '%</td>    
                <td>'. number_format($weekquota, 2, '.',','). '%</td>   
                <td>'. number_format($monthquota, 2, '.',','). '%</td>  
                <td>'. number_format($quartquota, 2, '.',','). '%</td>  
                <td>'. number_format($halfquota, 2, '.',','). '%</td>   
                <td>'. number_format($yearquota, 2, '.',','). '%</td>   

            </tr>'; 

}


?>
</tbody>
                </table>
                <?php //echo $Paginator->createLinks( $links, 'pagination pagination-sm' ); ?> 
                </div>
        </div>
        </body>
        <script type='text/javascript' src='JS/sorttable.js'></script>
        <script type='text/javascript' src='https://code.jquery.com/jquery-1.11.0.min.js'></script>
        <link href="featherlight/featherlight.css" type="text/css" rel="stylesheet" />
        <script src="featherlight/featherlight.js" type="text/javascript" charset="utf-8"></script>
        <!-- If you want to use jquery 2+: https://code.jquery.com/jquery-2.1.0.min.js -->
        <script type='text/javascript'>
        $(document).ready(function () {

            //console.log("HELLO")
            function exportTableToCSV($table, filename) {
                console.log($table)
                console.log(filename)


                var $headers = $table.find('tr:has(th)')
                    ,$rows = $table.find('tr:has(td)')

                    // Temporary delimiter characters unlikely to be typed by keyboard
                    // This is to avoid accidentally splitting the actual contents
                    ,tmpColDelim = String.fromCharCode(11) // vertical tab character
                    ,tmpRowDelim = String.fromCharCode(0) // null character

                    // actual delimiter characters for CSV format
                    ,colDelim = '","'
                    ,rowDelim = '"\r\n"';

                    // Grab text from table into CSV formatted string
                    var csv = '"';
                    csv += formatRows($headers.map(grabRow));
                    csv += rowDelim;
                    csv += formatRows($rows.map(grabRow)) + '"';

                    // Data URI
                    var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);
                    console.log(csvData)

                $(this)
                    .attr({
                    'download': filename
                        ,'href': csvData
                        ,'target' : '_blank' //if you want it to open in a new window
                });

                //------------------------------------------------------------
                // Helper Functions 
                //------------------------------------------------------------
                // Format the output so it has the appropriate delimiters
                function formatRows(rows){
                    return rows.get().join(tmpRowDelim)
                        .split(tmpRowDelim).join(rowDelim)
                        .split(tmpColDelim).join(colDelim);
                }
                // Grab and format a row from the table
                function grabRow(i,row){
                     
                    var $row = $(row);
                    //for some reason $cols = $row.find('td') || $row.find('th') won't work...
                    var $cols = $row.find('td'); 
                    if(!$cols.length) $cols = $row.find('th');  

                    return $cols.map(grabCol)
                                .get().join(tmpColDelim);
                }
                // Grab and format a column from the table 
                function grabCol(j,col){
                    var $col = $(col),
                        $text = $col.text();

                    return $text.replace('"', '""'); // escape double quotes

                }
            }


            // This must be a hyperlink
            $("#downloaddata").click(function (event) {
                // var outputFile = 'export'

                var outputFile = window.prompt("What do you want to name your output file (Note: This won't have any effect on Safari)") || 'export';
                outputFile = outputFile.replace('.csv','') + '.csv'
                 
                // CSV
                exportTableToCSV.apply(this, [$('#dvData>table'), outputFile]);
                
                // IF CSV, don't do event.preventDefault() or return false
                // We actually need this to be a typical hyperlink
            });
        });
        
        
        
        $(document).ready(function() {

      $("#searchInput").keyup(function(){
	//hide all the rows
          $("#fbody").find("tr").hide();

	//split the current value of searchInput
          var data = this.value.split(" ");
	//create a jquery object of the rows
          var jo = $("#fbody").find("tr");
          
	//Recusively filter the jquery object to get results.
          $.each(data, function(i, v){
              jo = jo.filter("*:contains('"+v+"')");
          });
        //show the rows that match.
          jo.show();
     //Removes the placeholder text  
   
      }).focus(function(){
          this.value="";
          $(this).css({"color":"black"});
          $(this).unbind('focus');
      }).css({"color":"#C0C0C0"});

  });



    </script>

</html>
<?php
	$content = ob_get_contents();
	$myfile = fopen("cache.txt", "w") or die("Unable to open file!");
	fwrite($myfile, $content);
	fclose($myfile);
	
	// send email
    //mail("dibwas@gmail.com","Cache created at ".date("Y-m-d H:i:s"),"Cache created at ".date("Y-m-d H:i:s"));
    
    echo "Script6 - ".($starttime - time());
$starttime = time();

?>