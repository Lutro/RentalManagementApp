<?php
require_once 'scripts/helper.php';

$renderParams = ["nav"=>navList(), "address" =>address(), "title"=>title()];

/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantID = (int)$_GET['id'];
echo ($tenantID);
$tableQuery = $dbh->prepare("call getTenantByID(?)");
$tableQuery->bind_param('i', $tenantID);
$success = $tableQuery->execute();
$resultArray = $tableQuery->get_result()->fetch_all(MYSQLI_ASSOC);

$tableQuery->close();


$renderParams["suites"] = $resultArray;
 $resultArray;
$valid = true;
$message = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){

    if(valueIfExists("name",$_POST)==""){
        $valid = false;
        $message = $message."name is not valid </br>";
       // $renderParams["name_class"] = "not_valid";
    }
    if(valueIFExists("phone",$_POST) == ""){
        $valid = false;
        $message = $message."phone not valid </br>";
        //$renderParams["phone_class"] = "not_valid";
    }
    if(!filter_var(valueIfExists("email",$_POST), FILTER_VALIDATE_EMAIL)){
        $valid = false;
        $message = $message."E-mail not valid </br>";
       // $renderParams["email_class"] = "not_valid";
    }
    if(valueIFExists("birthdate",$_POST) == ""){
        $valid = false;
        $message = $message."Birthdate not valid </br>";
        //$renderParams["phone_class"] = "not_valid";
    }
    if(valueIFExists("numberOfBikes",$_POST) < 0){
        $valid = false;
        $message = $message."Number of Bikes not valid </br>";
        //$renderParams["phone_class"] = "not_valid";
    }
    if(valueIFExists("storageLockerNumber",$_POST) < 0 ||
    valueIFExists("storageLockerNumber",$_POST) > 25 ){
        $valid = false;
        $message = $message."storage Locker Number not valid </br>";
        //$renderParams["phone_class"] = "not_valid";
    }


} else {
    $valid = false;
}


if(!$valid){
    //Post $_POST parameters from forms
   foreach($_POST as $key=>$value){
      $renderParams[$key] = $value;
   }
   $renderParams["message"] = $message;
   render_page("edit-tenant.twig",$renderParams);

} else {
    
    $data = $_POST;
    $dbh = get_database_object();

    $tableOccupantQuery = $dbh->prepare("call updateOccupantInfo(?, ? ,?, ?, ?, ?,?)");

    $name = $data["name"];
    $phone = $data["phone"];
    $email = $data["email"];
  
    //If You want to change the field name of a column

    // foreach($resultArray as $field) {
    //     if($field["ID"] == $restaurant) {
    //         $restaurant_name = $field["restaurant_name"];
    //     }
    // }
    
    $birthdate = $data["birthdate"];
    $numberOfBikes = $data["numberOfBikes"];
    $storageLockerNumber =$data["storageLockerNumber"];

    $tableOccupantQuery->bind_param('issssii', $tenantID, $name, $phone, $email, $birthdate, 
                                        $numberOfBikes, $storageLockerNumber);

   $success = $tableOccupantQuery->execute();
   terminate_on_query_error($success); 
   $tableOccupantQuery->close();



  // add into tenant
   $tableTenantQuery = $dbh->prepare("call updateTenantInfo(?, ? ,?, ?)");
   $numberOfPets = (int)$data["numberOfPets"];
   $leaseStart = $data["leaseStart"];
   $leaseEnd = $data["leaseEnd"];

   $tableTenantQuery->bind_param('iiss', $tenantID, $numberOfPets, $leaseStart, $leaseEnd);
   $success = $tableTenantQuery->execute();
   terminate_on_query_error($success);
   $tableTenantQuery->close();
   
  // add into lives in
   $tableLivesInQuery = $dbh->prepare("call updateLivesIn(?, ?, ?)");
   $suiteNumber = $data["suiteNumber"];
   $moveInDate = $data["moveInDate"];
   $moveOutDate = $data["moveOutDate"];
   
   $tableLivesInQuery->bind_param('iss', $tenantID, $moveInDate, $moveOutDate);
   $success = $tableLivesInQuery->execute();
   terminate_on_query_error($success);
   $tableLivesInQuery->close();

   $renderParams["message"] = "The tenant " . $name . " has been modified!\n\n";
   render_page('edit-tenant.twig',$renderParams);
}
