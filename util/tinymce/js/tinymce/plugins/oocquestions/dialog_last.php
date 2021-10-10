<?php
require_once "../../../../../../../../tsugi/config.php";

use \Tsugi\Core\LTIX;

// Retrieve the launch data if present
$LTI = LTIX::requireData();
$p = $CFG->dbprefix;
$displayname = $USER->displayname;

//var_dump($_SESSION);
$sesspieces = explode('=', addSession(""));
$phpsessid = $sesspieces[1];


$catsql = "SELECT category_id FROM {$p}eo_categories WHERE user_id = '".$USER->id."'";     
$catrow = $PDOX->rowDie($catsql);
$catid = $catrow['category_id'];



include("../../../../../groups_class.php");
$grp = new Groups();


?>
<!-- <!DOCTYPE html>  -->
<!-- This file expects $myonly (1=show users questions);  -->
<!-- <html > -->



<html ng-app="myApp" lang="en">
<!--<head> -->
    <meta charset="utf-8">
    <link href="../../../../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../../../../js/jquery-ui.min.css" rel="stylesheet">
    <style type="text/css">
    ul>li, a{cursor: pointer;}
    

    
    </style>
<!-- </head> -->
<!-- <body>  -->

<div class="container-fluid" ng-app="myApp" ng-app lang="en">
<legend>Question Bank</legend>
<p><strong>Click a question title to add it to a slide!</strong></p>
<div ng-controller="questionsCrtl">


 <div class="row">
       
         <div class="col-sm-4" >
            <div class="well well-sm">
               <label><?=__('Browse Category')?></label>
               <?php
               
               
               
                $group_id_csv = $grp->user_groups($USER->id);
                //echo "group_id_csv=".$group_id_csv;
                if ($group_id_csv != "") {
                $sql = "SELECT category_id, parent_id, category_name FROM eo_categories WHERE user_id = ".$USER->id .  " OR group_id IN (".$group_id_csv.") or share = 1";
                } else {
                $sql = "SELECT category_id, parent_id, category_name FROM eo_categories WHERE user_id = ".$USER->id." or share = 1";
                }
                //echo $sql;
                //var_dump($grp->user_groups($USER->id));

                $cats = $PDOX->allRowsDie($sql);
                
               
                //$cats = $PDOX->allRowsDie("SELECT category_id, parent_id, category_name FROM eo_categories");
                //$cats = $PDOX->allRowsDie("SELECT category_id, parent_id, category_name FROM {$p}eo_categories INNER JOIN {$p}eo_permissions on {$p}eo_categories.user_id = {$p}eo_permissions.user_id WHERE ((permission & 2) > 0)");
                 //var_dump($cats);
                $checked = '';
		    echo "<select id='searchcat' ng-model='searchcat' ng-change='changedValue(searchcat);'  class='form-control' >";
                   //$i=1;
            
		    foreach ($cats as $cat) {
                 
		        if ($cat['category_id'] == $catid) {$checked = 'selected="selected"';}
		        
		        echo "<option value='" . $cat['category_id'] . "' ".$checked.">" . $cat['category_name'] . "</option>";
		        
		        $checked = '';
                       
		      }
		    echo "</select><br/>";



                    ?>
               <!-- <input type="text" ng-model="search" ng-change="filter()" placeholder="Filter" class="form-control" />  -->
            <input name="catid" id="catid"  type="hidden" ng-model="catid" value="<?= $catid ?>"/>
            
            
                    <a href='#' id='add_random_question'>Add Random From this category</a>
                  <br/><select id="rand_filter" name="rand_filter">
                                     <option selected disabled value="none">Choose Filter</option>
                                     <option value="none">No Restrictions</option>
                                     <option value="de1">Difficulty = 1</option>
                                     <option value="de2">Difficulty = 2</option>
                                     <option value="de3">Difficulty = 3</option>
                                     <option value="de4">Difficulty = 4</option> 
                                     <option value="de5">Difficulty = 5</option>                                                                                      
                                     <option value="dge2">Difficulty >= 2</option>
                                     <option value="dge3">Difficulty >= 3</option>
                                     <option value="dge4">Difficulty >= 4</option>
                                     <option value="dle2">Difficulty <= 2 </option>
                                     <option value="dle3">Difficulty <= 3 </option>
                                     <option value="dle4">Difficulty <= 4</option>
                                     <option value="t1">Taxonomy = 1</option>
                                     <option value="t2">Taxonomy = 2</option>
                                     <option value="t3">Taxonomy = 3</option>
                                     <option value="t4">Taxonomy = 4</option>     
                                     <option value="t5">Taxonomy = 5</option>                                                       
                  </select>
            
            
              </div>
            </div>
    
    
    
             <div class="col-sm-4">
                <div class="well well-sm">
                 <label><?=__('Search All')?></label>
                    <div class="input-group">
                 
                  
                    <input type="text" ng-model="searchall" class="form-control">
                    <!-- <input type="text" ng-model="searchcat" ng-change="filter()" placeholder="Filter" class="form-control" />  -->
                    
                    <button type="button" class="btn btn-xs btn-default" ng-click="updateSearch()">Search</button>
                    
                  </div>
                </div>
              </div>
              
              
              <div class="col-sm-4">
              <div class="well well-sm">
              <?php $tagselsize=5; include "../../../../../question_type/tags.php"; ?>
              <button type="button" class="btn btn-xs btn-default" ng-click="updateTagSearch()">Search</button>
              </div>
              </div>
    
    
    
    
    
    </div>







    <div class="row">
        <div class="col-sm-4">PageSize:
            <select ng-model="entryLimit" class="form-control">
                <option>5</option>
                <option>10</option>
                <option>20</option>
                <option>50</option>
                <option>100</option>
            </select>
    <!--   <input name="catid" id="catid"  type="hidden" ng-model="catid" value="<?= $catid ?>"/> -->
       <input type="hidden" name="myonly" id="myonly"  ng-model="myonly" class="form-control" value="<?= $myonly ?>"/>
       <input type="hidden" name="userid" id="userid"  type="hidden" ng-model="userid" value="<?= $USER->id ?>" class="form-control"/>
            <!--  <input type="hidden" id="ajaxurl" name="ajaxurl" value="<?php echo(addSession('../../../../../reveal/ajax/getquestionsmce.php'))?>"> -->
              <input type="hidden" id="ajaxurl" name="ajaxurl" value="<?php echo(addSession('../../../../../ajax/getquestions.php'))?>">
              
        </div>
        <div class="col-sm-4">Filter:
            <input type="text" ng-model="search" ng-change="filter()" placeholder="Filter" class="form-control" />
           <!-- <input type="text" ng-model="searchcat" ng-change="filter()" placeholder="Filter" class="form-control" />  -->
        </div>
        <div class="col-sm-4" >Category:
               <?php
               
               
               
                //$group_id_csv = $grp->user_groups($USER->id);
                //echo "group_id_csv=".$group_id_csv;
                //if ($group_id_csv != "") {
                //$sql = "SELECT category_id, parent_id, category_name FROM eo_categories WHERE user_id = ".$USER->id .  " OR group_id IN (".$group_id_csv.") or share = 1";
                //} else {
                $sql = "SELECT category_id, parent_id, category_name FROM eo_categories WHERE user_id = ".$USER->id." or share = 1";
                //}
                //echo $sql;
                //var_dump($grp->user_groups($USER->id));

                $cats = $PDOX->allRowsDie($sql);
                
               
                //$cats = $PDOX->allRowsDie("SELECT category_id, parent_id, category_name FROM eo_categories");
                //$cats = $PDOX->allRowsDie("SELECT category_id, parent_id, category_name FROM {$p}eo_categories INNER JOIN {$p}eo_permissions on {$p}eo_categories.user_id = {$p}eo_permissions.user_id WHERE ((permission & 2) > 0)");
                 //var_dump($cats);
                $checked = '';
		echo "<select id='searchcat' ng-model='searchcat' ng-change='changedValue(searchcat);'  class='form-control' >";
               //$i=1;
        
		foreach ($cats as $cat) {
             
		    if ($cat['category_id'] == $catid) {$checked = 'selected="selected"';}
		    
		    echo "<option value='" . $cat['category_id'] . "' ".$checked.">" . $cat['category_name'] . "</option>";
		    
		    $checked = '';
                   
		  }
		echo "</select><br/>";
                ?>
           <!-- <input type="text" ng-model="search" ng-change="filter()" placeholder="Filter" class="form-control" />  -->
                           <input name="catid" id="catid"  type="hidden" ng-model="catid" value="<?= $catid ?>"/>
        
        
        <!--random question in slidedeck -->
          <a href='#' id='add_random_question'>Add Random From this category</a>
          <br/><select id="rand_filter" name="rand_filter">
                             <option selected disabled value="none">Choose Filter</option>
                             <option value="none">No Restrictions</option>
                             <option value="de1">Difficulty = 1</option>
                             <option value="de2">Difficulty = 2</option>
                             <option value="de3">Difficulty = 3</option>
                             <option value="de4">Difficulty = 4</option> 
                             <option value="de5">Difficulty = 5</option>                                                                                      
                             <option value="dge2">Difficulty >= 2</option>
                             <option value="dge3">Difficulty >= 3</option>
                             <option value="dge4">Difficulty >= 4</option>
                             <option value="dle2">Difficulty <= 2 </option>
                             <option value="dle3">Difficulty <= 3 </option>
                             <option value="dle4">Difficulty <= 4</option>
                             <option value="t1">Taxonomy = 1</option>
                             <option value="t2">Taxonomy = 2</option>
                             <option value="t3">Taxonomy = 3</option>
                             <option value="t4">Taxonomy = 4</option>     
                             <option value="t5">Taxonomy = 5</option>                                                       
                             </select>
        
        </div>
        
        
        
        
        
       <div class="row">
        <div class="col-md-12">
            <h5>Filtered {{ filtered.length }} of {{ totalItems}} total questions</h5>
        </div>
       </div>
    </div>
    <!--
