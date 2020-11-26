<?php
require_once 'scripts/helper.php';


/*get the information, to populate the list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getSuitesAllWorkedOn()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);


$keys = ["Suite Number", "Total Repairs"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Suites All Worked On", 
                 "heading"=>"Suites All Contractors Have Worked On",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("repair-orders-stats.twig", $renderParams);
