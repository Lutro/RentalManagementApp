<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getSuitesRent()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);

$keys = ["suiteNumber","name","rentAmount"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Rent Administration", 
                 "keys"=>$keys,
                 "heading"=>"Rent",
                 "suites"=>$resultArray];


render_page("suite-rent-grid.twig", $renderParams);