<div pagination="" page="currentPage" max-size="10" on-select-page="setPage(page)" boundary-links="true" total-items="filteredItems" items-per-page="entryLimit" class="pagination-small" previous-text="«" next-text="»"></div> -->



    <div class="row">
        <div class="col-md-12" ng-show="filteredItems > 0">
          <div id="allques">
            <table id='tab2' data-escape="true" class="table table-striped table-hover table-condensed table-responsive table-bordered">
            <thead>
        <!--    <th>Question Id<a ng-click="sort_by('question_id');"><i class="glyphicon glyphicon-sort"></i></a></th> -->
            <th style="display:none;">Question Id<a ng-click="sort_by('question_text');"><i class="glyphicon glyphicon-sort"></i></a></th>
            <th>Question Title&nbsp;<a ng-click="sort_by('question_text');"><i class="glyphicon glyphicon-sort"></i></a></th>
            <th>Question Type&nbsp;<a ng-click="sort_by('question_type');"><i class="glyphicon glyphicon-sort"></i></a></th>
     <!--       <th>Category<a ng-click="sort_by('category_id');"><i class="glyphicon glyphicon-sort"></i></a></th> -->
     <!--       <th>State&nbsp;<a ng-click="sort_by('state');"><i class="glyphicon glyphicon-sort"></i></a></th> -->
     <!--       <th>Created&nbsp;<a ng-click="sort_by('postalCode');"><i class="glyphicon glyphicon-sort"></i></a></th> -->
            <th>Updated&nbsp;<a ng-click="sort_by('updated_at');"><i class="glyphicon glyphicon-sort"></i></a></th>
         <!--   <th>Credit Limit&nbsp;<a ng-click="sort_by('creditLimit');"><i class="glyphicon glyphicon-sort"></i></a></th> -->
            </thead>
            <tbody id="qelement_container" class="connectedSortable"> 
                <tr class="question_row" style="cursor: pointer;" ng-repeat="data in filtered = (list | filter:search | orderBy : '-updated_at' : reverse) | startFrom:(currentPage-1)*entryLimit | limitTo:entryLimit">
                  <!--  <td>{{data.question_id}}</td> -->
                    <td style="display:none;" class="question_id">{{data.question_id}}</td>
                   <!--  <td ng-bind-html="data.question_title"></td> -->
                    <td><a id="{{data.question_id}}" data-qtype="{{data.question_type}}" class="question_element">{{data.question_title}}</a></td>
                    <td>{{data.question_type}}</td>
              <!--      <td>{{data.created_at}}</td>  -->
                    <td class="updated">{{data.updated_at}}</td>
                    
                    <!--
                    
                    
                    <td><a href="previewquestion.php?question_id={{data.question_id}}" target="_blank" title="Preview Question"><i class="fa fa-search" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'slide'" ><a href="question_type/slide/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'mcq'" ><a href="question_type/mcq/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'formula'" ><a href="question_type/formula/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'numeric' || data.question_type == 'alphanumeric'" ><a href="question_type/shortanswer/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    
                    
                    <td ng-if="data.question_type == 'structure'" ><a href="question_type/chemdoodle/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    <td ng-if="data.question_type == 'mechanism'" ><a href="question_type/chemdoodle/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'newman'" ><a href="question_type/newman/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'webmostructure'" ><a href="question_type/webmo/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                     <td ng-if="data.question_type == 'webmoconformer'" ><a href="question_type/webmo/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td> 
                    
                    
                    -->
                    
                    
                    
                    
                </tr>
            </tbody>
            </table>
           </div>
        </div>
        <div class="col-md-12" ng-show="filteredItems == 0">
            <div class="col-md-12">
                <h4>No questions found</h4>
            </div>
        </div>
                <input type="hidden" id="phpsessid" name="phpsessid" value="<?=$phpsessid?>">
        <div class="col-md-12" ng-show="filteredItems > 0">   
 
        <!--    <div pagination="" page="currentPage" on-select-page="setPage(page)" boundary-links="true" total-items="filteredItems" items-per-page="entryLimit" class="pagination-small" previous-text="&laquo;" next-text="&raquo;"></div> -->

