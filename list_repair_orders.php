<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getRepairOrders()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);

for($x=0; $x < count($resultArray); $x += 1){
    $resultArray[$x] += ["Assign Contractor"=>"<a href='list_contractors.php?rid=".$resultArray[$x]["ID"]."'>Assign</a>"];
}

        // <label for="province">Province</label>
        // <select class="" name="province">
        //     {% for prov in provinces %}
        //     <option value="{{prov}}" {% if prov == province %} selected {% endif %} > {{prov}} </option> 
        //    {% endfor %}
        // </select>
$keys = ["repairID","suiteNumber", "priority", "type", "startDate", "endDate", "inspectionDate","Assign Contractor"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Repair Orders", 
                 "heading"=>"Repair Orders",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("generic-table.twig", $renderParams);