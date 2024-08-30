<?php

use \Tsugi\Core\Mail;

class Notify {
   
//notify users
//object_user_id - person recieveing the notification usually owner of questio(comments) group, activity etc.
//user_id - user who performed action (commented, created question/activity, 
//object_id (question_id, activity_id, group_id)
//object newquetion, newactivity, quesition??, joingroup_request, 


function notify_user($object_user_id, $user_id, $object_id, $object) {
global $p, $PDOX;

      if ($object_user_id !== $user_id) {
      
          /*
          $notsql = "INSERT INTO {$p}eo_notifications
          (postby_user_id, object_user_id, object_id, object)
          VALUES('{$user_id}', '{$object_user_id}', '{$object_id}', '{$object}')";
                 
          $query = $PDOX->queryDie($notsql);
          */
          
          
          
         $savequery = "INSERT INTO {$p}eo_notifications
		        (postby_user_id, object_user_id, object_id, object)
		        VALUES ((:PID), :OUID, :OID, :OBJ)";
	     $PDOX->queryDie($savequery,
		        array(
		            //':PID' => $instrow,
		            //':OUID' => $USER->id, 
		            ':OUID' => $object_user_id,
		            ':PID' => $user_id,
		            ':OID' => $object_id,
		            ':OBJ' => $object
                        )
	            );
	            
	            
	     $this->email_user ($object_user_id, $user_id, $object_id, $object);
          
          
          
          
          
      }
      
}     
      
      
//email the user of new OOC notification if permitted by user
function email_user ($object_user_id, $user_id, $object_id, $object) {
    global $PDOX, $p;
    //check if user wants an email notif

 
    //if ($settingsrow['email_notifs']) {

        //get users email address
        $row = $PDOX->rowDie(
        "SELECT email
        FROM {$p}lti_user
        WHERE user_id = :UID",
        array(':UID' => $object_user_id)
        );
    
         //$message = $this->notification_email_html ($object_user_id, $user_id, $object_id, $object);
         $message = "A new question was posted on Learn";
         
         $unsuscribe = "\r\n\r\n".__("Note: You are receiving this notification from OpenOChem because you have email notifications turned on in your settings.\r\nTo stop receiving notifications go to My Settings and turn off Email Notifications");
         $message = $message . $unsuscribe;
          $to = 'openochem@gmail.com';
          $subject = 'OpenOChem Notification';
          $cmd = "/usr/bin/php /var/www/html/tsugi/mod/openochem/bg_email.php $object_user_id $to '" . $subject . "' '" . $message . "'";
          //echo $cmd;
          $token = Mail::computeCheck($object_user_id);
          Mail::send($to, $subject, $message);
          

      
     //}
     
}


function execInBackground($cmd) {  
    if (substr(php_uname(), 0, 7) == "Windows"){  
        pclose(popen("start /B ". $cmd, "r"));   
    }else{
        //echo $cmd;
        exec($cmd . " > /dev/null 2>/dev/null &", $output);
        //var_dump($return_var);
        var_dump( $output);    
    }  
} 





function notify_followers($user_id, $object_id, $object){
    global $PDOX, $p;
	$sql = "select distinct follower_id from {$p}eo_following
			where user_id = '".$user_id."'";

			
    $users = $PDOX->allRowsDie($sql);
    
    
    foreach($users as $user) {
    
    
    
   
    $follower_id = $user['follower_id'];
    
    $this->notify_user($follower_id, $user_id, $object_id, $object);
   
    //$notsql = "INSERT INTO {$p}eo_notifications
    //      (postby_user_id, object_user_id, object_id, object)
    //      VALUES('{$user_id}', '{$follower_id}', '{$object_id}', '{$object}')";
                 
    //      $query = $PDOX->queryDie($notsql);
    
    }
}






function notification_email_html ($object_user_id, $user_id, $object_id, $object) {
    global $PDOX, $p;
                    $notifout = '';
                    
                    //get displaname of postedby_id 
                    $displaynamerow = $PDOX->rowDie("SELECT displayname FROM {$p}lti_user WHERE user_id = ". $user_id);
                    $displayname = $displaynamerow['displayname'];
                
                    if ($object == 'question') {
                        
                        $notifout .= $displayname.' commented on one of your questions on OpenOChem! qid='.$object_id;
                        $notifout.= "<a href='previewquestion_engine.php?question_id=".$object_id."'>Click here to view</a>";
                    
                    } elseif ($object == 'newquestion') {
                        $notifout .= $displayname.' '.__('shared a question!').' qid='.$object_id;
                    
                    } elseif ($object == 'activity') {
                        $notifout .= $displayname.' '.__('commented on your activity!').' qid='.$object_id;

                    } elseif ($object == 'reviewed_publ') {
                        $notifout .= $displayname.' '.__('reviewed your question and it has been approved!').' qid='.$object_id;
                        //$notifout.= "<a href='previewquestion_engine.php?question_id=".$object_id."'>Click here to view</a>";
                        
                    } elseif ($object == 'reviewed_need') {
                        $notifout .= $displayname.' '.__('reviewed your question and it requires revisions!').' qid='.$object_id;
                        //$notifout.= "<a href='previewquestion_engine.php?question_id=".$object_id."'>Click here to view</a>";
                    
                    } elseif ($object == 'newactivity') {
                        $notifout .= $displayname.' '.__('shared an activity!').' qid='.$object_id;
                        
                      
                    } elseif ($object == 'newthread') {
                        $notifout .= 'There\'s a new question on Learn';
                       
                    
                    } elseif ($object == 'joingroup_request') {
                     
                     $groupsql = "SELECT group_name from {$p}eo_groups WHERE group_id = :GID";
                     $grouprow = $PDOX->rowDie($groupsql, array(':GID' => $object_id));
                 
                    $notifout .= $displayname.' '. __('would like to join your group').' ('.$grouprow['group_name'].')';
                    }  elseif ($object == 'joingroup_allowed') {
                    
                    
                    $groupsql = "SELECT group_name from {$p}eo_groups WHERE group_id = :GID";
                    $grouprow = $PDOX->rowDie($groupsql, array(':GID' => $object_id));
                 
                    $notifout .= $displayname.' '.__('allowed you to join the').' '.$grouprow['group_name'].' '.__('group');
                    
                    }  else {  //must just be a message
                    
                    
                    $action = "previewquestion_engine.php?question_id=".$object_id;
                    $notifout .= __('Message from')." ".$displayname. "\r\n\r\n" . $object;
                    

                    }

                //}
                //$notifout .= '</div>';
                
                
                return $notifout; 

}





function notification_html ($notif, $user_id) {
    global $PDOX, $p;
                    $notifout = '';
                    
                    //get displaname of postedby_id 
                    $displaynamerow = $PDOX->rowDie("SELECT displayname FROM {$p}lti_user WHERE user_id = ". $notif['postby_user_id']);
                    $displayname = $displaynamerow['displayname'];
                
                    if ($notif['object'] == 'question') {
                        $action = "previewquestion_engine.php?question_id=".$notif['object_id'];
                        $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> commented on one of your questions! qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        View</a><span class="deletenotif"  data-mode="single" data-uid="'.$user_id.'" data-nid="'.$notif['notification_id'].'"><a href="#"> '.__('delete').'</a></span></li>';
                    
                    } elseif ($notif['object'] == 'newquestion') {
                        $action = "previewquestion_engine.php?question_id=".$notif['object_id'];
                        $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> '.__('shared a question!').' qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        View</a><span class="deletenotif"  data-mode="single" data-uid="'.$user_id.'" data-nid="'.$notif['notification_id'].'"><a href="#"> '.__('delete').'</a></span></li>';
                    
                    } elseif ($notif['object'] == 'activity') {
                        $action = "activityinfo.php?activity_id=".$notif['object_id'];
                        $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> '.__('commented on your activity!').' qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        View</a><span class="deletenotif"  data-mode="single" data-uid="'.$user_id.'" data-nid="'.$notif['notification_id'].'"><a href="#"> '.__('delete').'</a></span></li>';
                        
                        
                     } elseif ($notif['object'] == 'reviewed_publ') {
                     $action = "previewquestion_engine.php?question_id=".$notif['object_id'];
                     $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> has reviewed your question and it has been approved! It is now available in the Question bank!  Thank you for sharing! qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        View</a><span class="deletenotif"  data-mode="single" data-uid="'.$user_id.'" data-nid="'.$notif['notification_id'].'"><a href="#"> '.__('delete').'</a></span></li>';
              
                        
                    } elseif ($notif['object'] == 'reviewed_need') {
                     $action = "previewquestion_engine.php?question_id=".$notif['object_id'];
                     $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> has reviewed your question and has decided it need more work. Check the question comments for any reviewer comments and issues. qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        View</a><span class="deletenotif"  data-mode="single" data-uid="'.$user_id.'" data-nid="'.$notif['notification_id'].'"><a href="#"> '.__('delete').'</a></span></li>';
                    

                    } elseif ($notif['object'] == 'newactivity') {
                        $action = "activityinfo.php?activity_id=".$notif['object_id'];
                        $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> '.__('shared an activity!').' qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        '.__('View').'</a><span class="deletenotif"  data-mode="single" data-uid="'.$user_id.'" data-nid="'.$notif['notification_id'].'"><a href="#"> '.__('delete').'</a></span></li>';
                    
                    } elseif ($notif['object'] == 'joingroup_request') {
                     
                     $groupsql = "SELECT group_name from {$p}eo_groups WHERE group_id = :GID";
                     $grouprow = $PDOX->rowDie($groupsql, array(':GID' => $notif['object_id']));
                 
                    $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> would like to join your group ('.$grouprow['group_name'].')<span class="allowjoin" data-mode="single" data-postbyid="'.$notif['postby_user_id'].'" data-nid="'.$notif['notification_id'].'" data-gid="'.$notif['object_id'].'"><a href="#"> Allow</a></span><span class="deletenotif"  data-mode="single" data-uid="'.$user_id.'" data-nid="'.$notif['notification_id'].'"><a href="#"> '.__('delete').'</a></span></li>';
                    
                    }  elseif ($notif['object'] == 'joingroup_allowed') {
                    
                    
                    $groupsql = "SELECT group_name from {$p}eo_groups WHERE group_id = :GID";
                    $grouprow = $PDOX->rowDie($groupsql, array(':GID' => $notif['object_id']));
                 
                    $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> '.__('allowed you to join the').' '.$grouprow['group_name'].' '.__('group').'<span class="deletenotif"  data-mode="single" data-uid="'.$user_id.'" data-nid="'.$notif['notification_id'].'"><a href="#"> '.__('delete').'</a></span></li>';
                    
                    }  else {  //must just be a message
                    
                    
                    $action = "previewquestion_engine.php?question_id=".$notif['object_id'];
                    $notifout .= '<li class="notif list-group-item"><strong>'.__('Message from').' '.$displayname.'</strong><p>'.$notif['object'].'</p><span class="deletenotif"  data-mode="single" data-uid="'.$user_id.'" data-nid="'.$notif['notification_id'].'"><a href="#"> '.__('delete').'</a></span></li>';
                    

                    }
                
                
                return $notifout; 

}



function notification_html_block ($notif, $user_id) {
    global $PDOX, $p;
                    $notifout = '';
                    
                    //get displaname of postedby_id 
                    $displaynamerow = $PDOX->rowDie("SELECT displayname FROM {$p}lti_user WHERE user_id = ". $notif['postby_user_id']);
                    $displayname = $displaynamerow['displayname'];
                
                    if ($notif['object'] == 'question') {
                        $action = "previewquestion_engine.php?question_id=".$notif['object_id'];
                        $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> commented on one of your questions! qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        View</a></li>';
                    
                    } elseif ($notif['object'] == 'newquestion') {
                        $action = "previewquestion_engine.php?question_id=".$notif['object_id'];
                        $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> '.__('shared a question!').' qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        View</a></li>';
                    
                    } elseif ($notif['object'] == 'activity') {
                        $action = "activityinfo.php?activity_id=".$notif['object_id'];
                        $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> '.__('commented on your activity!').' qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        View</a></li>';
                        
                        
                     } elseif ($notif['object'] == 'reviewed_publ') {
                     $action = "previewquestion_engine.php?question_id=".$notif['object_id'];
                     $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> has reviewed your question and it has been approved! It is now available in the Question bank!  Thank you for sharing! qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        View</a></li>';
              
                        
                    } elseif ($notif['object'] == 'reviewed_need') {
                     $action = "previewquestion_engine.php?question_id=".$notif['object_id'];
                     $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> has reviewed your question and has decided it need more work. Check the question comments for any reviewer comments and issues. qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        View</a></li>';
                    

                    } elseif ($notif['object'] == 'newactivity') {
                        $action = "activityinfo.php?activity_id=".$notif['object_id'];
                        $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> '.__('shared an activity!').' qid='.$notif['object_id'].'<a title="'.__('Click to view').'" href="'.$action.'">  
                        '.__('View').'</a></li>';
                    
                    } elseif ($notif['object'] == 'joingroup_request') {
                     
                    $groupsql = "SELECT group_name from {$p}eo_groups WHERE group_id = :GID";
                    $grouprow = $PDOX->rowDie($groupsql, array(':GID' => $notif['object_id']));
                 
                    $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> would like to join your group ('.$grouprow['group_name'].')<a href="mygroups.php"> Manage</a></li>';
                    }  elseif ($notif['object'] == 'joingroup_allowed') {
                    
                    
                    $groupsql = "SELECT group_name from {$p}eo_groups WHERE group_id = :GID";
                    $grouprow = $PDOX->rowDie($groupsql, array(':GID' => $notif['object_id']));
                 
                    $notifout .= '<li class="notif list-group-item"><strong>'.$displayname.'</strong> '.__('allowed you to join the').' '.$grouprow['group_name'].' '.__('group').'</li>';
                    
                    }  else {  //must just be a message
                    
                    
                    $action = "previewquestion_engine.php?question_id=".$notif['object_id'];
                    $notifout .= '<li class="notif list-group-item"><strong>'.__('Message from').' '.$displayname.'</strong><p>'.$notif['object'].'</p></li>';
                    

                    }
 
                return $notifout; 

}





function output_notifications($user_id){
    global $PDOX, $p;



    ///Notifcations
            $notifout = '';
            $notifsql = "SELECT notification_id, postby_user_id, object_id, object FROM {$p}eo_notifications WHERE object_user_id = " . $user_id;
            $query = $PDOX->allRowsDie($notifsql);
            
            $totalnotifs = count($query);
            
            
            
            if ($query) {
                $notifout = '<div class="container"><h3>Notifications</h3><div class="list-group">';
                $notifout = '<span class="deletenotif"  data-mode="all" data-uid="'.$user_id.'" data-nid="0"><a href="#"><small>'.__('Clear All').'</small></a></span>';
                
                foreach ($query as $notif) {
                
                
                    $notifout .= $this->notification_html ($notif, $user_id);
                


                }
                $notifout .= '</div></div>';
          
            } else {
            //echo "Sorry you have no notifications";
            $notifout .= '<p><b>'.__('You have no notifications at this time!').'</b></p>';
            }

            return $notifout;


}


function output_notifications_for_group($user_id, $gid){
    global $PDOX, $p;



    ///Notifcations
            $notifout = '';
            $notifsql = "SELECT notification_id, postby_user_id, object_id, object FROM {$p}eo_notifications WHERE object_id = $gid AND object_user_id = $user_id AND object = 'joingroup_request'";
            $query = $PDOX->allRowsDie($notifsql);
            
            $totalnotifs = count($query);
            
            
            
            if ($query) {
                $notifout = '<div class="container"><h3>Notifications</h3><div class="list-group">';
                //$notifout = '<span class="deletenotif"  data-mode="all" data-uid="'.$user_id.'" data-nid="0"><a href="#"><small>'.__('Clear All').'</small></a></span>';
                
                foreach ($query as $notif) {
                
                
                    $notifout .= $this->notification_html ($notif, $user_id);
                


                }
                $notifout .= '</div></div>';
          
            } else {
            //echo "Sorry you have no notifications";
            $notifout .= '<p><b>'.__('You have no notifications at this time!').'</b></p>';
            }

            return $notifout;


}




function output_notifications_block($user_id){
    global $PDOX, $p;

    ///Notifcations
            $notifout = '';
            $notifsql = "SELECT notification_id, postby_user_id, object_id, object FROM {$p}eo_notifications WHERE object_user_id = " . $user_id;
            $query = $PDOX->allRowsDie($notifsql);
            
            $totalnotifs = count($query);          
            
            if ($query) {
                $notifout = '<h3>Notifications</h3><div class="list-group">';
                $notifout = '<a href="notifications.php"><small>'.__('View All').'</small></a>';
                
                foreach ($query as $notif) {
                
                
                    $notifout .= $this->notification_html_block ($notif, $user_id);
                


                }
                //$notifout .= '</div>';
          
            } else {
            //echo "Sorry you have no notifications";
            $notifout .= '<p><b>'.__('You have no notifications at this time!').'</b></p>';
            }

            return $notifout;


}


function notifstotal($user_id){
    global $PDOX, $p;

    ///Notifcations
            $notifout = '';
            $notifsql = "SELECT 1 FROM {$p}eo_notifications WHERE object_user_id = " . $user_id;
            $query = $PDOX->allRowsDie($notifsql);
            
            $totalnotifs = count($query);          
            
            return $notifout;


}














function delete_notif($uid, $nid){

    global $PDOX, $p;

	
		$sql = "delete from {$p}eo_notifications 
				where object_user_id=$uid and notification_id=$nid";
	    
	    $result = $PDOX->queryDie($sql);
}


function delete_all_notifs($uid){

    global $PDOX, $p;
		$sql = "delete from {$p}eo_notifications 
				where object_user_id=$uid";
	    
	    $result = $PDOX->queryDie($sql);
	    
}






}

