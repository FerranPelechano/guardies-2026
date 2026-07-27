<?php if (!defined("VERSION")){ echo "No, no, no...";exit;}?>
    <div class="container-fluid"> 
      <h4><?=INDEX_API_TITUL;?></h4>

      <?php
      //Llistar les dades introduides al sistema a través de Power Automate					      
      //$query = "SELECT G.*, P.NOM AS NOM, P.MAIL AS MAIL FROM Guardia AS G LEFT JOIN Professor P ON G.IDPROFESSOR=P.ID WHERE G.ACTIVITAT LIKE '%[PW%' ORDER BY G.DATA DESC";      
      $query ="SELECT G.*, P.NOM AS NOM, P.MAIL AS MAIL, S.SUBSTITUT AS IDS, P2.NOM AS SNOM, P2.MAIL AS SMAIL FROM Guardia AS G LEFT JOIN Professor P ON G.IDPROFESSOR=P.ID  
              LEFT JOIN Substitucions S ON  S.PROFE=P.ID AND '".$data."' BETWEEN S.DE AND S.A LEFT JOIN Professor P2 ON S.SUBSTITUT=P2.ID WHERE G.ACTIVITAT LIKE '%[PW%' ORDER BY G.DATA DESC";      
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
        $cadena_taula.="<td>".$valor['COBERTAPER']."</td>";        
        $cadena_taula.="<td>".$valor['ACTIVITAT']."</td>";        
        $cadena_taula.="<td>".$valor['OBSERVACIONS']."</td>";        
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
             <th><?=API_TAULA_5;?></th>
             <th><?=API_TAULA_6;?></th>
             <th><?=API_TAULA_7;?></th>
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