<?php

if(!function_exists("db_write")){
  include("db.php");
}

function comm_update_user($user, $type){
  // for add/save/submit of user
  $path = preg_replace('/\/[^\/]*$/', '/', $_SERVER[REQUEST_URI]);
  $applink = sprintf('http://%s%sapp.php?id=%s', $_SERVER[HTTP_HOST], $path, $user["id"]);
  $esg = db_getesg();
  $subject = "ESG Application Update";
  if($type == "add"){
    $text = $esg["emailadd"];
  } elseif($type == "save"){
    $text = $esg["emailsave"];
  } elseif($type == "submit"){
    $text = $esg["emailsubmit"];
  } elseif($type == "admin"){
    return;
  } else {
    mail($esg["tech"], "ESG comm_update_user error", $type." is not among 'add', 'save', or 'submit'.");
    return;
  }
  $text = str_replace(["APPLINK", "FIRSTNAME", "LASTNAME"], [$applink, ucfirst($user["first"]), ucfirst($user["last"])], $text);
  $headers = "";
  foreach($esg["emailheaders"] as $header => $value){
    if(is_array($value)){
      $headers .= sprintf("%s: %s\r\n", ucfirst($header), implode(', ', $value));
    } else {
      $headers .= sprintf("%s: %s\r\n", ucfirst($header), $value);
    }
  }
  mail($user["email"], $subject, $text, $headers);
}

?>