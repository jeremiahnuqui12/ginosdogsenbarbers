<?php
require '../code/config.php';

if(empty($_GET['id'])){
    header("Location:" . getWebroot());
}
try{
    $query = getConnection()->prepare("SELECT * FROM `pricing_for_grooming` WHERE `id`=:id");
    $query->bindParam(":id", $_GET['id']);
    $query->execute();
    echo "Grooming price range: " . $query->fetch()['price'];
} catch (Exception $e) {
    echo "Price Error: " . $e->getMessage();
}

?>