<?php
	include_once '../code/config.php';
	include_once '../code/error.php';
	// Page Title Here
	$pageTitle = "Appoint for Grooming | Gino's Dogs en Barbers";
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
				return "Close(" . $s . ")";
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
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<?php getHeadlinks(); ?>
		<?php echo "<title>" . $pageTitle . "</title>"; ?>
    <meta charset="utf-8" />
    <!--link href="https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet"/>
    <link href="https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.print.min.css" rel="stylesheet" media="print"/>
    <script src="https://fullcalendar.io/releases/fullcalendar/3.9.0/lib/moment.min.js"></script>
    <script src="https://fullcalendar.io/releases/fullcalendar/3.9.0/lib/jquery.min.js"></script>
    <script src="https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.min.js"></script-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css"  media="screen,projection" charset="utf-8"/>
    <style>
      #calendar {
        margin: 0 auto;
      }
    </style>
		<script>
      var $calendarAPI = jQuery.noConflict();
      $calendarAPI(document).ready(function() {
        $calendarAPI('#calendar').fullCalendar({
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month'
          },
          navLinks: true, // can click day/week names to navigate views
          editable: true,
					height: 500,
          eventLimit: true, // allow "more" link when too many events//year-month-day
          events: <?php echo json_encode(calendarDay()); ?>,
          selectable: true,
          selectHelper: true,
          eventClick: function(event) {
						if (event.title == "Set Appointment") {
							var calendarDay = $calendarAPI.fullCalendar.formatDate(event.start, "Y-MM-DD");
							//var calendarLabel = new Date(calendarDay).toDateString("MMMM dd, yy");
							var x = new Date(calendarDay);
							var monthNames = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];
							var month = x.getMonth();
							var day = x.getDate();
							var year = x.getYear() + 1900;
							var fullMonth = monthNames[month] + " " + day + ", " + year;
							document.getElementById("calendarDayModalLabel").innerHTML = "Appointment for " + fullMonth;
	            $calendarAPI.ajax({
	              url: "displayTime.php?date=" + calendarDay,
	              success: function(result){
	                $calendarAPI("#calendarDayModalBody").html(result);
	              }
	            });
	            $calendarAPI("#calendarDayModal").fadeIn(90);
	            $calendarAPI('#calendarDayModal').css("background-color", "rgba(0,0,0,0.5)");
	            $calendarAPI("#calendarDayModal").modal('show');
						}
          },
        });
      });
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
						<li class="breadcrumb-item active" aria-current="page">Calendar</li>
				  </ol>
				</div>
				<div class="mb-3" style="padding:10px;">
					<div class="card">
					  <h5 class="card-header">Terms and Condition</h5>
					  <div class="card-body">
							<p class="card-text">
								I warrant that my pet is current with the vaccinations required by law,
								and that my pet is healthy to the best of my knowledge. If pet is hurt or becomes
								lil, <b>GINO'S DOGS EN' BARBERS</b> has permission to call or take my pet to a
								veterinarian of their choice if pet's vet is inaccessible; or administer medications;
								or give other advisable attention, with their discretion and judgement, and such
								expenses will be paid promptly by the owner unless injury is a direct result of
								negligence of <b>GINO'S DOGS EN' BARBERS</b> personnel.
							</p>
							<p class="card-text">
								<b>GINO'S DOGS EN' BARBERS</b> will not be held responsible for any sickness
								or injury caused by the pet to itself during grooming, and is not responsible for
								any accidental death of pet of the nature of pre-existing health condition or
								natural disaster(fire, storm, flood, etc.).
							</p>
							<p class="card-text">
								<b>GINO'S DOGS EN' BARBERS</b> Will not be held responsible for clipper-burn
								and/or minor nicks resulting from grooming of matted, neglected coats, or for
								irritation caused by removing coat from pet possessing mild to severe skin allergy,
								nor will be held responsible for stressful effects grooming may have upon an
								elderly pet.
							</p>
							<p class="card-text">
								<b>GINO'S DOGS EN' BARBERS</b> reserve the right to charge additional fees for
								services we consider over and above the norm covered by our standard rates, and
								reserve the right  to refuse service to customer whose pet may pose a threat to
								their customers and staff or to other pets, whether it be an aggression problem,
								health problem, or parasite problems.
							</p>
							<p class="card-text">
								Owner agreed to be responsible for any property damages caused by the
								pet. Pet/s shall be released from the facility when all charges are fully paid.
							</p>
					  </div>
					</div>
				</div>
        <div>
          <h3>Choose a day for your appointment</h3>
        </div>
        <div id='calendar' class="col-xs-12 col-md-10"></div>
			</div>
		</div>
		<!-- End of Page Content  -->
		<?php getPageFooter(); ?>
		<?php getFooterLinks(); ?>
    <!-- Modal Calendar Day Time-->
    <div class="modal" id="calendarDayModal" tabindex="-1" role="dialog" aria-labelledby="calendarDayModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="calendarDayModalLabel">Appointment for grooming</h5>
            <button type="button" class="close" data-dismiss="modal" onclick="closeCalendarDayModal(this);" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="calendarDayModalBody">
            ...
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" onclick="closeCalendarDayModal(this);">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript">
      function closeCalendarDayModal(x){
        document.getElementById('calendarDayModal').style.display = "none";
      }
    </script>
	</body>
</html>
