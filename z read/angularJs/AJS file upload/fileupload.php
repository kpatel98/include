<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.0/angular.min.js" integrity="sha512-jiG+LwJB0bmXdn4byKzWH6whPpnLy1pnGA/p3VCXFLk4IJ/Ftfcb22katPTapt35Q6kwrlnRheK6UPOIdJhYTA==" crossorigin="anonymous"></script>
</head>
<body ng-app="myApp" ng-controller="myCtrl">
    
        <div ng-controller="controller">
        {{uname}}<br>
        <input type="text" ng-model="uname"><button ng-click="senddata()">Send</button>
        </div>
        <br>
        <div class="file-upload" >
        <input type="file" ng-model="myFile" accept="image/*"  onchange="angular.element(this).scope().uploadedFile(this)" file-model="myFile"/>
        <button ng-click="uploadFile()">upload me</button>
        
    </div><br>
    <img src="{{src}}" height="500px">
</body>
<script type="text/javascript">
	var myApp = angular.module('myApp', []);

myApp.directive('fileModel', ['$parse', function ($parse) {
    return {
    restrict: 'A',
    link: function(scope, element, attrs) {
        var model = $parse(attrs.fileModel);
        var modelSetter = model.assign;

        element.bind('change', function(){
            scope.$apply(function(){
                modelSetter(scope, element[0].files[0]);
            });
        });
    }
   };
}]);

// We can write our own fileUpload service to reuse it in the controller
myApp.service('fileUpload', ['$http', function ($http) {
    this.uploadFileToUrl = function(file, uploadUrl, name){
         var fd = new FormData();
         fd.append('file', file);
         fd.append('name', name);
         $http.post(uploadUrl, fd, {
             transformRequest: angular.identity,
             headers: {'Content-Type': undefined,'Process-Data': false}
         })
         .success(function(){
            console.log("Success");
         })
         .error(function(){
            console.log("Success");
         });
     }
 }]);

 myApp.controller('myCtrl', ['$scope', 'fileUpload', function($scope, fileUpload){

   $scope.uploadFile = function(){
        var file = $scope.myFile;
        console.log('file is ' );
        console.dir(file);

        var uploadUrl = "save_form.php";
        var text = $scope.name;
        fileUpload.uploadFileToUrl(file, uploadUrl, text);
   };

   $scope.uploadedFile = function(element) {
            var reader = new FileReader();
            reader.onload = function(event) {
             $scope.$apply(function($scope) {
                $scope.files = element.files;
                 $scope.src = event.target.result;  
             });
            }
            reader.readAsDataURL(element.files[0]);
          }


}]);

    // insert
myApp.controller("controller", function($scope, $http) {
    $scope.uname='k patel';
    $scope.senddata = function() {
        $http.post(
            "save_form.php", {
                'uname' : $scope.uname,
                'uemail' : 'kpatel@gmail.com',
             
            }
        ).then(function success(response){
            $scope.uname =response.data;
        });
    }

    // fetch data
    $scope.fetchData=function(){
        $http({
            method:"POST",
            url:"api.php",
            data:{data_search:$scope.data_search}
        }).then(function success(response){
            $scope.searchData = response.data;
        });
    };





});

</script>
</html>