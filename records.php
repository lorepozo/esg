<?php
try {
  $c = file_get_contents('records/'.$_GET["q"].'.json');
  echo $c;
}
catch (Exception $e) {
  echo "Could not open record";
}
?>