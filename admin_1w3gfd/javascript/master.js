function setting_onclick(setting){
  var sub = document.getElementById('sub-setting');
  var icon = document.getElementById('setting-icon');
  if (sub.className == "d-none") {
    icon.className = "fa fa-caret-down";
    sub.className = "d-block";
  } else {
    icon.className = "fa fa-caret-right";
    sub.className = "d-none";
  }
}
