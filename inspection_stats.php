<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getInspectionStats()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);


$keys = ["Reason", "Inspections"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Inspection Statistics", 
                 "heading"=>"Number of Inspections per Reason",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("repair-orders-stats.twig", $renderParams);
    // $data = [
    //     ['Microsoft Internet Explorer', 56.33],
    //     ['Chrome', 24.03],
    //     ['Firefox', 10.38],
    //     ['Safari', 4.77],
    //     ['Opera', 0.91],
    //     ['Proprietary or Undetectable', 0.2]
    // ];

    // $ob = new Highchart();
    // $ob->chart->renderTo('container');
    // $ob->chart->type('pie');
    // $ob->title->text('My Pie Chart');
    // $ob->series(array(array("data"=>$data)));

    // render_page('piechart.twig', [
    //     'mypiechart' => $ob
    // ]);
