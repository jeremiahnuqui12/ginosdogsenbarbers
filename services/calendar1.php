<?php
require '../code/config.php';
function getAvailableDay(){
	$available = array();
	// Starting Date
	$start = date("Y-m-d") . '+1 day';
	// End date
	$end_date = date("Y-m-d") . '+1 year';
	for ($i=0; $i <= 400; $i++) {
		if (strtotime($start) <= strtotime($end_date)) {
			array_push($available, $start);
			$start = date ("Y-m-d", strtotime("+1 day", strtotime($start)));
		} else {
			break;
		}
	}
	return $available;
}
//---------------------------------------------------------------------------
function getHoliday($id) {
	try {
		$holiday = array();
		$holiday_details = array();
		$query = getConnection()->prepare("SELECT `value`,`details` FROM `date_close` ORDER BY `value` ASC");
		$query->execute();
		while ($row = $query->fetch()) {
			array_push($holiday, $row['value']);
			array_push($holiday_details, $row['details']);
		}
		if ($id == "days") {
			return $holiday;
		} elseif ($id == "details") {
			return $holiday_details;
		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
}
function calendarDay() {
  // Set timezone
	$available = getAvailableDay();
	//---------------------------------------------------
	foreach ($available as $open) {
		$list_available[] = array(
			"title" => getTitle($open, "txt"),
			"start" => $open,
			"color" => getTitle($open, "color")
		);
  }
	return $list_available;
}

function getTitle($date, $txt){
	$holiday = getHoliday("days");
	$holiday_details = getHoliday("details");
	for ($i=0; $i < count($holiday); $i++) {
		if (strtotime($holiday[$i]) == strtotime($date)) {
			$s = $holiday_details[$i];
		}
	}
	if ($txt == "txt") {
		if (isset($s)) {
			return $s;
		} else {
			return "Set Appointment";
		}
	} else {
		if (isset($s)) {
			return "#F0706A";
		} else {
			return "#00ff00";
		}
	}
}
  echo json_encode(calendarDay());
