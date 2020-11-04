<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getContractors()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);


$keys = ["name","phoneNumber","company","address","email", "workType"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"List of Contractors", 
                 "heading"=>"Contractors",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("generic-table.twig", $renderParams);