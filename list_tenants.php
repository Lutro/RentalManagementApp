<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getTenants()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);
$tenantQuery->close();


for($x=0; $x < count($resultArray); $x += 1){
    $resultArray[$x] += ["Edit"=>"<a href='edit_tenant.php?id=".$resultArray[$x]["id"]."'>edit</a>"];
}
for($x=0; $x < count($resultArray); $x += 1){
    $resultArray[$x] += ["Delete"=>"<a href='delete_tenant.php?id=".$resultArray[$x]["id"]."'>delete</a>"];
}
$keys = ["Name","Phone","Email","Number of Bikes","Storage Locker Number","Number of Pets", "Lease Start", "Lease End","Edit","Delete"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Tenant List", 
                 "heading"=>"Tenants",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("tenants-table.twig", $renderParams);
