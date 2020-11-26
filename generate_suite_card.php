<?php
require_once 'scripts/helper.php';



/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$suiteNum = (int)$_GET['suiteNumber'];

$filterQuery = $dbh->prepare('call getSuiteBySuiteNum(?)');
$filterQuery->bind_param('i', $suiteNum);
$success = $filterQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $filterQuery->get_result()->fetch_all(MYSQLI_BOTH);


$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Suite", 
                 "heading"=>"Suite",
                 "suite" => $resultArray,
                ];



render_page("suite-card.twig", $renderParams);