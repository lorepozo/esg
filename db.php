<?php
function db_write($delta, $admin, $id, $year, $user) {
  $userfile = sprintf('users/%s/%s.user', $year, $id);
  $userfilelog = sprintf('users/%s/%d.%s.user',  $year, time(), $id);
  
  $descriptorspec = array(
     0 => array("pipe", "r"),
     1 => array("pipe", "w"),
     2 => array("file", "dberr.log", "a")
  );
  $process = proc_open('./parser.py --encode', $descriptorspec, $pipes);
  if (is_resource($process)) {
    fwrite($pipes[0], json_encode($user));
    fclose($pipes[0]);
    $contents = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    proc_close($process);

    $delta = str_replace(',',';COMMA;',$delta);
    $timestamp = date("Y-m-d H:i:s");
    $change = sprintf("\n%s,%s,%s %s (%s),%s", $timestamp, $admin, $user["first"], $user["last"], $id, $delta);
    file_put_contents('changelog.csv', $change, FILE_APPEND | LOCK_EX);
    file_put_contents($userfile, $contents, LOCK_EX);
    file_put_contents($userfilelog, $contents);
  } else {
    ?>There was a problem writing your change to the database. Please contact <?php echo $esg["tech"] ?> for assistance<?php
  }
}

function db_getesg($alt=null) {
  global $ESG_FILE;
  if(!isset($ESG_FILE) || !file_exists($ESG_FILE)){
    $ESG_FILE = 'esg';
  }
  if($alt == null){
    return json_decode(shell_exec("./parser.py --esg --file ./$ESG_FILE"), true);
  }
  $descriptorspec = array(
     0 => array("pipe", "r"),
     1 => array("pipe", "w"),
     2 => array("pipe", "w")
  );
  $process = proc_open('./parser.py --esg', $descriptorspec, $pipes);
  if (is_resource($process)) {
    fwrite($pipes[0], $alt);
    fclose($pipes[0]);
    $contents = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    proc_close($process);
    return [json_decode($contents), $stderr];
  }
}

function db_getuser($id, $esg=null) {
  if($esg === null){
    $esg = db_getesg();
  }
  $userfile = sprintf('users/%s/%s.user', $esg["year"], $id);
  $user = json_decode(shell_exec("./parser.py --user --file ".$userfile), true);
  return $user;
}

function db_getusers() {
  $esg = db_getesg();
  $dir = 'users/'.$esg["year"];
  $files = scandir($dir);
  $users = [];
  foreach($files as $file){
    if($file == "." or substr_count($file, '.')>1){continue;}
    $id = explode(".", $file)[0];
    $user = db_getuser($id, $esg);
    $users[$id] = $user;
  }
  return $users;
}
?>