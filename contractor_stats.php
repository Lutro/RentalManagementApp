<?php
require_once 'scripts/helper.php';


/*get the contractor information, to populate the list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getContractorWorkStats()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);


$keys = ["Name", "Company", "Work Type", "Completed", "In Progress", "Average Days", "Average Quote ($)"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Contractor Stats", 
                 "heading"=>"Contractor Work Stats",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("repair-orders-stats.twig", $renderParams);
