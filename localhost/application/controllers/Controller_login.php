<?php
require_once __DIR__."/../models/Model_user.php";

class Controller_login{

    function generateCode($length=6){

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;

        while (strlen($code) < $length) {

            $code .= $chars[mt_rand(0,$clen)];
        }

        return $code;

    }

    function action_index(){

        $error = '';

        if(isset($_POST["submit"])){
            if(Model_user::IsSetUser($_POST["login"])){
                $user = Model_user::GetUser($_POST["login"]);

                if($user->GetUserPassword() == md5(md5($_POST["password"]))){
                    $hash = md5($this->generateCode(10));

                    $user->SetUserHash($hash);
                    $user->Save();

                    session_start();

                    $_SESSION["id"] = $user->GetUserID();

                    $host = $_SERVER["HTTP_HOST"];
                    header("Location: http://$host/main/index");
                }
                else{
                    $error = "Incorrect password!";
                }
            }
            else{
                $error = "User with this login does not exist!";
            }

            //вид страницы логина с ошибкой error
            print $error;
        }
        else{
            //вид страницы логина
            print "Login page";
        }
    }

    function action_logout(){
        //снимаем переменные массива _SESSION
        session_start();
        session_destroy();

        //вид страницы логина
        print "Successful logout";
    }
}