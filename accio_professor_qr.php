<?php if (!defined("VERSION")){ echo "No, no, no...";exit;}?>
<div class="container-fluid">     
	<h4><?=PROFESSOR_QR_TITUL_TAULA;?></h4>  	
</div>
<div class="container-fluid">     	
    <table class="table table-sm table-bordered">
        <tbody>	
        <?php 
        $profes = $db->select('Professor', array('ID','NOM','DEP'), array('ORDER' => 'NOM'));          
        $col_count = 0;
        echo "<tr>";
        foreach($profes as $profe){      
            $profe_id = $profe['ID'];	  	  
            $profe_nomprofe = $profe['NOM'];
            $profe_departament = $profe['DEP'];	
            
            //Generar QR de professor	
            $data = $profe_id."-".codi_validacio_control($profe_id);	
            ob_start();
            QRcode::png($data, false, QR_ECLEVEL_H, 8, 2);
            $qr = base64_encode(ob_get_contents());
            ob_end_clean();
            $qrimage = '<img src="data:image/png;base64,'.$qr.'" width="200px">';
            $logoimage = '<img src="images/logo.png" width="100px">';
            
            // Mostrar cel·la
            echo "<td><div style='text-align:center;padding:20px;'>$logoimage<br>$qrimage<br><font size='5'><b>$profe_nomprofe</b><br><i>$profe_departament</i></font><br>$data</div></td>";
            
            $col_count++;
            // Si arribem al màxim de columnes, tanca la fila i obri una de nova
            if($col_count % $columnes_qr_max == 0){
                echo "</tr><tr>";
            }
        }
        // Si l'última fila no està completa, tanca la fila
        if($col_count % $columnes_qr_max != 0){
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>