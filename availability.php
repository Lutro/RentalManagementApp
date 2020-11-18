<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getLockersInUse()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all();

foreach ($resultArray as $key => $value) {
    // $arr[3] will be updated with each value from $arr...
    echo "{$key} => {$value} ";
    print_r($resultArray);
}
// $keys = ["suiteNumber","bedrooms","bathrooms","rentAmount","size", "hasMasterKey"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Availability", 
                 "heading"=>"",
                 "lockers_in_use"=>$resultArray]  ;


render_page("availability.twig", $renderParams);