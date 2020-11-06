<?php
require_once 'scripts/helper.php';

render_page("sign-in.twig",["nav"=>navList(), 
"address" =>address(), 
"title"=>title(),
"page_title"=>"SLogin", 
"heading"=>"Management",
"address" =>address()]);