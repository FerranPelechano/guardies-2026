<?php if (!defined("VERSION")){ echo "No, no, no..."; exit;} ?>

<?php
// ================= FILTRES =================
$filtro="";
$filtro_text="";
$filtro_profe=$_GET['filtro_profe'] ?? "";
$filtro_data=$_GET['filtro_data'] ?? "";

if($filtro_profe!=""){
	$filtro=" WHERE C.IDPROFESSOR='".$filtro_profe."' ";
	$filtro_text=" (".$filtro_profe.") ";	
	$filtro_text.="<a href=\"index.php?accio=control_llista\" class=\"btn btn-outline-dark btn-sm\">X</a> ";
}

if($filtro_data!=""){
	$filtro=" WHERE C.IDPROFESSOR='".$filtro_profe."' AND C.DATA='".$filtro_data."'";
	$filtro_text=" (".$filtro_profe." ".$filtro_data.") ";	
	$filtro_text.="<a href=\"index.php?accio=control_llista\" class=\"btn btn-outline-dark btn-sm\">X</a> ";
}

// ================= PAGINACIÓ =================
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$per_pagina = $config_control_llista_paginació;
$offset = ($pagina - 1) * $per_pagina;

// Total registres
//$total = $db->query("SELECT COUNT(*) as total FROM Control C ".$filtro)->fetch()['total'];
$total = ($db->query("SELECT COUNT(*) as total FROM Control C ".$filtro)->fetch()['total'] ?? 0);
$total_pagines = ceil($total / $per_pagina);
?>

<div class="container-fluid">     

<h4><?=INDEX_CONTROL_LLISTA;?><?=$filtro_text;?></h4>

<table class="table table-sm table-bordered table-striped">
<thead>
<tr>
	<th><?=CONTROL_TH_1;?></th>
	<th><?=CONTROL_TH_2;?></th>
	<th><?=CONTROL_TH_3;?></th>
	<th><?=CONTROL_TH_4;?></th>
    <th><?=CONTROL_TH_5;?></th>
	<th><?=CONTROL_TH_6;?></th>						
    <th><?=CONTROL_TH_7;?></th>						
	<?php if ($username==$usuari_privilegiat): ?>
    	<th><?=CONTROL_TH_8;?></th>
	<?php endif; ?>
</tr>
</thead>

<tbody>	

<?php
// ================= CONSULTA AMB LIMIT =================
$controls=$db->query("
	SELECT  C.*, P.NOM 
	FROM Control C 
	LEFT JOIN Professor P ON C.IDPROFESSOR=P.ID 
	$filtro 
	ORDER BY DATA, HORA
	LIMIT $per_pagina OFFSET $offset
")->fetchAll();

foreach($controls as $control):

$control_id = $control['ID'];
$control_tipo = $control['TIPO'];
$control_idprofessor = $control['IDPROFESSOR'];
$control_professor = $control['NOM'];
$control_data = $control['DATA'];
$control_hora = $control['HORA'];
$control_ubicacio = $control['UBICACIO'];
$control_lat = $control['LATITUD'];
$control_lon = $control['LONGITUD'];

// ================= UBICACIÓ =================
if(($control_lat == "0" && $control_lon=="") || ($control_lat == "" && $control_lon=="")){
    $ubicacio="<a href=\"#\" class=\"btn btn-dark btn-sm\" title=\"".$control_ubicacio."\">INFO</a> ";
}else{                    
    $ubicacio="<a href=\"https://www.google.es/maps/place/".$control_lat.",".$control_lon."\" title=\"".$control_ubicacio."\" target=\"_blank\" class=\"btn btn-outline-dark btn-sm\">MAP</a> ";
}
$ubicacio.=explode(",",$control_ubicacio)[count(explode(",",$control_ubicacio))-1];

// ================= TEMPS (igual que abans) =================
$temps=$db->query("SELECT DISTINCT H.HORA FROM Horari H WHERE H.IDPROFESSOR=".$control_idprofessor." AND H.DATA='".dia_actual($control_data)."'")->fetchAll();

$temps_horari=0;
foreach($temps as $temp){
	$temps_horari += $config_temps[$temp['HORA']] ?? 0;
}

$temps=$db->query("SELECT * FROM Control WHERE IDPROFESSOR=".$control_idprofessor." AND DATA='".$control_data."' ORDER BY HORA")->fetchAll();

$temps_acumulat=0;
$t_in="";

foreach($temps as $temp){
	if ($temp['TIPO']=="IN") $t_in = $temp['HORA'];
	if ($temp['TIPO']=="OUT" && $t_in!=""){
		$temps_acumulat += (strtotime($temp['HORA']) - strtotime($t_in))/60;
		$t_in="";
	}
}

$temps_total = $temps_acumulat - $temps_horari;
$temps_estil = ($temps_total >= 0) ? "text-success" : "text-danger";
?>

<tr>
	<td><?= $control_id ?></td>
    <td><?= $control_tipo ?></td>
    <td>
        <a href="index.php?accio=control_llista&filtro_profe=<?= $control_idprofessor ?>" class="btn btn-outline-dark btn-sm">F</a>
        <?= $control_professor ?>
        <a href="index.php?accio=control_llista_professor&selectProfes=<?= $control_idprofessor ?>" class="btn btn-outline-dark btn-sm">AC</a>
    </td>
    <td>
        <a href="index.php?accio=control_llista&filtro_profe=<?= $control_idprofessor ?>&filtro_data=<?= $control_data ?>" class="btn btn-outline-dark btn-sm">F</a>
        <?= $control_data ?>
    </td>
    <td><?= $control_hora ?></td>
    <td><?= $ubicacio ?></td>
    <td class="<?= $temps_estil ?>">
        <?= number_format($temps_total/60,2) ?>h
    </td>

	<?php if ($username==$usuari_privilegiat): ?>
	<td>
		<a href="index.php?accio=control_elimina&id=<?= $control_id ?>" class="btn btn-danger btn-sm">
			<?= CONTROL_LLISTA_ELIMINA ?>
		</a>
	</td>
	<?php endif; ?>
</tr>

<?php endforeach; ?>

</tbody>
</table>

<!-- ================= PAGINACIÓ ================= -->
<nav>
<ul class="pagination justify-content-center">

<?php if ($pagina > 1): ?>
<li class="page-item">
<a class="page-link" href="?accio=control_llista&pagina=<?= $pagina-1 ?>&filtro_profe=<?= $filtro_profe ?>&filtro_data=<?= $filtro_data ?>">«</a>
</li>
<?php endif; ?>

<?php for ($i=1; $i<=$total_pagines; $i++): ?>
<li class="page-item <?= ($i==$pagina)?'active':'' ?>">
<a class="page-link" href="?accio=control_llista&pagina=<?= $i ?>&filtro_profe=<?= $filtro_profe ?>&filtro_data=<?= $filtro_data ?>">
<?= $i ?>
</a>
</li>
<?php endfor; ?>

<?php if ($pagina < $total_pagines): ?>
<li class="page-item">
<a class="page-link" href="?accio=control_llista&pagina=<?= $pagina+1 ?>&filtro_profe=<?= $filtro_profe ?>&filtro_data=<?= $filtro_data ?>">»</a>
</li>
<?php endif; ?>

</ul>
</nav>

</div>