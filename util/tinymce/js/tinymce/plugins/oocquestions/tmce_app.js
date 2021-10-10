var app = angular.module('myApp', ['ui.bootstrap', 'ngSanitize']);


//code for sortable tables
app.controller('dragdroptable', function ($scope) {
    $scope.list = ["one", "two", "three", "four", "five", "six"];
});

//angular.bootstrap(document, ['myapp']);



app.filter('unsafe', function($sce) { return $sce.trustAsHtml; });


app.filter('startFrom', function() {
    return function(input, start) {
        if(input) {
            start = +start; //parse to int
            return input.slice(start);
        }
        return [];
    }
});


app.controller('questionsCrtl', function ($scope, $http, $timeout) {
        //$scope.catid = {};
        $scope.myonly = {};
        $scope.userid = {};
        $scope.searching = false;
        //$scope.data = Service.value;
        
        //console.log($scope.catid);
        //alert($('#catid').val());
        $scope.loading = true;
        //var ajaxgetquesurl = $('#ajaxgetquesurl').val() + '&catid='+$('#searchcat').val()+'&myonly='+$('#myonly').val();
        //console.log(ajaxgetquesurl);
        //console.log($('#searchcat').val());
        
        
        //$http.get(ajaxgetquesurl, {catid: $('#searchcat').val(), myonly: $('#myonly').val()}).success(function(data){
        //console.log('here1');
        var ajaxurl = $('#ajaxurl').val();
        console.log("here");
        $http.get(ajaxurl,  {
        params: {
            catid: $('#searchcat').val(),
            myonly: $('#myonly').val(),
            searching: false
        }
        }).success(function(data){
        //alert($('#catid').val());
        //alert(angular.element(document.getElementById('catid')));
        //console.log('here2');
        $scope.list = data;
        //data = $sce.trustAsJs(data);
        //console.log(data);
        //console.log($('#searchcat').val());
        //console.log($scope.catid);
        //$scope.catid = 1;
        $scope.currentPage = 1; //current page
        $scope.entryLimit = 10; //max no of items to display in a page
        $scope.searchcat = $('#catid').val() //max no of items to display in a page
        //$scope.searchcat = 9; //max no of items to display in a page
        $scope.filteredItems = $scope.list.length; //Initially for no filter  
        $scope.totalItems = $scope.list.length;
        //Scopes.store('questionsCrtl', $scope);
        //console.log($scope.list);
        //alert('HERE2');
    })
    
    .finally(function () {
    $scope.loading = false;
    });

    $scope.setPage = function(pageNo) {
        $scope.currentPage = pageNo;
    };
    
    $scope.filter = function() {
        $timeout(function() { 
            $scope.filteredItems = $scope.filtered.length;
        }, 10);
    };
    
    $scope.sort_by = function(predicate) {
        $scope.predicate = predicate;
        $scope.reverse = !$scope.reverse;
    };
    
    
    $scope.confirmDelete = function($event){
    
           if(!confirm("Are you sure you want to delete this question?")) {
                $event.preventDefault();
                return false;
           }
           return true;    
    };
    
    
    $scope.resetSearch = function(){
   
        if(angular.isDefined($scope.searchtags)){
            delete $scope.searchtags;
            
        }
            $(".tags-select").val('').trigger("chosen:updated");
            $('#tags').val([]);
            $scope.difficulty = '0';
            $scope.bloomtax = '0';
            $scope.searchcat = '0';
       
    };


    
    $scope.moveQuestions = function($event) {
        var selected = [];
        $('.question_checkbox:checked').each(function() {
            //console.log($(this).val());
            selected.push($(this).val());
        });
        
        
                $scope.loading = true;
                //var ajaxgetquesurl = $('#ajaxgetquesurl').val() + '&catid='+$('#searchcat').val()+'&myonly='+$('#myonly').val();
                //console.log(ajaxgetquesurl);
                //console.log($('#searchcat').val());
                
                
                //$http.get(ajaxgetquesurl, {catid: $('#searchcat').val(), myonly: $('#myonly').val()}).success(function(data){
                //console.log('here1');
                var ajaxurl = $('#ajaxmoveqs').val();
                
                $http.get(ajaxurl,  {
                params: {
                    dest_catid: $('#movetocat').val(),
                    //PHPSESSID: $('#phpsessid').val(),
                    selected: selected.join()
                }
                }).success(function(data){
                
                //console.log($scope.searchcat);
                //console.log(searchcat);
                $scope.searchcat = searchcat;

                $scope.changedValue($('#movetocat').val());
                $scope.searchcat = $('#movetocat').val();
                //$('#searchcat').val($('#movetocat').val()).prop('selected', true);
                                //$('#searchcat').val($('#movetocat').val());
                //$("#searchcat option[value='"+$("#searchcat").val()+"']").removeAttr( 'selected' );
                //$("#searchcat option[value='"+$("#movetocat").val()+"']").attr('selected', 'selected');
                
                
                
                var objSelect = document.getElementById("searchcat");

                //Set selected
                setSelectedValue(objSelect, $("#movetocat").val());
                //console.log($("#movetocat").val());
                //console.log($("#searchcat").val());
                function setSelectedValue(selectObj, valueToSet) {
                    for (var i = 0; i < selectObj.options.length; i++) {
                        if (selectObj.options[i].value== valueToSet) {
                            selectObj.options[i].selected = true;
                            return;
                        } else {
                            selectObj.options[i].selected = false;
                        }
                    }
                }
                
                
                
                
                //$('#searchcat').val($('#movetocat').val());
                //alert($('#catid').val());
                //alert(angular.element(document.getElementById('catid')));
                //console.log('here2');
                //$scope.list = data;
                //data = $sce.trustAsJs(data);
                //console.log(data);
                //console.log($('#searchcat').val());
                //console.log($scope.catid);
                //$scope.catid = 1;
                //$scope.currentPage = 1; //current page
                //$scope.entryLimit = 10; //max no of items to display in a page
                //$scope.searchcat = $scope.catid; //max no of items to display in a page
                //$scope.filteredItems = $scope.list.length; //Initially for no filter  
                //$scope.totalItems = $scope.list.length;
                //Scopes.store('questionsCrtl', $scope);
                //console.log($scope.list);
                //alert('HERE2');
            })
            
            .finally(function () {
            $scope.loading = false;
            });
        
        
        
    };
    
    
    
        $scope.updateTagSearch = function(){
        
        //console.log("updatesearch");
        //console.log($scope.searchall);
        
        
        console.log($scope.searchtags);
        $scope.searching = true;
        var ajaxurl = $('#ajaxurl').val();
        $http.get(ajaxurl,  {

        params: {
            catid: searchcat,
            myonly: $('#myonly').val(),
            searchterm: JSON.stringify($scope.searchtags),
            searching: 'tags'
        }
        }).success(function(data){    



        $scope.list = data;
        //console.log(data);
        $scope.currentPage = 1; //current page
        $scope.entryLimit = 10; //max no of items to display in a page
        //$scope.searchcat = 40; //max no of items to display in a page
        $scope.filteredItems = $scope.list.length; //Initially for no filter  
        $scope.totalItems = $scope.list.length;

        $scope.setPage = function(pageNo) {
        $scope.currentPage = pageNo;
        };
        $scope.filter = function() {
            $timeout(function() { 
                $scope.filteredItems = $scope.filtered.length;
            }, 10);
        };
        
        
        $scope.sort_by = function(predicate) {
            $scope.predicate = predicate;
            $scope.reverse = !$scope.reverse;
        };
        
        app.filter('startFrom');
      })
      .finally(function(data){
      $scope.loading = false;
      });
    //console.log($scope.list);

    //console.log('HERE2');


}
    
    
    
    
    
        $scope.updateSearch = function(){
        
        //console.log("updatesearch");
        //console.log($scope.searchall);
        
        $scope.searching = true;
        var ajaxurl = $('#ajaxurl').val();
        $http.get(ajaxurl,  {

        params: {
            catid: searchcat,
            myonly: $('#myonly').val(),
            searchterm: $scope.searchall,
            searching: 'text'
        }
        }).success(function(data){    



        $scope.list = data;
        //console.log(data);
        $scope.currentPage = 1; //current page
        $scope.entryLimit = 10; //max no of items to display in a page
        //$scope.searchcat = 40; //max no of items to display in a page
        $scope.filteredItems = $scope.list.length; //Initially for no filter  
        $scope.totalItems = $scope.list.length;

        $scope.setPage = function(pageNo) {
        $scope.currentPage = pageNo;
        };
        $scope.filter = function() {
            $timeout(function() { 
                $scope.filteredItems = $scope.filtered.length;
            }, 10);
        };
        
        
        $scope.sort_by = function(predicate) {
            $scope.predicate = predicate;
            $scope.reverse = !$scope.reverse;
        };
        
        app.filter('startFrom');
      })
      .finally(function(data){
      $scope.loading = false;
      });
    //console.log($scope.list);

    //console.log('HERE2');


}
    
    
    

    


    $scope.changedValue = function(searchcat){
    
     $scope.loading = true;
     //var ajaxgetquesurl = $('#ajaxgetquesurl').val();
     //console.log(searchcat);
     
     //$http.post(ajaxgetquesurl, {catid: $('#searchcat').val(), myonly: $('#myonly').val()}).success(function(data){
        //alert($('#catid').val());
        //alert(angular.element(document.getElementById('catid')));
        
     var ajaxurl = $('#ajaxurl').val();
     $http.get(ajaxurl,  {
        params: {
            catid: searchcat,
            myonly: $('#myonly').val()
        }
        }).success(function(data){    



        $scope.list = data;
        //console.log(data);
        $scope.currentPage = 1; //current page
        $scope.entryLimit = 10; //max no of items to display in a page
        //$scope.searchcat = 40; //max no of items to display in a page
        $scope.filteredItems = $scope.list.length; //Initially for no filter  
        $scope.totalItems = $scope.list.length;
        //Scopes.store('updateCat', $scope);
        //console.log($scope.list);
        //console.log('HERE1');

        $scope.setPage = function(pageNo) {
        $scope.currentPage = pageNo;
        };
        $scope.filter = function() {
            $timeout(function() { 
                $scope.filteredItems = $scope.filtered.length;
            }, 10);
        };
        $scope.sort_by = function(predicate) {
        $scope.predicate = predicate;
        $scope.reverse = !$scope.reverse;
        };
        app.filter('startFrom');
    })
      .finally(function(data){
      $scope.loading = false;
      });
    //console.log($scope.list);

    //console.log('HERE2');


}





});






