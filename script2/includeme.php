<?php
date_default_timezone_set("Asia/Taipei");
    error_reporting(0);
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

    function scrap_page2($scrap_url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $scrap_url);
        curl_setopt($curl, CURLOPT_REFERER, $scrap_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($curl);
        curl_close($curl);


        // Create a DOM object
        $html = new simple_html_dom();
        // Load HTML from a string
        $html->load($str);
        //$html = file_get_html($scrap_url);

        if ($html && is_object($html) && isset($html->nodes)) {

            $items = $html->find("table tr");

        // loop through items on current page
            foreach ($items as $item) {
                scrap_single2($item);
            }

            $html->clear();
        }
    }

    function scrap_single2($html) {

        global $delimiter,$texttowrite, $db_insert;

       
        $current_date = date("Y-m-d H:i:s");
        $current_justdate = date("Y-m-d");
        
        if ($html && is_object($html) && isset($html->nodes)) {

            $td0 = get_value($html, "td", 0, "text");
            $td1 = get_value($html, "td", 1, "text");
            $td2 = get_value($html, "td", 2, "text");
            $td3 = get_value($html, "td", 3, "text");

            if ($html->find("td", 2)) {

                $csv_line = "";
                $stock1 = "";
                $stock2 = "";
                $sbl1 = "";
                $sbl2= "";

                    if (strlen($td0) > 2) {
                        $csv_line .= quote_string($td0." TT");
                        $csv_line .= $delimiter . quote_string($td1);
                        $stock1 = $td0." TT";
                        $sbl1 = intval(str_replace(",", "", $td1));

                        if ( strlen($td1) < 15) {
                            $db_insert .= "('".$stock1."', '".$sbl1."', '".$current_date."', '".$current_justdate."'),";
                        }                    

                    }
                    if (strlen($td2) > 2 && strlen($td3) < 15 ){
                        $csv_line .= "\n" . quote_string($td2." TT");
                        $csv_line .= $delimiter . quote_string($td3);
                        $stock2 = $td2." TT";
                        $sbl2 = intval(str_replace(",", "", $td3));

                        $db_insert .= "('".$stock2."', '".$sbl2."', '".$current_date."', '".$current_justdate."'),";

                    }

            }

            $html->clear();
        }

        

    }
    


    // set display errors status
    ini_set('display_errors', 0); // 1-turn on all error reporings 0-turn off all error reporings
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

    // change max execution time to unlimitied
    ini_set('max_execution_time', 0);

    

    // include simple html dom parser
    require_once "simple_html_dom.php";
    
    // scrap url
    $scrap_url = "www.tse.com.tw/SBL/TWT96U?response=html"; //http://www.tse.com.tw/ch/trading/SBL/TWT96U/TWT96U.php";

    // field delimiter in output file
    $delimiter = ",";
    date_default_timezone_set('Asia/Taipei');
    // output filename
    
    

    //$file = "../output/script2/outputSBL_" .date('m-d-Y_h-i-s a'). ".csv";

    // open file for writing final results
    //$handler = @fopen($file, "w");
    
    /*$texttowrite = "Stock Code" . $delimiter .
    "Real Time Available Volume for SBL Short Sales" . $delimiter .
    "Last Modify" . $delimiter .
    "\n";*/
    $current_date = date("Y-m-d");

    $db_hist_insert_start = "INSERT INTO `sbl_info_history` (`ticker`, `sbl_qty`, `created_at`, `date`) VALUES ";
    //$db_insert_start = " INSERT INTO `avail_info` (`ticker`, `avail`, `last_modify`, `created_at`) VALUES ";

    $db_insert_start = "INSERT INTO `sbl_info` (`ticker`, `sbl_qty`, `created_at`, `date`) VALUES ";

    $db_insert = "";

    scrap_page2($scrap_url);

    $db_hist_insert = $db_hist_insert_start . substr($db_insert, 0, strlen($db_insert) -1);

    $db_hist_insert_query = mysqli_prepare($conn, $db_hist_insert);

    //echo $db_insert;

    if (!mysqli_stmt_execute($db_hist_insert_query)) {
        echo("Error description: " . mysqli_error($conn));
    }

    if (!mysqli_stmt_execute(mysqli_prepare($conn, "Delete from `sbl_info` where date = '".$current_date."';"))) {
        echo("Error description: " . mysqli_error($conn));
    }

    //$db_insert = $db_insert_start . substr($db_insert, 0, strlen($db_insert) -1)."; ";

    $db_insert = $db_insert_start . substr($db_insert, 0, strlen($db_insert) -1).";";

    $db_insert_query = mysqli_prepare($conn, $db_insert);

    //echo $db_insert;

    if (!mysqli_stmt_execute($db_insert_query)) {
        echo("Error description: " . mysqli_error($conn));
    }
    
    //fwrite($handler, $texttowrite);
    //fclose($handler);


    // define functions
    
    
    echo "Script2 - ".($starttime - time());
        
    echo "done 2";



    $myfile = fopen("script2.txt", "a") or die("Unable to open file!");
    fwrite($myfile, "\n\rDone at " . date("Y-m-d H:i:s"));
    fclose($myfile);

    // send email
    mail("dibwas@gmail.com","Script 2 done at ".date("Y-m-d H:i:s"),"Script 2 done at ".date("Y-m-d H:i:s"));

    mysqli_close($conn);

?>

