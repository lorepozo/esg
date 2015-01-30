<?php

if(!function_exists("db_write")){
  include("db.php");
}

function comm_update_user($user, $type){
  // for add/save/submit of user
  $path = preg_replace('/\/[^\/]*$/', '/', $_SERVER['REQUEST_URI']);
  $applink = sprintf('http://%s%sapp.php?id=%s', $_SERVER['HTTP_HOST'], $path, $user["id"]);
  $esg_globals = db_getglobals();
  $subject = "ESG Application Update";
  if($type == "add"){
    $text = $esg_globals["emailadd"];
  } elseif($type == "save"){
    $esg = db_getesg();
    $text = $esg["emailsave"];
  } elseif($type == "submit"){
    $esg = db_getesg();
    $text = $esg["emailsubmit"];
  } elseif($type == "admin"){
    return;
  } else {
    comm_inform_tech("ESG comm_update_user error. $type is not among 'add', 'save', or 'submit'.", false);
    return;
  }
  $text = str_replace("%APPLINK%", $applink, $text);
  while (preg_match('/%([A-Z0-9]+)\|?([^%]+)?%/', $text)) {
    $text = preg_replace_callback(
      '/%([A-Z0-9]+)\|?([^%]+)?%/',
      function($matches) use ($user){
        $attribute = $user[strtolower($matches[1])];
        if(!$attribute){
          $attribute = $matches[2];
        }
        return $attribute;
      },
      $text
    );
  }
  $headers = "";
  foreach($esg_globals["emailheaders"] as $header => $value){
    if(is_array($value)){
      $headers .= sprintf("%s: %s\r\n", ucfirst($header), implode(', ', $value));
    } else {
      $headers .= sprintf("%s: %s\r\n", ucfirst($header), $value);
    }
  }
  mail($user["email"], $subject, $text, $headers);
}

function comm_inform_tech($msg="", $verbose=true) {
  if($verbose == true){
    $msg .= "\n\n".json_encode(get_defined_vars());
  }
  mail(db_getglobals()["tech"], "[URGENT] [ESG ERROR] Application Issue", $msg);
}
