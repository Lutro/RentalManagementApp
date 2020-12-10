<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getInspectionStats()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);


$keys = ["Reason", "Inspections"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Inspection Statistics", 
                 "heading"=>"Number of Inspections per Reason",
                 "table" => $resultArray,
                 "keys" => $keys,
                "chartType" => "bar" ];


render_page("repair-orders-stats.twig", $renderParams);
