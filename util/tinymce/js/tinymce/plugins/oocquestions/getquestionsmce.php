<?php

require_once "../../../config.php";


use \Tsugi\UI\Table;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;


// Retrieve the launch data if present
$LTI = LTIX::requireData();
//$LTI = LTIX::getConnection();
$p = $CFG->dbprefix;


//include("../groups_class.php");
//$grp = new Groups();

//var_dump($_GET);

///since angular sends json get the raw output instead
$postdata = file_get_contents("php://input");




//echo "CATID=$catid";


$catid = $_GET['catid'];
//$myonly = $_GET['myonly'];
$myonly = 2;
//var_dump($postdata);
$request = json_decode($postdata);
//var_dump($request);
//$catid = $request->catid;
//$catid = intval($_POST['catid']);

//$myonly = intval($request->myonly);
//$myonly = intval($_POST['myonly']);

//$user_id = intval($request->userid);
$user_id = $USER->id;
//echo $catid;


//$cat_id = $_POST['params'];
//echo $cat_id;
//echo "user_id=".$user_id;



if ($myonly == '1') {  //users and users group questions
    //echo "HERE";
    //echo "catid-=".$catid;

    $grpcats = $grp->user_group_categories($USER->id);

    
    //echo "grpcats=".$grpcats;
    //$group_id_csv = $grp->user_groups($USER->id);
    //$query="select question_id, question_title, question_text, tags, question_type, created_at, updated_at from {$p}eo_questions where user_id = :UID AND category_id = :CID ORDER BY updated_at DESC";
    $query="select question_id, question_title, tags, question_type, created_at, updated_at from {$p}eo_questions where user_id = :UID AND category_id = :CID ORDER BY updated_at DESC";
    
        $pdoarray = array(
                    ':CID' => $catid,
                    ':UID' => $user_id
                  );
    
    
    
    //echo $group_id_csv;
    if ($grpcats) {
    $group_cat_ids = explode(',',$grpcats);
        if (in_array($catid, $group_cat_ids)) {
          //$query="select question_id, question_title, question_text, tags, question_type, created_at, updated_at from {$p}eo_questions where category_id = $catid ORDER BY updated_at DESC";
          $query="select question_id, question_title, tags, question_type, created_at, updated_at from {$p}eo_questions where category_id = $catid ORDER BY updated_at DESC";
          
                  $pdoarray = array(
                    ':CID' => $catid
                  );
          
          
          
        }   
    }
   
} else {
//$query="select question_id, question_text, question_type, created_at, updated_at from {$p}eo_questions where (share=1 and category_id = $catid) or user_id = $user_id";


//$query="select user_id, question_id, question_title, question_text, tags, question_type, created_at, updated_at from {$p}eo_questions where (share = 1 or user_id = $user_id) and category_id = $catid";
//this was sql for when your visibility controlled questions, since changing to category based sharing this is obsolete
//$query="select user_id, question_id, question_title, question_text, tags, question_type, created_at, updated_at from {$p}eo_questions where (share = 1 or user_id = $user_id) and category_id = $catid";
    //$query="select user_id, question_id, question_title, question_text, tags, question_type, created_at, updated_at from {$p}eo_questions where category_id = :CID ORDER BY updated_at DESC";
    $query="select user_id, question_id, question_title, tags, question_type, created_at, updated_at from {$p}eo_questions where category_id = :CID AND question_type IN ('structure', 'mechanism', 'numeric', 'alphanumeric', 'mcq') ORDER BY updated_at DESC";



 $pdoarray = array(
                    ':CID' => $catid
                  );




}

//echo $query;
//echo $query;
//$query="select question_id, question_text, question_type, category_id from {$p}eo_questions where share=1";




$result = $PDOX->allRowsDie($query, $pdoarray);

//$result = $mysqli->query($query) or die($mysqli->error.__LINE__);

$arr = array();

$num_rows = count($result);

if($num_rows > 0) {

	//while($row = $result->fetch_assoc()) {
        foreach($result as $row){

		$arr[] = $row;	
	}

}
# JSON-encode the response
$json_response = json_encode($arr);
//echo "here";
// # Return the response
header('Content-Type: application/json');
echo $json_response;
?>
