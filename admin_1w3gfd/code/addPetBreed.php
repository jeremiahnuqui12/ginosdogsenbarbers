<?php

require 'config.php';

if(empty($_GET['name'])) {
  header("Location: ../appointment?newBreed=failed");
}

try {
  $query = getConnection()->prepare("INSERT INTO `dog_breed_list`(`dog_breed`) VALUES (:breed)");
  $query->bindParam(":breed", $_GET['name']);
  $query->execute();
  header("Location: ../appointment/?newBreed=success");
} catch (Exception $e) {

}
