<?php
require_once 'scripts/helper.php';

/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();


$filterName = valueIfExists("name",$_GET);
$filterPhone = valueIfExists("phone",$_GET);


$tenantIDQuery = $dbh->prepare('call removeContractor(?,?)');
$tenantIDQuery->bind_param('ss', $filterName, $filterPhone);
$success = $tenantIDQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Contractor Deleted", 
                 "message" => "Contractor ".$filterName." deleted" ];
if($success){                
    render_page("error.twig", $renderParams);
    exit();
}   

