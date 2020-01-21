<?php
function appointmentTypeError(){
  if (isset($_GET['type'])) {
    return ucfirst($_GET['type']);
  }
}
function appointmentDateError(){
  if (isset($_GET['date'])) {
    return ucfirst($_GET['date']);
  }
}
function appointmentTimeError(){
  if (isset($_GET['time'])) {
    return ucfirst($_GET['time']);
  }
}

function contactError(){
  if (isset($_GET['contact'])) {
    return ucfirst($_GET['contact']);
  }
}
function petnameError(){
  if(isset($_GET['petname'])){
    return ucfirst($_GET['petname']);
  }
}
function petageError(){
  if(isset($_GET['petage'])){
    return ucfirst($_GET['petage']);
  }
}
function petbreedError(){
  if(isset($_GET['breed'])){
    return ucfirst($_GET['breed']);
  }
}
function petgenderError(){
  if(isset($_GET['petgender'])){
    return ucfirst($_GET['petgender']);
  }
}
function LastRabiesVaccinationDateError() {
  if(isset($_GET['lastRabiesVaccDate'])){
    return ucfirst($_GET['lastRabiesVaccDate']);
  }
}
function LastVaccinationDateError() {
  if(isset($_GET['lastVaccDate'])){
    return ucfirst($_GET['lastVaccDate']);
  }
}
function emailError(){
  if(isset($_GET['email'])){
    return ucfirst($_GET['email']);
  }
}
function fullNameError(){
  if(isset($_GET['name'])){
    return ucfirst($_GET['name']);
  }
}
function passwordError(){
  if(isset($_GET['password'])){
    return ucfirst($_GET['password']);
  }
}
function confirmPasswordError(){
  if(isset($_GET['confirm'])){
    return ucfirst($_GET['confirm']);
  }
}
?>
