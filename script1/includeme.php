<?php 
date_default_timezone_set("Asia/Taipei");
	error_reporting(1);
	$starttime = time();

	require_once ('../dbconnect.php');

	$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	function quote_string($string) {
	    $string = str_replace('"', "'", $string);
	    $string = str_replace('&amp;', '&', $string);
	    $string = str_replace('&nbsp;', ' ', $string);
	    $string = preg_replace('!\s+!', ' ', $string);
	    return '"' . trim($string) . '"';
	}

	function get_value($element, $selector_string, $index, $type = "text") {
	    $value = "";
	    $cont = $element->find($selector_string, $index);
	    if ($cont) {
	        if ($type == "href") {
	            $value = $cont->href;
	        } elseif ($type == "src") {
	            $value = $cont->src;
	        } elseif ($type == "text") {
	            $value = trim($cont->plaintext);
	        } elseif ($type == "content") {
	            $value = trim($cont->content);
	        } else {
	            $value = $cont->innertext;
	        }
	    }

	    return trim($value);
	}


	// define functions
	function scrap_page($scrap_url) {

		global $base_url;
		$next_page = "";

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_URL, $scrap_url);
		curl_setopt($curl, CURLOPT_REFERER, $scrap_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		$str = curl_exec($curl);
		curl_close($curl);
		//echo $str;
		// Create a DOM object
		$html = new simple_html_dom();
		// Load HTML from a string
		$html->load($str);
	    //$html = file_get_html($scrap_url);

		if ($html && is_object($html) && isset($html->nodes)) {


			$json_decoded = json_decode($html, TRUE);
			$items = $json_decoded["msgArray"];

			// loop through items on current page
			foreach ($items as $item) {

				scrap_single($item,$texttowrite);
			}

			$html->clear();
		}

		return $next_page;
	}

	function scrap_single($item) {

		global $delimiter,$texttowrite, $db_insert;

		$csv_line = "";

		$csv_line .= quote_string($item["stkno"]) . " TT";
		$csv_line .= $delimiter . quote_string($item["slblimit"]);
		$csv_line .= $delimiter . quote_string($item["txtime"]);

		//$texttowrite .= $csv_line . "\n";

		$stock = $item["stkno"] . " TT";
		$limit =  $item["slblimit"];
		$time =  $item["txtime"];
		#echo $time . " ";

		$tcomp = explode(":",$time);

		$hr = intval($tcomp[0]);
		$min = 0;
		$sec = 0;

		if (count($tcomp) > 2) {
			$min = intval($tcomp[1]);
			$sec = intval($tcomp[2]);

		}    

		$last_trade = new DateTime(); #date("y-m-d h:i:s a");
		$current_date = date("Y-m-d H:i:s");
		$current_justdate = date("Y-m-d");
		$last_trade->setTime($hr, $min, $sec);

		$db_insert .= "('".$stock."', '".$limit."', '".$last_trade->format('Y-m-d H:i:s')."', '".$current_date."', '".$current_justdate."'),";
		
		
		

	}
	
	// set display errors status
	//ini_set('display_errors', 0); // 1-turn on all error reporings 0-turn off all error reporings
	
	// change max execution time to unlimitied
	ini_set('max_execution_time', 0);

	// include simple html dom parser
	require_once "simple_html_dom.php";

	// base url
	$base_url = "";

	// scrap url
	$scrap_url = "http://mis.twse.com.tw/stock/api/getStockSblsCap.jsp";

	// field delimiter in output file
	$delimiter = ",";
	// Set Time Zone

	date_default_timezone_set('Asia/Taipei');
	// output filename

	//$file = "../output/script/output_" .date('m-d-Y_H-i-s'). ".csv";

	// open file for writing final results
	//$handler = @fopen($file, "w");

	/*$texttowrite = "Stock Code" . $delimiter .
	"Real Time Available Volume for SBL Short Sales" . $delimiter .
	"Last Modify" . $delimiter .
	"\n";*/

	$current_date = date("Y-m-d");

	$db_hist_insert_start = "INSERT INTO `avail_info_history` (`ticker`, `avail`, `last_modify`, `created_at`, `date`) VALUES ";
	//$db_insert_start = " INSERT INTO `avail_info` (`ticker`, `avail`, `last_modify`, `created_at`) VALUES ";

	$db_insert_start = "INSERT INTO `avail_info` (`ticker`, `avail`, `last_modify`, `created_at`, `date`) VALUES ";

	$db_insert = "";

	scrap_page($scrap_url);

	$db_hist_insert = $db_hist_insert_start . substr($db_insert, 0, strlen($db_insert) -1);

	$db_hist_insert_query = mysqli_prepare($conn, $db_hist_insert);

	//echo $db_insert;

	if (!mysqli_stmt_execute($db_hist_insert_query)) {
		echo("Error description: " . mysqli_error($conn));
	}
	
	if(date("Hi") > "0800" && date("Hi") < "1830") {

    	if (!mysqli_stmt_execute(mysqli_prepare($conn, "Delete from `avail_info` where date = '".$current_date."';"))) {
    		echo("Error description: " . mysqli_error($conn));
    	}
    
    	//$db_insert = $db_insert_start . substr($db_insert, 0, strlen($db_insert) -1)."; ";
    
    	$db_insert = $db_insert_start . substr($db_insert, 0, strlen($db_insert) -1).";";
    
    	$db_insert_query = mysqli_prepare($conn, $db_insert);
    
    	//echo $db_insert;
    
    	if (!mysqli_stmt_execute($db_insert_query)) {
    		echo("Error description: " . mysqli_error($conn));
    	}
	}
	//fwrite($handler, $texttowrite);
	//fclose($handler);

	echo "Script1 - ".($starttime - time());

	
	echo "done 1";

	$myfile = fopen("script1.txt", "a") or die("Unable to open file!");
	fwrite($myfile, "\n\rDone at " . date("Y-m-d H:i:s"));
	fclose($myfile);

	// send email
    mail("dibwas@gmail.com","Script 1 done at ".date("Y-m-d H:i:s"),"Script 1 done at ".date("Y-m-d H:i:s"));

    mysqli_close($conn);

?>
