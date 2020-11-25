<?php
require_once 'scripts/helper.php';

// $renderParams = [FormTypeKey=>VisitForm,"nav"=>navList(), "address" =>address(), "title"=>title()];

$db = get_database_object();

$tableQuery = $db->prepare("call getSuites()");
$success = $tableQuery->execute();
$resultArray = $tableQuery->get_result()->fetch_all(MYSQLI_ASSOC);
$tableQuery->close();
$renderParams["suites"] = $resultArray;
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
   render_page("add-tenant.twig",$renderParams);

} else {
    
    $data = $_POST;
    $db = get_database_object();

    $tableOccupantQuery = $db->prepare("call insertOccupant(?, ? ,?, ?, ?, ?)");

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
   echo gettype($tenantID)."<br>";
   $tableTenantIDQuery->close();


   // add into tenant
   $tableTenantQuery = $db->prepare("call insertTenant(?, ? ,?, ?)");
   $numberOfPets = (int)$data["numberOfPets"];
   echo gettype($numberOfPets)."<br>";
   $leaseStart = $data["leaseStart"];
   echo gettype($leaseStart)."<br>";
   $leaseEnd = $data["leaseEnd"];
  
   echo gettype($leaseEnd)."<br>";

   $tableTenantQuery->bind_param('iiss', $tenantID, $numberOfPets, $leaseStart, $leaseEnd);
    $success = $tableTenantQuery->execute();
    terminate_on_query_error($success); 
   
//    // add into lives in
//    $tableLivesInQuery = $db->prepare("call insertLivesIn(?, ? ,?, ?)");
//    $suiteNumber = $date["suiteNum"];
//    $moveInDate = $data["moveInDate"];
   
//    $tableLivesInQuery->bind_param('iis', $tenantID, $suiteNumber, $moveInDate);
//    $success = $tableLivesInQuery->execute();



    $renderParams["message"] = "The new occupant " . $name . " has been added!";
    render_page('add-tenant.twig',$renderParams);
}
