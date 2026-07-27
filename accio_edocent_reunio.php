<?php if (!defined("VERSION")){ echo "No, no, no...";exit;}?>
<?php
$FProfes = $_POST['selectProfes'];
if ($FProfes==""){$FProfes="";}
?>
<div class="container-fluid">     
	<h4><?=EDOCENT_REUNIO_TITUL;?></h4>
  	<form  id="FormGrup" method="post" action="index.php?accio=edocent_reunio">
    	<div class="form-group">
			<div class="form-group">
				<label for="labelProfes"><?=EDOCENT_REUNIO_LABEL_PROFES;?></label>
				<select multiple size="12" class="form-control" id="selectProfes" name="selectProfes[]">
					<?php
						$profes = $db->select('Professor', array('ID','NOM'), array('ORDER' => 'NOM'));  
						foreach($profes as $profe){      
							$profe_id = $profe['ID'];	  	  
							$profe_nomprofe = $profe['NOM'];	  	  
							echo "<option value='".$profe_id."'>".$profe_nomprofe."</option>";
						}
					?>        
				</select>
			</div>
		<div class="form-group">	  
			<br>
			<button type="submit" class="btn btn-primary"><?=EDOCENT_REUNIO_LABEL_SUBMIT;?></button>	 
		</div>
  	</form>
</div>
<div class="container-fluid">     
	<h4><?=EDOCENT_REUNIO_HORARI;?><?= $FProfes ? " (" . count($FProfes) . EDOCENT_REUNIO_PROFES_SELECCIONATS : "" ?></h4> 
	<table class="table table-sm table-bordered table-striped">
		<thead>
			<tr>
			<th class="col-2" scope="col"><?=EDOCENT_REUNIO_HORA;?></th>
			<th class="col-2" scope="col"><?=DIA_1;?></th>
			<th class="col-2" scope="col"><?=DIA_2;?></th>
			<th class="col-2" scope="col"><?=DIA_3;?></th>
			<th class="col-2" scope="col"><?=DIA_4;?></th>
			<th class="col-2" scope="col"><?=DIA_5;?></th>
			</tr>
		</thead>
		<tbody>	
			<?php            
            foreach ($config_intervals as $hora){	                
				//GENERAR DIES	
				$valor_dies_setmana=[];			
				foreach ($config_dies_setmana as $dia_setmana){					                    				                    
                    $query = "SELECT * FROM Horari AS H LEFT JOIN Professor AS P1 ON H.IDPROFESSOR = P1.ID WHERE IDPROFESSOR IN (".implode(',', $FProfes).") AND DATA = '$dia_setmana' AND HORA = '$hora'  ORDER BY P1.NOM";
                    $Qhorari = $db->query($query)->fetchAll();
                    //echo $db->last()."<br>";                    					
                    $aux="";			  	
					foreach($Qhorari as $temp){
						$temp_id=$temp['ID'];						
                        $temp_idprofe=$temp['IDPROFESSOR'];						
                        $aux2="";
                        if ($temp['TIPUS']=='LEC'){
							$aux .= $temp['TIPUS']." - ".$temp['NOM'];
                            $aux2 =" ".$temp['GRUP']." ".$temp['MATERIA']." (".$temp['AULA'].")";                            
                            $aux .=" <button type=\"button\" class=\"btn btn-outline-secondary btn-sm\" title=\"".$aux2."\">".$temp['GRUP']."</button>";                            
                            $aux .="<br>";
						}else{
							$aux .= $temp['TIPUS']." - ".$temp['NOM']."<br>";
						}                        
					}
					array_push($valor_dies_setmana,$aux);
                    
				}
                
				//CONSTRUIR LA TAULA
				echo "<tr>";
				echo "<td>".$config_hores[$hora]."</td>";		
				foreach ($valor_dies_setmana as $valor_dia_setmana){					
					if($valor_dia_setmana==""){
						echo "<td></td>";
					}else{
						echo "<td>".$valor_dia_setmana."</td>";
					}
				}
				echo "</tr>";		  
                
			}	            
			?>		
		</tbody>
	</table>
 </div>