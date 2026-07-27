<?php
error_reporting(1);
define("VERSION","Guardies4 2026-07-26 Ferran Pelechano. Llicència Creative Commons BY-NC-SA. f.pelechanogarcia@edu.gva.es");
$idioma="ca"; 
?>
<?php require_once "index_idioma_" . $idioma . ".php"; ?>
<?php require_once "index_config.php"; ?>
<?php require_once "index_funcions.php"; ?>
<!doctype html>
<html lang="<?= $idioma; ?>">
  <head>
    <meta charset="utf-8">    
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="<?=VERSION;?>">
    <title><?=INDEX_TITUL?></title>	
	<link rel="icon" href="images/logo.png" sizes="32x32" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container-fluid"> 
      <h4><?=INDEX_API_TITUL;?></h4>
      <?php
      //Obtenir valors de la connexió HTTP del POWER AUTOMATE
      $Fcadena_1 = $_GET['ID'];
      $Fcadena_2 = $_GET['Jornada'];
      $Fcadena_3 = $_GET['Data'];
      $Fcadena_4 = $_GET['DataFinal'];      
      $Fcadena_5 = $_GET['mail'];
      //Mostrar les dades rebudes des de Power Automate
      /*
      echo "<p><strong>Valors rebuts des de Power Automate:</strong></p>";
      echo "<ul>";
      echo "<li>ID: " . $Fcadena_1 . "</li>";
      echo "<li>Jornada: " . $Fcadena_2 . "</li>";
      echo "<li>Data: " . $Fcadena_3 . "</li>";
      echo "<li>DataFinal: " . $Fcadena_4 . "</li>";
      echo "<li>mail: " . $Fcadena_5 . "</li>";
      echo "</ul>";
      */
      //Processar les dades rebudes
        //Tipus: DT3.8. Permís personal per ser víctima de violència terrorista (Article 24)
        //Jornada: Jornada Incompleta / Jornada Completa
        //Inici: 2025-12-06T03:00:00Z
        //Final: 2025-12-06T07:00:00Z
        //Observacions: Test Adjunt

      //Calcular ID de professor a partir del seu mail
      $query ="SELECT P.ID AS IDP FROM Professor P WHERE P.MAIL='$Fcadena_5'";
      $id_docent = $db->query($query)->fetchAll();
      foreach ($id_docent as $docent){    
        $Profe = $docent['IDP'];
      }
      //Completar activitat codificada per tindre referència a la API de Power Automate
      $FActivitat = "[PW-$Fcadena_1]";
      //Inici: 2025-12-06T03:00:00Z
      //Final: 2025-12-06T07:00:00Z
      $data_inici = substr($Fcadena_3,0,10);
      $data_final = substr($Fcadena_4,0,10);
      $hora_inici = substr($Fcadena_3,11,5);
      $hora_final = substr($Fcadena_4,11,5);
      /*
      echo "<p>Data Inici: $data_inici, Data Final: $data_final, Hora Inici: $hora_inici, Hora Final: $hora_final</p>";
      */
      //Calcular les dates i intervals a emplenar entre inici i final     
      if ($Fcadena_2 =="Jornada Completa") {
          //Marcar totes les dates entre inici i final
          $start = new DateTime($data_inici);
          $end = new DateTime($data_final);
          $end = $end->modify( '+1 day' );
          $interval = new DateInterval('P1D');
          $daterange = new DatePeriod($start, $interval ,$end);
          $dates_array = array();
          foreach($daterange as $date){
              $dates_array[] = $date->format("Y-m-d");
          }
          //Marcar totes les hores de la jornada
          $check_intervals = array();
          $check_intervals = $config_intervals;
          foreach ($check_intervals as $index => $hora){	          	
            $check_intervals[]=$config_intervals[$index];
          }
          //Fem un array amb dia i hora a marcar per fer després les insercions
          $check_intervals_dia_hora = array();
          foreach ($dates_array as $dia) {
              foreach ($check_intervals as $hora) {
                  $check_intervals_dia_hora[] = array('dia' => $dia, 'hora' => $hora);
                  //echo "Afegit dia: $dia, hora: $hora<br>";
              }
          }


      } else {
          //Marcar totes les dates entre inici i final
          $start = new DateTime($data_inici);
          $end = new DateTime($data_final);
          $end = $end->modify( '+1 day' );
          $interval = new DateInterval('P1D');
          $daterange = new DatePeriod($start, $interval ,$end);
          $dates_array = array();
          foreach($daterange as $date){
              $dates_array[] = $date->format("Y-m-d");
          }
          //Si sols tenim un dia, cal calcular les hores a marcar segons la jornada mirant les hores d'inici i final
          if (count($dates_array) == 1) {
              $check_intervals_dia_hora = array();  
              //Calcular les hores a marcar segons la jornada mirant les hores d'inici i final
              $check_intervals = array();
              $hora_inici_ts = strtotime($hora_inici);
              $hora_final_ts = strtotime($hora_final);
              $ultima_franja_abans = null;
              foreach ($config_intervals_hores as $index => $hora){	
                 $hora_ts = strtotime($hora);
                // Guardem la última franja abans de l'hora d'inici
                if ($hora_ts <= $hora_inici_ts) {
                    $ultima_franja_abans = $index;                    
                }   
                //Franges dintre del interval                 
              	if (($hora_ts >= $hora_inici_ts) && ($hora_ts < $hora_final_ts)){
           		
                  $check_intervals[]=$config_intervals[$index];
              	}          	
              }          
              // Afegim la franja anterior si no està ja afegida
              if ($ultima_franja_abans !== null) {
                  $franja = $config_intervals[$ultima_franja_abans];
                  if (!in_array($franja, $check_intervals)) {
                      array_unshift($check_intervals, $franja);                      
                  }
              }              
          }
          //Fem un array amb dia i hora a marcar per fer després les insercions
          $check_intervals_dia_hora = array();
          foreach ($dates_array as $dia) {
              foreach ($check_intervals as $hora) {
                  $check_intervals_dia_hora[] = array('dia' => $dia, 'hora' => $hora);
                  //echo "Afegit dia: $dia, hora: $hora<br>";
              }
          }

          //Si tenim dos dies, cal calcular les hores a marcar del primer dia segons l'hora d'inici i les hores a marcar del segon dia segons l'hora final
          if (count($dates_array) == 2) {
            $check_intervals_dia_hora = array();  
            //Calcular les hores a marcar segons la jornada mirant les hores d'inici i final
              
              //Primer dia              
              $check_intervals = array();
              $dia=$dates_array[0];
              $hora_inici_ts = strtotime($hora_inici);
              $hora_final_ts = strtotime($hora_final);
              $ultima_franja_abans = null;
              foreach ($config_intervals_hores as $index => $hora){	
                 $hora_ts = strtotime($hora);
                // Guardem la última franja abans de l'hora d'inici
                if ($hora_ts <= $hora_inici_ts) {
                    $ultima_franja_abans = $index;                    
                }   
                //Franges dintre del interval                 
              	if (($hora_ts >= $hora_inici_ts) ){           		
                  $check_intervals[]=$config_intervals[$index];
              	}          	
              }          
              // Afegim la franja anterior si no està ja afegida
              if ($ultima_franja_abans !== null) {
                  $franja = $config_intervals[$ultima_franja_abans];
                  if (!in_array($franja, $check_intervals)) {
                      array_unshift($check_intervals, $franja);                      
                  }
              }   
              //Fem un array amb dia i hora a marcar per fer després les insercions
              foreach ($check_intervals as $hora) {
                  $check_intervals_dia_hora[] = array('dia' => $dia, 'hora' => $hora);
                  //echo "Afegit dia: $dia, hora: $hora<br>";
              }

              //Segon dia
              $check_intervals = array();
              $dia=$dates_array[1];
              $hora_inici_ts = strtotime($hora_inici);
              $hora_final_ts = strtotime($hora_final);
              foreach ($config_intervals_hores as $index => $hora){	
                 $hora_ts = strtotime($hora);
                //Franges dintre del interval                 
              	if (($hora_ts < $hora_final_ts) ){           		
                  $check_intervals[]=$config_intervals[$index];
              	}          	
              }                        
              //Fem un array amb dia i hora a marcar per fer després les insercions              
              foreach ($check_intervals as $hora) {
                  $check_intervals_dia_hora[] = array('dia' => $dia, 'hora' => $hora);
                  //echo "Afegit dia: $dia, hora: $hora<br>";
              }
          }

          //Si tenim més de dos dies, cal calcular les hores a marcar del primer dia segons l'hora d'inici, les hores completes del dies intermedis i les hores a marcar del darrer dia segons l'hora final
          if (count($dates_array) > 2) {
              $check_intervals_dia_hora = array();  
              //Calcular les hores a marcar segons la jornada mirant les hores d'inici i final
              $check_intervals = array();
              //Primer dia
              $check_intervals = array();
              $dia=$dates_array[0];
              $hora_inici_ts = strtotime($hora_inici);
              $hora_final_ts = strtotime($hora_final);
              $ultima_franja_abans = null;
              foreach ($config_intervals_hores as $index => $hora){	
                 $hora_ts = strtotime($hora);
                // Guardem la última franja abans de l'hora d'inici
                if ($hora_ts <= $hora_inici_ts) {
                    $ultima_franja_abans = $index;                    
                }   
                //Franges dintre del interval                 
              	if (($hora_ts >= $hora_inici_ts) ){           		
                  $check_intervals[]=$config_intervals[$index];
              	}          	
              }          
              // Afegim la franja anterior si no està ja afegida
              if ($ultima_franja_abans !== null) {
                  $franja = $config_intervals[$ultima_franja_abans];
                  if (!in_array($franja, $check_intervals)) {
                      array_unshift($check_intervals, $franja);                      
                  }
              }  
              //Fem un array amb dia i hora a marcar per fer després les insercions              
              foreach ($check_intervals as $hora) {
                  $check_intervals_dia_hora[] = array('dia' => $dia, 'hora' => $hora);
                  //echo "Afegit dia: $dia, hora: $hora<br>";
              }  

              //Dies intermedis
              $check_intervals = array();
              foreach (array_slice($dates_array, 1, -1) as $dia) {
                foreach ($config_intervals_hores as $index => $hora){	          	
                  $check_intervals[]=$config_intervals[$index];
                }
                //Fem un array amb dia i hora a marcar per fer després les insercions              
                foreach ($check_intervals as $hora) {
                  $check_intervals_dia_hora[] = array('dia' => $dia, 'hora' => $hora);
                  //echo "Afegit dia: $dia, hora: $hora<br>";
                }                
              }

              //Darrer dia
              $check_intervals = array();
              $dia=$dates_array[count($dates_array)-1];
              $hora_inici_ts = strtotime($hora_inici);
              $hora_final_ts = strtotime($hora_final);
              foreach ($config_intervals_hores as $index => $hora){	
                 $hora_ts = strtotime($hora);
                //Franges dintre del interval                 
              	if (($hora_ts < $hora_final_ts) ){           		
                  $check_intervals[]=$config_intervals[$index];
              	}          	
              }  
              //Fem un array amb dia i hora a marcar per fer després les insercions              
              foreach ($check_intervals as $hora) {
                  $check_intervals_dia_hora[] = array('dia' => $dia, 'hora' => $hora);
                  //echo "Afegit dia: $dia, hora: $hora<br>";
              }                
          }          
      }

      
