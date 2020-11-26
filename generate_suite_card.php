<?php
require_once 'scripts/helper.php';



// /*get the tenant information, to populate the tenant list from the database */
// $dbh = get_database_object();
// /* check connection */
// terminate_on_connect_error();

// $tenantID = (int)$_GET['id'];
// echo gettype($tenantID);

// $tenantIDQuery = $dbh->prepare('call removeTenant(?)');
// $tenantIDQuery->bind_param('i', $tenantID);
// $success = $tenantIDQuery->execute();


// if($success){                
//     render_page("error.twig",array("message" => "Tenant with ID: ".$tenantID." deleted"));
//     exit();
// }   


// $tenantQuery = $dbh->prepare('call getTenants()');
// $success = $tenantQuery->execute();

// terminate_on_query_error($success); //program will terminate on error

// $resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);
// $tenantQuery->close();


// for($x=0; $x < count($resultArray); $x += 1){
//     $resultArray[$x] += ["delete"=>"<a href='delete_tenant.php?id=".$resultArray[$x]["id"]."'>Delete</a>"];
// }
// $keys = ["name","phone","email","numberOfBikes","storageLockerNumber","numberOfPets", "leaseStart", "leaseEnd","delete"];

// $renderParams = ["nav"=>navList(), 
//                  "address" =>address(), 
//                  "title"=>title(),
//                  "page_title"=>"Tenant List", 
//                  "heading"=>"Tenants",
//                  "table" => $resultArray,
//                  "keys" => $keys ];


render_page("suite-card.twig", []);