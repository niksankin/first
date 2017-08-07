<?php
class Model_DB{
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $connection;

    function __construct($host, $user, $pass, $dbname){

        if(!is_string($host)){
            die("Host must be a string!");
        }
        if(!is_string($dbname)){
            die("DBName must be a string!");
        }
        if(!is_string($user)){
            die("User must be a string!");
        }if(!is_string($pass)) {
            die("Pass must be a string!");
        }

        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        $this->host = $host;
    }

    private function ProcessRowSet(mysqli_result $rows){

        $result = array();
        while($row = $rows->fetch_assoc()){
            array_push($result, $row);
        }

        return $result;
    }

    function Connect(){

        try{
            $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        }
        catch (mysqli_sql_exception $e){
            throw $e;
        }

        return true;
    }

    function Disconnect(){
        try{
            $this->connection->close();
        }
        catch (mysqli_sql_exception $e){
            throw $e;
        }
    }

    function TrimString($string){
        return $this->connection->real_escape_string($string);
    }

    function Insert(array $data, $table){

        $columns = "";
        $values = "";
        if(!is_string($table)){
            die("Table must be a string!");
        }

        foreach($data as $column=>$value){
            $columns .= ($columns == "") ? "" : ", ";
            $columns .= $column;
            $values .= ($values == "") ? "" : ", ";
            $values .= $value;
        }

        $sql = "INSERT INTO $table ($columns) VALUES ($values)";

        try{
            $this->connection->query($sql);
        }
        catch (mysqli_sql_exception $e){
            throw $e;
        }

        return $this->connection->insert_id;
    }

    function Select($table, $where){

        if(!is_string($table)){
            die("Table must be a string!");
        }

        if(!is_string($where)){
            die("Where must be a string!");
        }

        $sql = "SELECT * FROM $table WHERE $where";

        try {
            $result = $this->connection->query($sql);

            if(!$result->num_rows){
                throw new mysqli_sql_exception("Can`t find row in $table that matches condition $where");
            }
        }
        catch (mysqli_sql_exception $e){
            throw $e;
        }

        return $this->ProcessRowSet($result);
    }

    function Update(array $data, $table, $where){

        if(!is_string($table)){
            die("Table must be a string!");
        }

        if(!is_string($where)){
            die("Where must be a string!");
        }

        foreach($data as $column=>$value){

            $sql = "UPDATE $table SET $column = $value WHERE $where";
            try{
                $this->connection->query($sql);
            }
            catch (mysqli_sql_exception $e){
                throw $e;
            }
        }

        return true;
    }
}