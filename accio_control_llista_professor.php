<?php if (!defined("VERSION")){ echo "No, no, no...";exit;}?>
<?php
$filtro_profe = $_REQUEST["selectProfes"] ?? "";
?>
<div class="container-fluid"> 
    <h4><?=INDEX_CONTROL_LLISTA_PROFESSOR_TITLE;?> (<?= $filtro_profe; ?>)</h4>
      <form  id="FormControlManual" method="post" action="index.php?accio=control_llista_professor">
      	<div class="form-group">
        		<label for="labelProfes"><?=INDEX_CONTROL_MANUAL_PROFE;?></label>
        		<select class="form-control" id="selectProfes" name="selectProfes">
	      		<option value=""></option>
	      		<?php
	      		
                $profes = $db->select('Professor', array('ID','NOM'), array('ORDER' => 'NOM'));  
	      		foreach($profes as $profe){      
		  		  $profe_id = $profe['ID'];	  	  
	      		  $profe_nomprofe = $profe['NOM'];				    
		  			echo "<option value='".$profe_id."'>".$profe_nomprofe."</option>";				  
                    if ($filtro_profe==$profe_id){
                        echo "<option selected value='".$profe_id."'>".$profe_nomprofe."</option>";				  
                    }
	      		}
	      		?>        
        		</select><br>                        
            <button type="submit" class="btn btn-primary"><?=INDEX_CONTROL_LLISTA_PROFESSOR_SUBMIT;?></button>	 
        </div>
      </form>
      <br>