/*
      //Mostrar les dades processades
      echo "<p><strong>Valor processats:</strong></p>";
      echo "<ul>";
      echo "<li>ID PROFESSOR: " . $Profe . "</li>";
      echo "<li>Activitat: " . $FActivitat . "</li>";      
      echo "<li>Dies/Hora a insertar: ";
      print_r($check_intervals_dia_hora);
      echo "</li>";      
      echo "</ul>";
  */    


      	foreach ($check_intervals_dia_hora as $DiaHora){
          $Dia = $DiaHora['dia'];
          $Hora = $DiaHora['hora'];
          //echo "Processant Dia: $Dia, Hora: $Hora<br>";
      		//Mirar el dia del que consultar horari	
      		$dia = dia_actual($Dia);
      		//Comprovar SUBSTITUCIO-> Canviar idsubst per idprofe
      		$es_substitut= $db->count('Substitucions', array('SUBSTITUT' => $Profe,'DE[<=]' => $Dia,'A[>=]'=>$Dia));
      		if($es_substitut!=0){
      			$aux_subs = $db->select('Substitucions(S)', ['PROFE'], array('SUBSTITUT' => $Profe,'DE[<=]' => $Dia,'A[>=]'=>$Dia));
      			foreach($aux_subs as $aux_sub){      
      				$nouProfe = $aux_sub['PROFE'];
      			}			
      			$Profe=$nouProfe;
      		}	

      		
      			//Els usuaris normals sols poden insertar o eliminar en dates actuals o futures!
      			$passat=false;
      			if ($username=="admin" || strtotime($Dia) >= strtotime($data)){$passat=true;}			
      			if($passat){
      					// MODE AFEGIR ABSENCIES
      				    if ($Hora==""){
      				        //No cal fer res
      				        }else{
      				    	//Comprobar si tenim ja una Guardia creada al sistema per evitar duplicats
      				    	$contar_guardies = $db->count('Guardia', array('IDPROFESSOR' => $Profe,'DATA' => $Dia,'HORA'=>$Hora));				
      				    	if ($contar_guardies==0){					
      				    		//Comprovar que el docent té sessió d'horari que cobrir
      							//Registrar Totes les sessions
      							$contar_sessio = $db->count('Horari', array('IDPROFESSOR' => $Profe,'DATA' => $dia,'HORA'=>$Hora));
      			    			if ($contar_sessio!=0){								
      				    			  $db->insert("Guardia", array('IDPROFESSOR' => $Profe,'DATA' => $Dia,'HORA'=>$Hora, 'ACTIVITAT' => $FActivitat, 'OBSERVACIONS' => $FObservacions));
                          //echo "Afegit registre per Profe: $Profe, Data: $Dia, Hora: $Hora, Activitat: $FActivitat<br>";
      				    		}			    			
      				    	}else{
      				    		//No creem el duplicat
      				    	}
      				    }				
      				}
      			}
      





      //Llistar les dades introduides al sistema a través de Power Automate					
      //$query = "SELECT G.*, P.NOM AS NOM, P.MAIL AS MAIL FROM Guardia AS G LEFT JOIN Professor P ON G.IDPROFESSOR=P.ID WHERE G.ACTIVITAT LIKE '%[PW%' AND G.ACTIVITAT LIKE '%$Fcadena_1]%' ORDER BY G.ID";      
      $data = date("Y-m-d");
      $query ="SELECT G.*, P.NOM AS NOM, P.MAIL AS MAIL, S.SUBSTITUT AS IDS, P2.NOM AS SNOM, P2.MAIL AS SMAIL FROM Guardia AS G LEFT JOIN Professor P ON G.IDPROFESSOR=P.ID  
              LEFT JOIN Substitucions S ON  S.PROFE=P.ID AND '".$data."' BETWEEN S.DE AND S.A LEFT JOIN Professor P2 ON S.SUBSTITUT=P2.ID WHERE G.ACTIVITAT LIKE '%$Fcadena_1]%' ORDER BY G.ID";           
      
      //echo $query;
      $llista_entrades_api = $db->query($query)->fetchAll();
      $cadena_taula="";
      foreach($llista_entrades_api as $valor){

        //Calcular el valor del array config_intervals_hores en base a la posició que done el array config_intervals pel valor $valor['HORA']
        $posicio_hora = array_search($valor['HORA'], $config_intervals);
        $hora_text = $config_intervals_hores[$posicio_hora];
        if ($valor['IDS']>0){
          $valor['IDPROFESSOR']=$valor['IDS'];
          $valor['NOM']=$valor['NOM']." <b>[S]</b> ".$valor['SNOM'];
          $valor['MAIL']=$valor['SMAIL'];
        }


        $cadena_taula.="<tr>";
        $cadena_taula.="<td>".$valor['ID']."</td>";        
        $cadena_taula.="<td>".$valor['NOM']." (".$valor['IDPROFESSOR'].")</td>";        
        $cadena_taula.="<td>".$valor['DATA']."</td>";        
        $cadena_taula.="<td>".$hora_text." (".$valor['HORA'].")</td>";                    
        $cadena_taula.="<td>".$valor['ACTIVITAT']."</td>";        
        $cadena_taula.="<td>".$valor['MAIL']."</td>";        
        $cadena_taula.="</tr>";
      }                 
      ?>
      <table class="table table-sm table-bordered table-striped">
        <thead>    
          <tr>
             <th><?=API_TAULA_1;?></th>
             <th><?=API_TAULA_2;?></th>
             <th><?=API_TAULA_3;?></th>
             <th><?=API_TAULA_4;?></th>
             
             <th><?=API_TAULA_6;?></th>
             
             <th><?=API_TAULA_8;?></th>
          </tr>
        </thead>
        <tbody><?=$cadena_taula;?></tbody>
      </table> 
    </div>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>