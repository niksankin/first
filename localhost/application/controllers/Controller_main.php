<?php
class Controller_main{
    function action_index(){

        session_start();

        echo "All right! Your session id is ".$_SESSION["id"];
    }
}