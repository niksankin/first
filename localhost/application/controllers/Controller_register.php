<?php
require_once __DIR__."/../models/Model_user.php";

class Controller_register{

    function action_index(){

        $error = array();

        if(isset($_POST["login"]) && isset($_POST["password"])){

            if(!preg_match("/^[a-zA-Z0-9]+$/", $_POST["login"])){
                array_push($error, "Login must contains lowercase and uppercase letters and numbers.");
            }

            if(strlen($_POST["login"]) < 3 or strlen($_POST["login"]) > 30){
                array_push($error, "Login length must be between 3 and 30 chars.");
            }

            if(Model_user::IsSetUser($_POST["login"])){
                array_push($error, "This login has already registered.");
            }

            if(empty($error)){
                $password = md5(md5(trim($_POST["password"])));
                $login = $_POST["login"];
                $data = array("user_login" => $login, "user_password" => $password);
                $user = new Model_user($data);
                $user->Save();
                $host = $_SERVER["HTTP_HOST"];
                header("Location: http://$host/login/index");
            }
            else{
                //Вид страницы регистрации с ошибками error
                print_r($error);
            }
        }
        else{
            //вид для страницы регистрации
            print "Register page";
        }
    }

}