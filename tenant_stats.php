<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

// $tenantQuery = $dbh->prepare('call getTenants()');
// $success = $tenantQuery->execute();

// terminate_on_query_error($success); //program will terminate on error

// $resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// for($x=0; $x < count($resultArray); $x += 1){
//     $resultArray[$x] += ["link"=>"<a href='visitByRestaurant.php?rid=".$resultArray[$x]["ID"]."'>Visits</a>"];
// }
// $keys = ["name","phone","email","numberOfBikes","storageLockerNumber","numberOfPets", "leaseStart", "leaseEnd"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Tenant Stats", 
                 "heading"=>"Tenant Stats" ];


render_page("stats.twig", $renderParams);