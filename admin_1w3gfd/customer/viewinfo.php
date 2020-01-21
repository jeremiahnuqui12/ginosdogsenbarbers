<?php
require '../code/config.php';
function getInfo(){
  try {
    $appInfo = getConnection()->prepare("SELECT
      `contact_number`,
      `pet_name`,
      `pet_breed`,
      `pet_gender`,
      `pet_age`,
      `pet_size`,
      `last_rabies_vaccination`,
      `last_vaccination`
       FROM `appointment_customer_info`
    WHERE `appointment_id`=:appId");
    $appInfo->execute(
      array(
        ":appId" => $_GET['id']
      )
    );
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
        <td><?php echo getBreed($row['pet_breed']); ?></td>
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
        <td class="font-weight-bold">Last Rabies Vaccination Date:</td>
        <td><?php echo date('F d, Y', strtotime($row['last_rabies_vaccination'])); ?></td>
      </tr>
      <tr>
        <td class="font-weight-bold">Last Vaccination Date:</td>
        <td><?php echo date('F d, Y', strtotime($row['last_vaccination'])); ?></td>
      </tr>
      <?php
    }
    echo "</table>";
  } catch (Exception $e) {
    echo "error";
  }
}
function getBreed($id) {
  try {
    if ((int)$id) {
      $query = getConnection()->prepare("SELECT `dog_breed` FROM `dog_breed_list` WHERE `pet_id` = :id");
      $query->bindParam(":id", $id);
      $query->execute();
      return $query->fetch()['dog_breed'];
    } else {
      return $id;
    }
  } catch (Exception $e) {
    return $e->getMessage();
  }
}
getInfo();
?>
