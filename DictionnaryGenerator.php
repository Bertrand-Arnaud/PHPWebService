<?php
// Exemple of different types of query with the structure of the query dictionnary as associative array. 
$data = [];

$data["InsertCategory"] = [];
$data["InsertCategory"]["type"] = "insert";
$data["InsertCategory"]["sql"] = "INSERT INTO Category (label) VALUES (:label)";
$data["InsertCategory"]["param"] = ["label"];

$data["DeleteCategory"] = [];
$data["DeleteCategory"]["type"] = "delete";
$data["DeleteCategory"]["sql"] = "DELETE FROM Categories WHERE ID = :ID";
$data["DeleteCategory"]["param"] = ["ID"];

$data["SelectConnection"] = [];
$data["SelectConnection"]["type"] = "select";
$data["SelectConnection"]["sql"] = "SELECT * FROM users WHERE email = :email AND pwd = pwd";
$data["SelectConnection"]["param"] = ["email", "pwd"];

$data["InsertTag"] = [];
$data["InsertTag"]["type"] = "insert";
$data["InsertTag"]["sql"] = "INSERT INTO users (email, pwd) VALUES :values";
$data["InsertTag"]["param"] = ["values"];


$file = 'WSDictionnary.json';
file_put_contents($file, json_encode($data, true));

?>
