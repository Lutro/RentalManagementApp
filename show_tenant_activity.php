<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getTenantActivityLog()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error
