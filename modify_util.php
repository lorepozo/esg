<?php

function modify_category_print($category) {
  ?>
  <div class="group">
  <h2><?php echo $category[0] ?></h2><div>
  <textarea class="form-control" placeholder="top text"><?php echo $category[1]["text"] ?></textarea>
  <div class="fields">
  <?php
  foreach ($category[1]["fields"] as $question) { ?>
    <div class="group form-horizontal" id="<?php echo $question[1] ?>">
    <h4><?php echo $question[1] ?></h4><div>
    <h4><strong><?php echo $question[0] ?></strong></h4>
    <div class="form-group">
      <label class="col-md-3 control-label">Prompt: </label>
      <div class="col-md-9"><textarea class="form-control"><?php echo $question[2] ?></textarea></div>
    </div>
    <?php switch ($question[0]) {
    	case "text":
      	break;
  	 	case "textarea": ?>
        <div class="form-group">
          <label class="col-md-offset-2 col-md-1 control-label">rows: </label>
          <div class="col-md-1"><input class="spinner" size=4 value=<?php echo $question[3] ?>></div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label">Text below input: </label>
          <div class="col-md-9"><textarea class="form-control"><?php echo $question[4] ?></textarea></div>
        </div><?php
  			break;
  	 	case "radio":
  			foreach($question[3] as $radio) {?>
          <div class="form-group">
            <label class="col-md-1 control-label">Value: </label>
            <div class="col-md-2"><input class="form-control" type="text" value="<?php echo $radio[0] ?>"></div>
            <label class="col-md-1 control-label">Text: </label>
            <div class="col-md-8"><input class="form-control" type="text" value="<?php echo $radio[1] ?>"></div>
          </div>
  			<?php }
  			break;
      case "image":
        break;
    }?>
    </div>
    </div>
    <?php
  }
  ?>
  </div></div></div>
  <?php
}

function modify_subjects_print() {
  return;
}

?>