<?php
require_once 'program_constants.php';
require_once 'vendor/autoload.php';

function render_page($templateName, $parameters){

    $loader = new \Twig\Loader\FilesystemLoader(TemplateDirectory);
    $twig = new \Twig\Environment($loader, [ ]);
    
    echo $twig->render($templateName, $parameters);
}
//Create a new sql connection object, with the paramters
//for the application
function valueIfExists($key,$array){
    if(array_key_exists($key, $array)){
        return $array[$key];
    } else  {
        return "";
    }
}

//Constants for pages
function navList(){
    return [[ "link" => 'list_suites.php', "text"=>'Suites'],
            [ "link" => 'list_tenants.php', "text"=> 'Tenants'],
            [ "link" => 'list_repair_orders.php', "text"=>'Repair Orders'],
            [ "link" => 'list_contractors.php', "text"=>'Contractors'],
            [ "link" => 'mgmt_stats.php', "text"=>'Management Statistics']];
}

// Defines the address element for a page
function address(){
    return "Langara College<br>".
            "100 West 49th Avenue<br>".
            "Vancouver B.C.<br>".
            "Canada V5Y 2Z6<br>".
            "604.323.5511<br>";

}

function title(){
    return "Renty: Easier Suite Management on the go";
}

//Create a new sql connection object, with the paramters
//for the application
function get_database_object(){
    return new mysqli(DBHost,DBUser,DBPWD,DBName);
}
//Check SQL connect status, and terminate if there is an error
function terminate_on_connect_error(){
    if (mysqli_connect_errno()) {
        render_page("error.twig", array( "message"=> mysqli_connect_error()));
        exit();
    } 
}
//check if query if successful, terminate if there is an error
function terminate_on_query_error($success){
    if(!$success){                
        render_page("error.twig",array("message" => "Query error"));
        exit();
    }   
}