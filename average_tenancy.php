<?php
require_once 'scripts/helper.php';


/*get the tenancy information, to populate the list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getAvgTenancy()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);


$keys = ["Years", "Days"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Average Tenancy", 
                 "heading"=>"Average Length of Tenancy",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("repair-orders-stats.twig", $renderParams);
