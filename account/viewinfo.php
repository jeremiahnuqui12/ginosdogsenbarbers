<?php
require '../code/config.php';
function getInfo(){
  try {
    $appInfo = getConnection()->prepare("SELECT
      `pricing_for_grooming`.`size` AS `petSize_name`,
      `contact_number`,
      `pet_name`,
      `pet_gender`,
      `pet_breed`,
      `pet_age`,
      `price`,
      `last_rabies_vaccination`,
      `last_vaccination`
      FROM `appointment_customer_info`
      INNER JOIN `pricing_for_grooming` ON `pricing_for_grooming`.`id`=`appointment_customer_info`.`pet_size`
      WHERE `appointment_id`=:appId");
    $appInfo->execute(
      array(
        ":appId" => $_GET['id']
      )
    );
    //echo "<button type=\"button\" class=\"btn btn-info\" name=\"button\" onclick=\"window.print()\">Print</button>";
    echo "<table class=\"table\">";
    while ($row = $appInfo->fetch()) {
      ?>
      <tr>
        <td class="font-weight-bold">Contact Number:</td>
        <td><?php echo $row['contact_number']; ?></td>
      </tr>
      <tr>
        <td class="font-weight-bold">Pet Name:</td>
        <td><?php echo $row['pet_name']; ?></td>
      </tr>
      <tr>
        <td class="font-weight-bold">Pet Breed:</td>
        <td><?php echo checkBreed($row['pet_breed']); ?></td>
      </tr>
      <tr>
        <td class="font-weight-bold">Pet Gender:</td>
        <td><?php echo $row['pet_gender']; ?></td>
      </tr>
      <tr>
        <td class="font-weight-bold">Pet Age:</td>
        <td><?php echo $row['pet_age']; ?></td>
      </tr>
      <tr>
        <td class="font-weight-bold">Pet Size:</td>
        <td><?php echo $row['petSize_name'] . " (Price Range: " . $row['price'] . ")"; ?></td>
      </tr>
      <tr>
        <td class="font-weight-bold">Last Rabies Vaccination Date:</td>
        <td><?php echo $row['last_rabies_vaccination']; ?></td>
      </tr>
      <tr>
        <td class="font-weight-bold">Last Rabies Vaccination Date:</td>
        <td><?php echo $row['last_vaccination']; ?></td>
      </tr>
      <?php
    }
    echo "</table>";
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}
function checkBreed($breed){
  //return (int)$breed;
  if((int)$breed == 0){
    return $breed;
  } else {
    $query = getConnection()->prepare("SELECT * FROM `dog_breed_list` WHERE `pet_id`=:id");
    $query->execute(
      array(":id"=>$breed)
    );
    return $query->fetch()['dog_breed'];
  }
}
getInfo();
