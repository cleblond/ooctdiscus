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

$myonly = '2';
?>
<!DOCTYPE html>
<!-- This file expects $myonly (1=show users questions);  -->
<!-- <html > -->

<!-- <html ng-app="myApp" lang="en"> -->
<!--<head> -->
    <meta charset="utf-8">
 <!--   <link href="css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="../../../../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../../../../css/eocustom.css" rel="stylesheet">
    <link href="../../../../../js/jquery-ui.min.css" rel="stylesheet">
    <link href="../../../../../js/chosen/chosen.min.css" rel="stylesheet" />
    <style type="text/css">
    ul>li, a{cursor: pointer;}
    
    
    
    </style>
<br/>
<br/>
<div class="container-fluid" ng-app="myApp" ng-app lang="en">
<div ng-controller="questionsCrtl">

    <div class="row">
    <div class="col-sm-3"> <!-- search boxes -->


            <input name="catid" id="catid" type="hidden" ng-model="catid" value="<?= $catid ?>" />



              <div class="well well-sm">
              <h4>Search</h4>             
              <label><?=__('Category')?></label>
                <?php

                
                $group_id_csv = $grp->user_groups($USER->id);
                //echo "group_id_csv=".$group_id_csv;
                if ($group_id_csv != "") {
                $sql = "SELECT category_id, parent_id, category_name FROM eo_categories WHERE user_id = ".$USER->id .  " OR group_id IN (".$group_id_csv.") or share = 1";
                } else {
                $sql = "SELECT category_id, parent_id, category_name FROM eo_categories WHERE user_id = ".$USER->id." or share = 1";
                }
                
                $cats = $PDOX->allRowsDie($sql);
                
                
                
                    
                $checked = '';
		        echo '<select id="searchcat" ng-model="searchcat"  class="form-control" >';
                       $i=1;
                 echo "<option value='0' selected='selected'>All</option>";
		        foreach ($cats as $cat) {
                           //echo "i=".$i;
		            //echo $parent_id;
		            if ($catid == "0") {$catid=$cat['category_id'];}
		            //if ($cat['category_id'] == $catid && $catid !== "0") {$checked = 'selected="selected"';}
		            echo "<option value='" . $cat['category_id'] . "' ".$checked.">" . $cat['category_name'] . "</option>";
		            $checked = '';
                            //$i=$i+1;
		          }
		        echo "</select>";

                    ?>


              <?php include "../../../../../question_type/tags.php"; ?>
              
                           <label ><?=__('Difficulty')?>&nbsp;&nbsp;</label>
                        <!-- <input type="text" name="question_type" value="<?= $type ?>" readonly><br/>  -->
                              <select ng-model="difficulty"  class="form-control custom-select" name="difficulty" id="difficulty">
                                <option value="0" selected="selected"><?=__('Not Set')?></option>
                                <option value="de1">Difficulty = 1</option>
                                <option value="de2">Difficulty = 2</option>
                                <option value="de3">Difficulty = 3</option>
                                <option value="de4">Difficulty = 4</option>
                                <option value="de5">Difficulty = 5</option>
                                <option value="dge2">Difficulty &gt;= 2</option>
                                <option value="dge3">Difficulty &gt;= 3</option>
                                <option value="dge4">Difficulty &gt;= 4</option>
                                <option value="dle2">Difficulty &lt;= 2 </option>
                                <option value="dle3">Difficulty &lt;= 3 </option>
                                <option value="dle4">Difficulty &lt;= 4</option>
                                
                                
                              </select> 

                            <label ><?=__('Bloom Taxonomy')?>&nbsp;&nbsp;</label>
                              <select ng-model="bloomtax" class="form-control custom-select"   name="taxonomy" id="taxonomy">
                                <option value="0" selected><?=__('Not Set')?></option>
                                <option value="1"><?=__('Knowledge')?></option>
                                <option value="2"><?=__('Comprehension')?></option>
                                <option value="3"><?=__('Application')?></option>
                                <option value="4"><?=__('Analysis')?></option>
                                <option value="5"><?=__('Synthesis')?></option>
                                <option value="6"><?=__('Evaluation')?></option>
                              </select>
                              
                              
                              <br/>
                              <div class="button-wrapper">
                              <button type="button" class="btn btn-basic" ng-click="updateTagSearch()">Search</button>
                              <button type="button" class="btn btn-basic" ng-click="resetSearch()" >Reset</button>
                              <button id='add_random_question' type="button" class="btn btn-basic btn-block adaptivequiz" title="Add Random Question with these criteria.">Add Random Question</button>
                              </div>
                              
                              
             
              </div>
              
              
              <div class="well well-sm">
                 <label><?=__('Search All Text')?></label>
                    <div class="input-group">
                 
                  
                    <input type="text" ng-model="searchall" class="form-control">
                    <!-- <input type="text" ng-model="searchcat" ng-change="filter()" placeholder="Filter" class="form-control" />  -->
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default" ng-click="updateSearch()">Search</button>
                    </span>
                  </div>
              </div>
              
              
              
              
       </div>
    
    
    
       <div class="col-sm-9">
           <div class="row">
             <div class="col-sm-12" >
               
                   <!-- <input type="text" ng-model="search" ng-change="filter()" placeholder="Filter" class="form-control" />  -->
                
                
               <!-- 
                        <a href='#' id='add_random_question'>Add Random From this category</a>
                      <br/><select id="rand_filter" name="rand_filter" ng-model="rand_filter">
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
                      
                      -->
              </div>
           </div>
            

    
    
    

              
             

    
        <div class="row">
            <div class="col-md-6"><?=__('Filter')?>
                <input type="text" ng-model="search" ng-change="filter()" placeholder="Filter Search Results" class="form-control" />
               <!-- <input type="text" ng-model="searchcat" ng-change="filter()" placeholder="Filter" class="form-control" />  -->
            </div>
        
        
            <div class="col-md-6"><?=__('Page Size')?>
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
           <!--     <input type="hidden" id="ajaxurl" name="ajaxurl" value="<?php echo(addSession('ajax/getquestions_adaptive.php'))?>"> -->
              <input type="hidden" id="ajaxurl" name="ajaxurl" value="<?php echo(addSession('../../../../../ajax/getquestions.php'))?>">
            </div>
        </div>
          
        
      

        
        
        
        
       <div class="row">
            <div class="col-sm-12">
                Filtered {{ filtered.length }} of {{ totalItems}} total questions
            </div>
       </div>
       
       



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
                <tr class="question_row" style="cursor: pointer;" ng-repeat="data in filtered = (list | filter:search | orderBy : predicate :reverse) | startFrom:(currentPage-1)*entryLimit | limitTo:entryLimit">
                <!-- <tr ng-repeat="data in filtered = (list | filter:search | orderBy : predicate :reverse) | startFrom:(currentPage-1)*entryLimit | limitTo:entryLimit"> -->
                  <!--  <td>{{data.question_id}}</td> -->
                    <td style="display:none;" class="question_id">{{data.question_id}}</td>
                    <td><a id="{{data.question_id}}" data-qtype="{{data.question_type}}" data-qtext="{{data.question_text}}" class="question_element">{{data.question_title}}</a></td>
                    <td>{{data.question_type}}</td>
              <!--      <td>{{data.created_at}}</td>  -->
                    <td class="updated">{{data.updated_at}}</td>
                    <td><a href="previewquestion_engine.php?question_id={{data.question_id}}" target="_blank" title="Preview Question"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'slide'" ><a href="question_type/slide/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'mcq'" ><a href="question_type/mcq/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                     <td ng-if="data.question_type == 'choice'" ><a href="question_type/choice/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    
                    <td ng-if="data.question_type == 'formula'" ><a href="question_type/formula/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'lewis-electrons' || data.question_type == 'lewis-charges'" ><a href="question_type/chemdoodle/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'numeric' || data.question_type == 'alphanumeric'" ><a href="question_type/shortanswer/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    
                    
                    <td ng-if="data.question_type == 'structure'" ><a href="question_type/chemdoodle/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    <td ng-if="data.question_type == 'mechanism'" ><a href="question_type/chemdoodle/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'newman'" ><a href="question_type/newman/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                    <td ng-if="data.question_type == 'webmostructure'" ><a href="question_type/webmo/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td>
                    
                     <td ng-if="data.question_type == 'webmoconformer'" ><a href="question_type/webmo/editquestion.php?question_id={{data.question_id}}"  title="Edit Question"><i class="fa fa-cog" aria-hidden="true"></i></a></td> 
                    

                    
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

