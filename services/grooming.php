<?php
	include_once '../code/config.php';
	include_once '../code/error.php';
	// Page Title Here
	$pageTitle = "Home | Gino's Dogs en Barbers";
	if (isset($_GET['date']) && isset($_GET['time'])) {
		$_SESSION['date'] = $_GET['date'];
		$_SESSION['time'] = $_GET['time'];
		header("Location: grooming.php");
	} elseif (isset($_SESSION['date']) && isset($_SESSION['time'])) {
		// code...
	} else {
		header("Location: reservationCalendar.php");
	}
	function app_time(){
		$time12Hour = array(
			"8:00 AM",
			"9:30 AM",
			"11:00 AM",
			"12:30 PM",
			"2:00 PM",
			"3:30 PM",
			"5:00 PM",
			"6:30 PM"
		);
		$time24Hour = array(
			"8:00",
			"9:30",
			"11:00",
			"12:30",
			"14:00",
			"15:30",
			"17:00",
			"18:30"
		);
		for ($aa=0; $aa < count($time12Hour); $aa++) {
			echo "<option value=\"" . $time24Hour[$aa] ."\">" . $time12Hour[$aa] . "</option>";
		}
	}
	function getPetAge(){
		$ageList = array(
			"below 6 months old",
			"6 Months to 1 Year",
			"1 year to 3 year",
			"3 year to 6 year",
			"6 year to 9 year",
			"9 year to 12 year",
			"12 year to 15 year",
			"above 15 years old"
		);
		for ($x=0; $x < count($ageList); $x++) {
			?>
			<option value="<?php echo $ageList[$x]; ?>"><?php echo $ageList[$x]; ?></option>
			<?php
		}
	}
	//---Get Dog Breeds -----------------------
	function getDogBreeds() {
		$breedList = getConnection()->prepare("SELECT * FROM `dog_breed_list`;");
		$breedList->execute();
		while ($row = $breedList->fetch()) {
			echo "<option value=\"" . $row['pet_id'] . "\">" . $row['dog_breed'] . "</option>";
		}
	}
	if (!isset($_SESSION['7a13ce2a07525b4fd46ebc0226706fab'])) {
		if ($_SERVER['HTTP_REFERER'] == getWebroot()."/services/reservationCalendar.php") {
			header("Location: ../account/?signin=1&reservation=1&date	=" . $_SESSION['date'] . "&time=" . $_SESSION['time']);
		} else {
			header("Location: ../account/?signin=1&reservation=1");
		}

	}
	if (!isset($_SESSION['date'])) {
		header("Location: reservationCalendar.php");
	}
	function getPetsize(){
		try {
			$query = getConnection()->prepare("SELECT * FROM `pricing_for_grooming`");
			$query->execute();
			while ($row = $query->fetch()) {
				?>
				<option value="<?php echo $row['id']; ?>"> <?php echo $row['size']; ?></option>
				<?php
			}
		} catch (Exception $e) {

		}

	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadlinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
		<script type="text/javascript">
			function checkContactNumber(x) {
				var spanId = document.getElementById('contactError');
				if (x.value.length == 11) {
					x.type = "text";
					x.maxlength = 18;
					x.value = "+63 (" + x.value.substring(1, 4) + ")-" + x.value.substring(4, 7) + "-" + x.value.substring(7, 11);
					removeErrorMessage(spanId, x);
				} else if(x.value.length == 0) {
					getErrorMessage("Required", spanId, x);
				} else if (!x.value.match(/^[0-9\-\)\(\ \+]+$/)) {
					getErrorMessage("Invalid Characters", spanId, x);
				} else if (x.value.substring(0, 1) != 09 && x.value.substring(0, 6) != "+63 (9") {
					getErrorMessage("Invalid Number", spanId, x);
				} else if(x.value.length < 11) {
					getErrorMessage("Incomplete Number", spanId, x);
				}
			}
			function checkPetBreed(field) {
				var element = document.getElementById("petBreed");
				if (field.value == "other") {
					field.removeAttribute("name");
					if (!document.getElementById("otherBreed")) {
						var input = document.createElement("input");
						var type = document.createAttribute("type");
						var classx = document.createAttribute("class");
						var name = document.createAttribute("name");
						var require = document.createAttribute("required");
						var id = document.createAttribute("id");
						var placeholder = document.createAttribute("placeholder");
						type.value = "text";
						name.value = "pet_breed";
						id.value = "otherBreed";
						classx.value="form-control";
						placeholder.value = "Please Specify Breed";
				    input.setAttributeNode(type);
						input.setAttributeNode(name);
						input.setAttributeNode(require);
						input.setAttributeNode(id);
						input.setAttributeNode(classx);
						input.setAttributeNode(placeholder);
						element.appendChild(input);
					}
				} else {
					field.name="pet_breed";
					if (document.getElementById('otherBreed')) {
						element.removeChild(document.getElementById('otherBreed'));
					}
				}
			}
			//---Display Error Message
			function getErrorMessage(message, spanId, x) {
				spanId.innerHTML = message;
				x.style.border = "1px solid#ff0000";
				x.style.backgroundColor = "#FFFFE0";
			}
			function removeErrorMessage(spanId, x){
				spanId.innerHTML = "";
				x.style.border = "1px solid#d0d0d0";
				x.style.backgroundColor = "#fff";
			}
		</script>
		<script type="text/javascript">
			function appType(x) {
				var spanId = document.getElementById('apptypeError');
				if (x.value == "") {
					getErrorMessage("Required", spanId, x);
				} else {
					removeErrorMessage(spanId, x);
				}
			}
			function appDate(x) {
				var spanId = document.getElementById('appdateError');
				if (x.value == "") {
					getErrorMessage("Required", spanId, x);
				} else {
					removeErrorMessage(spanId, x);
				}
			}
			function appTime(x) {
				var spanId = document.getElementById('apptimeError');
				if (x.value == "") {
					getErrorMessage("Required", spanId, x);
				} else {
					removeErrorMessage(spanId, x);
				}
			}
			function checkPetname(x) {
				var spanId = document.getElementById('petnameError');
				if (x.value == "") {
					getErrorMessage("Required", spanId, x);
				} else if (!x.value.match(/^[a-zA-Z ]+$/)) {
					getErrorMessage("Invalid characters", spanId, x);
				} else {
					removeErrorMessage(spanId, x);
				}
			}
			function checkPetgender(x) {
				var spanId = document.getElementById('petgenderError');
				if (x.value == "") {
					getErrorMessage("Required", spanId, x);
				} else {
					removeErrorMessage(spanId, x);
				}
			}
			function checkPetbreed(x) {
				var spanId = document.getElementById('petbreedError');
				if (x.value == "") {
					getErrorMessage("Required", spanId, x);
				} else {
					removeErrorMessage(spanId, x);
				}
			}
			function checkPetage(x) {
				var spanId = document.getElementById('petageError');
				if (x.value == "") {
					getErrorMessage("Required", spanId, x);
				} else {
					removeErrorMessage(spanId, x);
				}
			}
			//onblur="checklastRabiesVaccDate(this)"
			function checklastRabiesVaccDate(x) {
				var spanId = document.getElementById('lastrabiesVaccineError');
				if (x.value == "") {
					getErrorMessage("Required", spanId, x);
				} else {
					removeErrorMessage(spanId, x);
				}
			}
			function checklastVaccDate(x) {
				var spanId = document.getElementById('lastVaccineError');
				if (x.value == "") {
					getErrorMessage("Required", spanId, x);
				} else {
					removeErrorMessage(spanId, x);
				}
			}
		</script>
		<script>
			function petSizePrice(size){
				if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp=new XMLHttpRequest();
				} else {  // code for IE6, IE5
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function() {
					if (this.readyState==4 && this.status==200) {
						document.getElementById("size_info").innerHTML=this.responseText;
					}
				}
				xmlhttp.open("GET","grommingPrice.php?id="+size.value,true);
				xmlhttp.send();
			}
		</script>
	</head>
	<body>
		<!-- Page Header -->
		<?php getPageHeader(); ?>
		<!-- Start of Page Content Here -->
		<div class="page-content">
			<div class="container">
				<div aria-label="breadcrumb">
				  <ol class="breadcrumb">
				    <li class="breadcrumb-item" aria-current="page">
							<a href="../">Home</a>
						</li>
						<li class="breadcrumb-item" aria-current="page">
							<a href="calendar.php">Calendar</a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">Appointment Details</li>
				  </ol>
				</div>
				<div>
					<form action="../code/appointment.php" method="post" enctype="multipart/form-data">
						<fieldset class="col-lg-12 col-md-12">
							<legend>Appointment Details</legend>
							<!--div class="form-group col-md-4">
					      <label for="app-type">Appointment Type:</label>
					      <select name="app_type" required class="form-control" id="apptype" onblur="appType(this)">
									<option value="">Select</option>
									<option value="One Time">One Time</option>
									<option value="Monthly">Monthly</option>
					      </select>
								<span class="text-danger" id="apptypeError"><?php //echo appointmentTypeError(); ?></span>
					    </div-->
							<div class="form-row col-md-12">
								<div class="form-group col-md-4">
							    <label for="app-date">Appointment Date:</label>
							    <input type="text" class="form-control" value="<?php echo $_SESSION['date']; ?>" readonly required id="appdate" name="app_date" onblur="appDate(this)"/>
							  </div>
								<div class="form-group col-md-4">
									<label for="app-time">Appointment Time:</label>
									<input type="text" value="<?php echo $_SESSION['time']; ?>" readonly class="form-control" required id="apptime" name="app_time" onblur="appTime(this)"/>
								</div>
							</div>
						</fieldset>
						<fieldset class="col-lg-12 col-md-12">
							<legend>Pet Details</legend>
							<div class="form-group col-md-4">
								<label for="owner-name">Owner's Name: </label>
								<input class="form-control" readonly required type="text" value="<?php echo getSessionName(); ?>"/>
							</div>
							<div class="form-row col-md-12 col-lg-12">
								<div class="form-group col-md-4">
									<label for="pet-name">Pet Name:</label>
									<input class="form-control" required type="text" maxlength="30" placeholder="Pet Name" name="pet_name" id="petname" onblur="checkPetname(this)"/>
									<span class="text-danger" id="petnameError"><?php echo petnameError(); ?></span>
								</div>
								<div class="form-group col-md-5">
									<label for="owner-contact-number">Owner's Contact Number: (Cellphone Number Only)</label>
									<input class="form-control" id="owner-contact-number" required type="text" maxlength="11" placeholder="Contact Number" onblur="checkContactNumber(this);" name="contact_number" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode === 8"/>
									<span class="text-danger" id="contactError"><?php echo contactError(); ?></span>
								</div>
							</div>
							<div class="form-row col-md-12 col-lg-12">
								<div class="form-group col-md-4">
									<label for="pet-gender">Pet Size:</label>
									<select required name="pet_size" id="petsize" onchange="petSizePrice(this)" class="form-control">
										<option value="">Select</option>
										<?php getPetsize(); ?>
									</select>
									<span class="text-info" id="size_info"></span>
								</div>
								<div class="form-group col-md-4">
									<label for="pet-gender">Pet Gender:</label>
									<select required name="pet_gender" id="petgender" class="form-control" onblur="checkPetgender(this)">
										<option value="">Select</option>
										<option value="Male">Male</option>
										<option value="Female">Female</option>
									</select>
									<span class="text-danger" id="petgenderError"><?php echo petgenderError(); ?></span>
								</div>
							</div>
							<div class="form-row col-md-12 col-lg-12">
								<div class="form-group col-md-4"  id="petBreed">
									<label for="pet-breed">Pet Breed:</label>
									<select name="pet_breed" class="form-control" onchange="checkPetBreed(this);" required id="petbreed" onblur="checkPetbreed(this)">
										<option value="">Select</option>
										<?php getDogBreeds(); ?>
										<option value="other">Others</option>
									</select>
									<span class="text-danger" id="petbreedError"><?php echo petbreedError(); ?></span>
								</div>
								<div class="form-group col-md-4">
									<label for="petage">Pet Age: </label>
									<select name="pet_age" class="form-control" required id="petage" onblur="checkPetage(this)">
										<option value="">Select</option>
										<?php getPetAge(); ?>
									</select>
									<span class="text-danger" id="petageError"><?php echo petageError(); ?></span>
								</div>
							</div>
						</fieldset>
						<fieldset class="col-lg-12 col-md-12">
							<legend>Other Details</legend>
							<div class="form-row">
								<div class="form-group col-md-4">
									<label for="last-rabiesVaccine">Last Rabies Vaccination Date: </label>
									<input class="form-control" required type="date" id="lastrabiesVaccine" name="lastRabiesVaccDate" required max="<?php echo date("Y-m-d"); ?>" onblur="checklastRabiesVaccDate(this)"/>
									<span class="text-danger" id="lastrabiesVaccineError"><?php echo LastRabiesVaccinationDateError(); ?></span>
								</div>
								<div class="form-group col-md-4">
									<label for="last-vaccine">Last Vaccination Date: </label>
									<input class="form-control" required type="date" name="lastVaccDate" id="lastVaccine" required max="<?php echo date("Y-m-d"); ?>" onblur="checklastVaccDate(this)"/>
									<span class="text-danger" id="lastVaccineError"><?php echo LastVaccinationDateError(); ?></span>
								</div>
							</div>
							<input type="submit" name="reserve" class="form-control col-md-5 btn btn-primary" value="Submit Appointment for Grooming">
						</fieldset>
					</form>
				</div>
			</div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
		<script type="text/javascript">
			/*var errorId = ["apptypeError",
				"appdateError",
				"apptimeError",
				"contactError",
				"petnameError",
				"petgenderError",
				"petbreedError",
				"petageError",
				"lastrabiesVaccineError",
				"lastVaccineError"
			];
			var inputId = ["apptype",
				"appdate",
				"apptime",
				"owner-contact-number",
				"petname",
				"petgender",
				"petbreed",
				"petage",
				"lastrabiesVaccine",
				"lastVaccine"
			];
			for (var i = 0; i <= errorId.length; i++) {
				checkId = document.getElementById(errorId[i]);
				if (checkId.innerText != "") {
					document.getElementById(inputId[i]).style.border = "1px solid#ff0000";
					document.getElementById(inputId[i]).style.backgroundColor = "#FFFFE0";
				}
			}*/
		</script>
	</body>
</html>