<div pagination="" page="currentPage" max-size="10" on-select-page="setPage(page)" boundary-links="true" total-items="filteredItems" items-per-page="entryLimit" class="pagination-small" previous-text="«" next-text="»"></div>
            
            
        </div>
    </div>
</div>
</div>



<script src="../../../../../js/jquery-3.1.1.min.js"></script>
<!--  <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="../../../../../js/angular.min.js"></script>
<script src="../../../../../js/angular-sanitize.min.js"></script>
<script src="../../../../../js/ui-bootstrap-tpls-0.10.0.min.js"></script>
<script src="../../../../../reveal/app/tmce_app.js"></script>
<script type="text/javascript">

$(document).ready(function(){

  $( "#add_random_question" ).on('click', function(e) {
      //qtype = $(this).attr("data-qtype");
       filter = $('#rand_filter').find(":selected").val();
       cat = $('#searchcat').find(":selected").val();
      
       randcat = 'rc'+cat+'-'+filter;
      
       
       html = '<div class="question_ph_div mceNonEditable"  data-qtype="rand" data-qid="'+randcat+'">Question Title: ' +e.target.innerHTML+ '<br/>Question Type: Random - ' +randcat+ '</div>';
       parent.tinymce.activeEditor.insertContent(html);
       parent.tinymce.activeEditor.windowManager.close();
  
  
  });



  //js to handle adding clicked question to page
  $( "#qelement_container" ).on('click', '.question_element', function(e) {
  //console.log(e);
   //console.log(e.target.innerHTML);
   //   console.log(e.target.id);
   //console.log($(e.target).next());
   //console.log($(this).attr("data-qtype"));
   qtype = $(this).attr("data-qtype");
   
   html = '<div class="question_ph_div mceNonEditable" data-qtype="'+qtype+'" data-qid="'+e.target.id+'">Question Title: ' +e.target.innerHTML+ '<br/>Question Type: ' +qtype+ '</div>';
   parent.tinymce.activeEditor.insertContent(html);
   parent.tinymce.activeEditor.windowManager.close();
  });
});
</script>

<!-- <script type="text/javascript" src="../../../../../js/functions.js"></script>      --> 
 <!--   </body>
</html>  -->
