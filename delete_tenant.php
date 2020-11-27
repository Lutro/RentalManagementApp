<?php
require_once 'scripts/helper.php';

/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantID = (int)$_GET['id'];
//echo gettype($tenantID);

$tenantIDQuery = $dbh->prepare('call removeTenant(?)');
$tenantIDQuery->bind_param('i', $tenantID);
$success = $tenantIDQuery->execute();

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Contractor Deleted", 
                 "message" => "Tenant with ID: ".$tenantID." deleted"];
if($success){                
    render_page("error.twig", $renderParams);
    exit();
}   
//terminate_on_query_error($success); //program will terminate on error

