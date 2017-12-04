<?php
class Queue{

    // DB connection
    private $conn;


    // properties

    public function setType($type){
        $this->type = $type;
    }

    public function setfirstName($firstName){
        $this->firstName = $firstName;
    }

    public function setlastName($lastName){
        $this->lastName = $lastName;
    }

    public function setOrganization($organization){
        $this->organization = $organization;
    }

    public function setService($service){
        $this->service = $service;
    }

    public function setCreated($created){
        $this->created = $created;
    }


    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read products
    function getQue($tableName,$type){

       // select all query
       $query = "SELECT
                   type, firstName, lastName,organization, service,created
                FROM
                    $tableName
                WHERE
                    DATE(created) = CURDATE()";

        if($type){
            $query .= "AND type = '$type'";
        }

       // prepare query statement
       $stmt = $this->conn->prepare($query);

       // execute query
       $stmt->execute();

       return $stmt;
    }

    // create new Queue entry
    function create($tableName){

        $date_now = date("Y-m-d H:i:s");
        // query to insert record
        $query = "INSERT INTO
                    $tableName(type, firstName, lastName,organization, service,created)
                VALUES
                    (:type,
                    :firstName,
                    :lastName,
                    :organization,
                    :service,
                    :created)";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // bind values
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":firstName", $this->firstName);
        $stmt->bindParam(":lastName", $this->lastName);
        $stmt->bindParam(":organization", $this->organization);
        $stmt->bindParam(":service", $this->service);
        $stmt->bindParam(":created", $date_now);

        // execute query
        if($stmt->execute()){
            return true;
        }else{
            return $stmt->errorInfo();
        }
    }
}