<script src="../../../../../js/jquery-3.1.1.min.js"></script>
<!--  <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
  <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
<script src="../../../../../js/angular.min.js"></script>
<script src="../../../../../js/angular-sanitize.min.js"></script>
<script src="../../../../../js/ui-bootstrap-tpls-0.10.0.min.js"></script>
<script src="../../../../../reveal/app/tmce_app.js"></script>
<script type="text/javascript">

$(document).ready(function(){

  $( "#add_random_question" ).on('click', function(e) {
           
            catid = $('#searchcat').val();
            //cattitle = $('#searchcat').find(":selected").html();
            tags = $('#tags').val();
            diff = $('#difficulty').val();
            btax = $('#taxonomy').val();
            
            if (tags == null) {
                    var tagscsv = '';
            } else { 
                    //var tags = JSON.stringify($scope.searchtags);
                    var tagscsv = tags.join(":");
            }
           
            randcat = 'rc' + catid +"-b" + btax + "-" + diff + "-t"+tagscsv;

           
           html = '<div class="question_ph_div mceNonEditable"  data-qtype="rand" data-qid="'+randcat+'">Question Title: ' +e.target.innerHTML+ '<br/>Question Type: Random - ' +randcat+ '</div>';
           parent.tinymce.activeEditor.insertContent(html);
           

       //}


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

  <script src="../../../../../js/chosen/chosen.jquery.js"></script>
  <script>
      $( document ).ready(function() {
          $('.tags-select').chosen({width: "100%", include_group_label_in_selected: true});
      });
  </script>

<!-- <script type="text/javascript" src="../../../../../js/functions.js"></script>      --> 
 <!--   </body>
</html>  -->
