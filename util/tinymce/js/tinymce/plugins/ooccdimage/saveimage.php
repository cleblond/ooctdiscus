<?php


require_once "../../../../../../../config.php";

use \Tsugi\Core\LTIX;

// Retrieve the launch data if present
//$LTI = LTIX::requireData();
$LTI = LTIX::requireData();
$p = $CFG->dbprefix;


$img = $_POST['imgBase64'];
$filename = $_POST['fname'];
$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace(' ', '+', $img);
$filedata = base64_decode($img);
//saving
//$filename = 'img'.uniqid().'.png';
//file_put_contents($filename, $filedata);

//echo $_SESSION["RF"]["subfolder"];

file_put_contents('/var/www/html/tsugi/mod/openochem/uploads/'. $_SESSION["RF"]["subfolder"].'/'.$filename, $filedata);
