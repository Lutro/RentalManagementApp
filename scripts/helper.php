<?php
require_once 'program_constants.php';
require_once 'vendor/autoload.php';

function get_database_object(){
    return new mysqli(DBHost,DBUser,DBPWD,DBName);
}

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