<?php
include("admin_util.php");
include("modify_util.php");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Modify Application</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
  <script>
  $(function() {
    $("#accordion")
      .accordion({
        header: "> div > h2",
        collapsible: true,
        heightStyle: "content",
        active: false
      })
      .sortable({
        axis: "y",
        handle: "h2",
        stop: function(event, ui) {
          ui.item.children("h2").triggerHandler("focusout");
          $(this).accordion("refresh");
        }
      });
    $(".fields")
      .accordion({
        header: "> div > h4",
        collapsible: true,
        heightStyle: "content",
        active: false
      })
      .sortable({
        axis: "y",
        handle: "h4",
        stop: function(event, ui) {
          ui.item.children("h4").triggerHandler("focusout");
          $(this).accordion("refresh");
        }
      });
    $(".spinner").spinner();
  });
  </script>
</head>
<body class="container">
<div style="height:20px;width:100%"></div>
<?php
admin_login($_SERVER);
$esg = json_decode(file_get_contents('esg.json') , true);
?>
<div id="accordion">
<?php
foreach ($esg["questions"] as $category) {
  if (in_array($category[0], $esg["aftersubjects"])) {continue;}
  modify_category_print($category);
}
modify_subjects_print();
foreach ($esg["questions"] as $category) {
  if (in_array($category[0], $esg["aftersubjects"])) {
    modify_category_print($category);
  }
}
?>
</div>
</body>
</html>