<?php
require_once 'scripts/helper.php';

function deleteTenant($tenantID) {
    $tenantIDQuery->bind_param('i', $tenantID);
}

/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getTenants()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);
$tenantQuery->close();
// $tenantIDQuery = $dbh->prepare('call removeTenant(?)');
// $tenantID = 1;
// $tenantIDQuery->bind_param('i', $tenantID);

for($x=0; $x < count($resultArray); $x += 1){
    $resultArray[$x] += ["delete"=>"<input type=\"submit\"id=".$resultArray[$x]["id"]."' class='button' value='delete'>"];
}
$keys = ["name","phone","email","numberOfBikes","storageLockerNumber","numberOfPets", "leaseStart", "leaseEnd","delete"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Tenant List", 
                 "heading"=>"Tenants",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("generic-table.twig", $renderParams);