<?php if (!defined("VERSION")){ echo "No, no, no...";exit;}?>
<div class="container-fluid"> 
    <h4><?=INDEX_CONTROL_MANUAL_TITUL;?></h4>
    
    <?php
    if (count($_POST)==0){
      // Mostrar Formulari
      ?>
      <form  id="FormControlManual" method="post" action="index.php?accio=control_manual">
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
	      		}
	      		?>        
        		</select><br>
            <label for="labelProfes"><?=INDEX_CONTROL_MANUAL_VALIDADOR;?></label> <br>
            <input type="text" id="validador" name="validador" value="" class="form-control"> <br><br>
            <input type="hidden" id="inout" name="inout" value="" readonly>
            <input type="hidden" id="latitud" name="latitud" value="" readonly>
            <input type="hidden" id="longitud" name="longitud" value="" readonly>
            <button id="boto_in" type="button" class="btn btn-success btn-sm" onclick="enviar('IN');"><?=INDEX_CONTROL_MANUAL_IN;?></button>
            <button id="boto_out" type="button" class="btn btn-danger btn-sm" onclick="enviar('OUT');"><?=INDEX_CONTROL_MANUAL_OUT;?></button>
            <button id="boto_clear" type="button" class="btn btn-warning btn-sm" onclick="netejar();"><?=INDEX_CONTROL_MANUAL_CLEAR;?></button>
            <?php if ($username == $usuari_privilegiat){ ?>
            <br><br>  
            <b><?=INDEX_CONTROL_MANUAL_DATAHORA;?></b><br>
            <label><?=INDEX_CONTROL_MANUAL_DATA;?>:</label><br>
              <input type="date" name="data_manual" value="<?=date('Y-m-d')?>" class="form-control"><br>

              <label><?=INDEX_CONTROL_MANUAL_HORA;?>:</label><br>
              <input type="time" name="hora_manual" value="<?=date('H:i')?>" class="form-control"><br><br>
            <?php } ?> 
          </div>       
      </form>
      <script>
          if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(function(posicio) {
                document.getElementById("latitud").value = posicio.coords.latitude;
                document.getElementById("longitud").value = posicio.coords.longitude;            
              });
          }
          function enviar(valor){
            document.getElementById("inout").value=valor;
            document.getElementById("FormControlManual").submit();               
          }
          
          function netejar(){
            document.getElementById("selectProfes").value="";
            document.getElementById("validador").value="";
          }
      </script>

      <br>
      <script src="lib/html5-qrcode.min.js"></script>
      <div id="reader" style="width:300px;"></div>
      <script>
      function onScanSuccess(decodedText, decodedResult) {

          str_split=decodedText.split("-");
          if (str_split.length==2){
            document.getElementById("selectProfes").value=str_split[0];
            document.getElementById("validador").value=str_split[1];            
        }
      }
      const html5QrCode = new Html5QrcodeScanner("reader",{ fps: 10, qrbox: 250 });
      html5QrCode.render(onScanSuccess);
      </script>


    <?php
    }else{
      //Rebre dades del Form                  
      $Fprofe = $_POST["selectProfes"];
      $Fvalidador = $_POST["validador"];
      $Finout = $_POST["inout"];
      $Flatitud=$_POST["latitud"];
      $Flongitud=$_POST["longitud"];  
      $Fubicacio=$Flatitud.",".$Flongitud;
      //Afegir informació del navegador i IP de la màquina que realitza el control
      $Fubicacio.=", ".$_SERVER['HTTP_USER_AGENT'].", ".$_SERVER['REMOTE_ADDR'];
      //Calcular validador
      $codi_validacio = codi_validacio_control($Fprofe);

      if (
          !empty($Fprofe) &&
          (
              $username == $usuari_privilegiat ||
              $codi_validacio == $Fvalidador
          )
      ){
          // Processar entrada
          $missatge = "";

          if ($username == $usuari_privilegiat && !empty($_POST["data_manual"]) && !empty($_POST["hora_manual"])) {
              $data = $_POST["data_manual"];
              $hora = $_POST["hora_manual"];
          } else {
              $data = date("Y-m-d");
              $hora = date("H:i:s");
          }
          $db->insert("Control", array('IDPROFESSOR' => $Fprofe, 'TIPO' => $Finout,  'DATA' => $data,  'HORA' => $hora,  'UBICACIO' => $Fubicacio,  'LATITUD' => $Flatitud,  'LONGITUD' => $Flongitud,  'DATAHORA_CODI_APP' => '', 'DATAHORA_QR_GENERAT'=> ''));	
          //echo $db->last();
          //Contestar
          if($Finout == "IN"){
            $missatge = "<b>".INDEX_CONTROL_ENTRADA."</b><br>";
            $missatge_alert="alert-success";
          }
          if($Finout == "OUT"){
            $missatge = "<b>".INDEX_CONTROL_EIXIDA."</b><br>";
            $missatge_alert="alert-info";
          }        
           
          //Mostrar dades del Guardat        
          $profes = $db->select('Professor', array('ID','NOM'), array('AND' => ['ID' => $Fprofe]));  	  
          foreach ($profes as $profe){
            $profe_id = $profe['ID'];		
            $profe_nom = $profe['NOM'];		
            $missatge.= " <b>[".$profe_id."]</b> ".$profe_nom."<br>";
          }		        
          $missatge.=$data." ".$hora."<br>";        
          echo "<div class=\"alert ".$missatge_alert."\" role=\"alert\">".$missatge."</div>";
          echo "<a href=\"index.php?accio=control_manual\" class=\"btn btn-primary btn-sm\">".INDEX_CONTROL_MANUAL_TORNAR."</a>";
          echo "<audio id=\"beepSound\" src=\"sounds/sensor-beep.mp3\" preload=\"auto\"></audio>";        
          echo "<script>
          window.addEventListener('load', function() {
              var audio = document.getElementById('beepSound');
              if (audio) {
                  audio.play().catch(function(e){
                      console.log('No s\\'ha pogut reproduir el so:', e);
                      if (navigator.vibrate) navigator.vibrate(200);
                  });
              }
          });
          </script>";          
      }else{
        //Validació incorrecta
        $missatge=INDEX_CONTROL_MANUAL_VALIDACIO_MAL;
        $missatge_alert="alert-danger";
        echo "<div class=\"alert ".$missatge_alert."\" role=\"alert\">".$missatge."</div>";
        echo "<a href=\"index.php?accio=control_manual\" class=\"btn btn-primary btn-sm\">".INDEX_CONTROL_MANUAL_TORNAR."</a>";
        echo "<audio id=\"errorSound\" src=\"sounds/sensor-error.mp3\" preload=\"auto\"></audio>";        
        echo "<script>
        window.addEventListener('load', function() {
            var audio = document.getElementById('errorSound');
            if (audio) {
                audio.play().catch(function(e){
                    console.log('No s\\'ha pogut reproduir el so:', e);
                    if (navigator.vibrate) navigator.vibrate(300);
                });
            }
        });
        </script>";        
      }      
    }
    ?>   
</div>