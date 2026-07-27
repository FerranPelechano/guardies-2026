<?php if (!defined("VERSION")){ echo "No, no, no...";exit;}?>


<div class="container-fluid">     
	<h4><?=GUARDIES_TITUL;?></h4> 
	<table class="table table-sm table-bordered table-striped">
		<thead>
			<tr>
			<th class="col-2" scope="col"><?=PROFESSOR_HORA;?></th>
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
                $aux="";
				foreach ($config_dies_setmana as $dia_setmana){
                    $Qhoraris = $db->query("
                    SELECT H.ID, H.IDPROFESSOR,H.DATA,H.HORA,P1.NOM ,
                    (SELECT P.NOM FROM Substitucions S LEFT JOIN Professor P ON  S.SUBSTITUT=P.ID WHERE S.PROFE=H.IDPROFESSOR AND ('".$data."' BETWEEN S.DE AND S.A)) AS SUSTITUT
                    FROM Horari H LEFT JOIN Professor AS P1 ON H.IDPROFESSOR = P1.ID 
                    WHERE (H.DATA = '".$dia_setmana."' AND H.HORA = '".$hora."' AND H.TIPUS = 'G') 
                    ORDER BY P1.NOM
                    ")->fetchAll();	
					foreach($Qhoraris as $temp){
						$sus="";
                        $sus=$temp['SUSTITUT'];
                        if ($sus==""){
                            $aux.=$temp['NOM']."<br>";		
                        }else{                            
                            $aux.=$temp['NOM']." <b>[S]</b> ".$temp['SUSTITUT']."<br>";
                        }
					}			                    
					array_push($valor_dies_setmana,$aux);                    
                    $aux="";
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