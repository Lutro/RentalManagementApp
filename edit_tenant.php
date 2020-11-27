<?php
require_once 'scripts/helper.php';

$renderParams = ["nav"=>navList(), "address" =>address(), "title"=>title()];

/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantID = (int)$_GET['id'];
//echo gettype($tenantID);
$tableQuery = $dbh->prepare("call getTenantByID(?)");
$tableQuery->bind_param('i', $tenantID);
$success = $tableQuery->execute();
$resultArray = $tableQuery->get_result()->fetch_all(MYSQLI_ASSOC);

$tableQuery->close();
var_dump($resultArray);
var_dump($_POST);

$renderParams["suites"] = $resultArray;
 $resultArray;
$valid = true;
$message = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    // if(array_search(valueIfExists("restaurant",$_POST), $resultArray)){
    //     $valid = false;
    //     $message = $message."restaurant not valid </br>";
    //     $renderParams["restaurant_class"] = "not_valid";
    // }
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
    // if(valueIfExists("lastname",$_POST)==""){
    //     $valid = false;
    //     $message = $message."lastname not valid </br>";
    //     $renderParams["lastname_class"] = "not_valid";
    // }
    // $refDate = "01-04-2020";
    // if(!dateCheck(valueIfExists("timein",$_POST),$refDate )){
    //     $valid = false;
    //     $message = $message."Time in is not valid <br>";
    //     $renderParams["timein_class"] = "not_valid";
    // }
    // if(!dateCheck(valueIfExists("timeout",$_POST),$refDate )){
    //     $valid = false;
    //     $message = $message."Time out is not valid <br>";
    //     $renderParams["timeout_class"] = "not_valid";
    // }

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
    $db = get_database_object();

    $tableOccupantQuery = $db->prepare("call updateOccupantInfo(?, ? ,?, ?, ?, ?)");

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

    $tableOccupantQuery->bind_param('ssssii', $name, $phone, $email, $birthdate, 
                                        $numberOfBikes, $storageLockerNumber);

   $success = $tableOccupantQuery->execute();
   terminate_on_query_error($success); 
   $tableOccupantQuery->close();

   //If fields are not empty add as tenant

   //Get tenant ID
   $tableTenantIDQuery = $db->prepare("call getOccupantID(?, ? ,?)");
   $tableTenantIDQuery->bind_param('sss', $name, $phone, $email);
   $success = $tableTenantIDQuery->execute();
   terminate_on_query_error($success); 

   $result = $tableTenantIDQuery->get_result();
   $value = $result->fetch_object();
   $tenantID = $value->ID;
   $tableTenantIDQuery->close();

   // add into tenant
   $tableTenantQuery = $db->prepare("call insertTenant(?, ? ,?, ?)");
   $numberOfPets = (int)$data["numberOfPets"];
   $leaseStart = $data["leaseStart"];
   $leaseEnd = $data["leaseEnd"];

   $tableTenantQuery->bind_param('iiss', $tenantID, $numberOfPets, $leaseStart, $leaseEnd);
   $success = $tableTenantQuery->execute();
   terminate_on_query_error($success);
   $tableTenantQuery->close();
   
   // add into lives in
   $tableLivesInQuery = $db->prepare("call insertLivesIn(?, ?, ?)");
   $suiteNumber = $data["suiteNumber"];
   $moveInDate = $data["moveInDate"];
   
   $tableLivesInQuery->bind_param('iis', $tenantID, $suiteNumber, $moveInDate);
   $success = $tableLivesInQuery->execute();
   terminate_on_query_error($success);
   $tableLivesInQuery->close();

   $renderParams["message"] = "The new occupant " . $name . " has been added!\n\n";
   render_page('edit-tenant.twig',$renderParams);
}
