<?php
require_once __DIR__ . "/Model_DB.php";

class Model_user
{
    private $login;
    private $password;
    private $hash;
    private $id;
    const DB_NAME = "firstsite";
    const TABLE = "users";
    const USER = "admin";
    const PASSWORD = "12345";
    private static $db;

    function __construct(array $data)
    {
        $this->login = (isset($data["user_login"])) ? $data["user_login"] : null;
        $this->password = (isset($data["user_password"])) ? $data["user_password"] : null;
        $this->hash = (isset($data["user_hash"]) ? $data["user_hash"] : null);
        $this->id = (isset($data["user_id"]) ? $data["user_id"] : null);
    }

    static function StartConnection()
    {
        self::$db = new Model_DB($_SERVER["HTTP_HOST"], self::USER, self::PASSWORD, self::DB_NAME);
        try{
            self::$db->Connect();
        }
        catch (mysqli_sql_exception $e){
            print "Error in Model_user::StartConnection: ".$e;
        }
    }

    static function EndConnection()
    {
        try{
            self::$db->Disconnect();
        }
        catch (mysqli_sql_exception $e){
            print "Error in Model_user::EndConnection: ".$e;
        }
    }

    static function IsSetUser($login)
    {
        self::StartConnection();


        if(!is_string($login)){
            die("Login must be a string!");
        }

        $login = self::$db->TrimString($login);
        try{
            self::$db->Select(self::TABLE, "user_login = '$login'");
            $set = true;
        }
        catch(mysqli_sql_exception $e){
            $set = false;
        }
        finally {
            self::EndConnection();

            return $set;
        }
    }

    static function GetUser($login)
    {
        self::StartConnection();

        if(!is_string($login)){
            die("Login must be a string!");
        }

        $login = self::$db->TrimString($login);

        try{
            $data = self::$db->Select(self::TABLE, "user_login = '$login'");
        }
        catch (mysqli_sql_exception $e){
            print "User with login $login does not exist!";
        }
        finally {
            self::EndConnection();
        }

        return new Model_user($data[0]);
    }

    function GetUserLogin(){
        return $this->login;
    }

    function GetUserPassword(){
        return $this->password;
    }

    function GetUserHash(){
        return $this->hash;
    }

    function GetUserID(){
        return $this->id;
    }

    function SetUserLogin($login)
    {
        if(!is_string($login)){
            die("Login must be a string!");
        }

        $this->login = $login;
    }

    function SetUserPassword($pass)
    {
        if(!is_string($pass)){
            die("Password must be a string!");
        }

        $this->password = $pass;
    }

    function SetUserHash($hash)
    {
        if(!is_string($hash)){
            die("Hash must be a string!");
        }

        $this->hash = $hash;
    }

    function Save()
    {
        self::StartConnection();

        $this->login = self::$db->TrimString($this->login);
        $this->password = self::$db->TrimString($this->password);

        $data = ["user_login" => "'$this->login'",
            "user_password" => "'$this->password'",
            "user_hash" => "'$this->hash'"];

        if (isset($this->id)) {
            $where = "user_id = $this->id";
            try{
                self::$db->Update($data, self::TABLE, $where);
            }
            catch (mysqli_sql_exception $e){
                print("Error in function Model_user::Save() while updating user data: please check Model_user object data.");
            }
            finally{
                self::EndConnection();
            }
        } else {
            try{
                $this->id = self::$db->Insert($data, self::TABLE);
            }
            catch (mysqli_sql_exception $e){
                print("Error in function Model_user::Save() while creating new user: please check Model_user object data.");
            }
            finally{
                self::EndConnection();
            }
        }
    }
}