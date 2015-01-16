<h2>Classes</h2>
<p>Please indicate which classes you think you'll take.</p>
<p>This is preliminary and you will be able to change it.
  We will meet with you on <?php echo date("l, F j Y",$esg["registerclasses"])?>
  to confirm your choices.</p>
<p><em>In order to be in ESG in the fall term you will need to take at least two of
your classes in ESG.</em></p>

<?php
foreach ($esg["subjects"] as $title => $subject) { ?>
    <br><label><?php echo $title?></label><br>
    <?php
    foreach ($subject["fields"] as $field) { ?>
      <label class="radio-inline"><input type="radio" name="<?php echo $subject["id"]?>"
           value="<?php echo $field[0]?>"><?php echo $field[1]?></label><br>
    <?php }  
  } ?>
<br><p class="help-block">
    * ES.729 is 'Science Writing and New Media:
    Engineering Communication in Context' it is a CI-H taught by Dave Custer.<br>
    ** ES.S10 is 'Drugs and the Brain' taugh by Z. Fallows.<br>
    *** ES.S11 is 'There is more to physics than Newton', taught by Analia Barrantes.
</p>


<div class="form-group">
  <label for="rcsubjects">What subjects will you take in the regular curriculum?</label>
    <p class="help-block">Please list course numbers if you know them.</p>
    <textarea class="form-control" name="rcsubjects"></textarea><br>
</div>
<hr>