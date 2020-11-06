<script type="text/javascript">

	// select delete
	$scope.fetchData=function(){
        $http({
            method:"POST",
            url:"api.php",
            data:{data_search:$scope.data_search}
        }).then(function success(response){
            $scope.searchData = response.data;
        });
    };

    // insert update
    $scope.insert = function() {
        $http.post(
            "api.php", {
                'uname' : $scope.name,
                'uemail' : $scope.email,
             
            }
        ).success(function(data) {
            $scope.data=data;
            $scope.name = null;
             $scope.email = null;
        });
    }

</script>


<?php

	$info = json_decode(file_get_contents("php://input"));
	$vid = mysqli_real_escape_string($conn, $info->editcat);
	$vcat = mysqli_real_escape_string($conn, $info->category);


	echo json_encode($data);
