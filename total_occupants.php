<?php
require_once 'scripts/helper.php';


/*get the occupant information, to populate the occupant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getTotalCurrentOccupants()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);


$keys = ["Current Occupants"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Current Occupants", 
                 "heading"=>"Total Current Occupants",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("repair-orders-stats.twig", $renderParams);
