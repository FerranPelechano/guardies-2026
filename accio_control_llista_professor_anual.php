<?php if (!defined("VERSION")){ echo "No, no, no...";exit;}?>

<?php
$inici = new DateTime($calendari_inicial);
$final = new DateTime($calendari_final);
$final->modify('+1 day');

$interval = new DateInterval('P1D');
$rang = new DatePeriod($inici, $interval, $final);

$resultats = [];

foreach ($rang as $data) {

    $dia = $data->format("d");
    $mes = $data->format("Y-m");
    $data_str = $data->format("Y-m-d");

    $temps_total = 0;
    $temps_acumulat = 0;
    $temps_horari = 0;

    $conteig_in = 0;
    $conteig_out = 0;

    $estat = "sense_dades";

    if ($data->format("N") >= 6) $estat = "cap_setmana";
    if (in_array($data_str, $calendari_festius)) $estat = "festiu";

    $control = $db->query("
        SELECT * FROM Control 
        WHERE IDPROFESSOR = '".$filtro_profe."' 
        AND DATE(DATA) = '".$data_str."'
    ")->fetchAll();

    if (count($control) > 0) {

        $horari = $db->query("
            SELECT DISTINCT H.HORA 
            FROM Horari H  
            WHERE H.IDPROFESSOR = ".$filtro_profe." 
            AND H.DATA = '".dia_actual($data_str)."'
        ")->fetchAll();

        foreach($horari as $h){
            $temps_horari += $config_temps[$h['HORA']];
        }

        $fitxatges = $db->query("
            SELECT * FROM Control 
            WHERE IDPROFESSOR = ".$filtro_profe." 
            AND DATE(DATA) = '".$data_str."'
            ORDER BY HORA
        ")->fetchAll();

        $t_in = "";
        $t_out = "";

        foreach($fitxatges as $f){

            if ($f['TIPO'] == "IN"){
                $t_in = $f['HORA'];
                $conteig_in++;
            }

            if ($f['TIPO'] == "OUT"){
                if ($t_in != "") $t_out = $f['HORA'];
                $conteig_out++;
            }

            if ($t_in != "" && $t_out != ""){
                $temps_acumulat += (strtotime($t_out) - strtotime($t_in)) / 60;
                $t_in = "";
                $t_out = "";
            }
        }

        $temps_total = (int) round($temps_acumulat - $temps_horari);
        $estat = $temps_total >= 0 ? "positiu" : "negatiu";
    }

    $resultats[$mes][$dia] = [
        "estat" => $estat,
        "data" => $data_str,
        "temps_total" => (int)$temps_total,
        "temps_acumulat" => (int)$temps_acumulat,
        "temps_horari" => (int)$temps_horari,
        "conteig_in" => $conteig_in,
        "conteig_out" => $conteig_out
    ];
}
?>

<!-- 🔵 TAULA VISUAL -->
<table class="table table-bordered text-center">
<thead>
<tr>
<th>Mes</th>
<?php for ($i=1;$i<=31;$i++): ?>
    <th><?= $i ?></th>
<?php endfor; ?>
</tr>
</thead>
<tbody>

<?php foreach ($resultats as $mes => $dies): ?>
<tr>
<td><b><?= $mes ?></b></td>

<?php for ($i=1;$i<=31;$i++): 
$dia = str_pad($i,2,"0",STR_PAD_LEFT);

if (isset($dies[$dia])) {

    $info = $dies[$dia];

    switch($info["estat"]){
        case "positiu":
            $fill = $config_control["colors"]["positiu_fill"];
            $stroke = $config_control["colors"]["positiu_stroke"];
            break;
        case "negatiu":
            $fill = $config_control["colors"]["negatiu_fill"];
            $stroke = $config_control["colors"]["negatiu_stroke"];
            break;
        case "cap_setmana":
            $fill = $config_control["colors"]["cap_setmana_fill"];
            $stroke = $config_control["colors"]["cap_setmana_stroke"];
            break;
        case "festiu":
            $fill = $config_control["colors"]["festiu_fill"];
            $stroke = $config_control["colors"]["festiu_stroke"];
            break;
        default:
            $fill = $config_control["colors"]["sense_dades_fill"];
            $stroke = $config_control["colors"]["sense_dades_stroke"];
    }

    $tooltip = $info["data"]." | Dif: ".$info["temps_total"];

} else {
    $fill = "none";
    $tooltip = "-";
}
?>

<td>
<span data-bs-toggle="tooltip" title="<?= $tooltip ?>">
<svg height="16" width="16">
<circle cx="8" cy="8" r="7"
    fill="<?= $fill ?>"
    stroke="<?= $stroke ?>"
    stroke-width="2"
/>
</svg>
</span>
</td>

<?php endfor; ?>

</tr>
<?php endforeach; ?>

</tbody>
</table>

<script>
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el){
    new bootstrap.Tooltip(el);
});
</script>

<!-- 🔽 ACORDIÓ -->
<div class="accordion mt-4" id="accordionMesos">

<?php $i = 0; foreach ($resultats as $mes => $dies): $i++; 

// 🔹 totals del mes
$total_acumulat_mes = 0;
$total_horari_mes = 0;
$total_diferencia_mes = 0;
$total_in_mes = 0;
$total_out_mes = 0;

foreach ($dies as $info){
    $total_acumulat_mes += $info["temps_acumulat"];
    $total_horari_mes += $info["temps_horari"];
    $total_diferencia_mes += $info["temps_total"];
    $total_in_mes += $info["conteig_in"];
    $total_out_mes += $info["conteig_out"];
}

$color_mes = $total_diferencia_mes >= 0 
    ? $config_control["colors"]["positiu_fill"]
    : $config_control["colors"]["negatiu_fill"];
?>

<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse"
                data-bs-target="#m<?= $i ?>">

            <div>
                📅 <strong><?= $mes ?></strong>&nbsp;
            </div>

            <div class="text-muted small">
                <?= $config_control["texts"]["treballat"] ?>: <b><?= $total_acumulat_mes ?></b> |
                <?= $config_control["texts"]["horari"] ?>: <b><?= $total_horari_mes ?></b> |
                <?= $config_control["texts"]["diferencia"] ?>: 
                <b style="color: <?= $color_mes ?>">
                    <?= $total_diferencia_mes ?>
                </b> |
                <?= INDEX_CONTROL_LLISTA_PROFESSOR_TH5; ?>: <b><?= $total_in_mes ?>/<?= $total_out_mes ?></b>
            </div>

        </button>
    </h2>

    <div id="m<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#accordionMesos">
        <div class="accordion-body">

        <table class="table table-sm table-bordered">
        <thead>
        <tr>
            <th></th>
            <th><?= INDEX_CONTROL_LLISTA_PROFESSOR_TH1; ?></th>
            <th><?= INDEX_CONTROL_LLISTA_PROFESSOR_TH2; ?></th>
            <th><?= INDEX_CONTROL_LLISTA_PROFESSOR_TH3; ?></th>
            <th><?= INDEX_CONTROL_LLISTA_PROFESSOR_TH4; ?></th>
            <th><?= INDEX_CONTROL_LLISTA_PROFESSOR_TH5; ?></th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($dies as $info): 

            $color = $info["temps_total"] >= 0 ? "text-success fw-bold" : "text-danger fw-bold";

            switch($info["estat"]){
                case "positiu":
                    $fill = $config_control["colors"]["positiu_fill"];
                    $stroke = $config_control["colors"]["positiu_stroke"];
                    break;
                case "negatiu":
                    $fill = $config_control["colors"]["negatiu_fill"];
                    $stroke = $config_control["colors"]["negatiu_stroke"];
                    break;
                case "cap_setmana":
                    $fill = $config_control["colors"]["cap_setmana_fill"];
                    $stroke = $config_control["colors"]["cap_setmana_stroke"];
                    break;
                case "festiu":
                    $fill = $config_control["colors"]["festiu_fill"];
                    $stroke = $config_control["colors"]["festiu_stroke"];
                    break;
                default:
                    $fill = $config_control["colors"]["sense_dades_fill"];
                    $stroke = $config_control["colors"]["sense_dades_stroke"];
            }
        ?>

        <tr>
            <td>
                <svg height="16" width="16">
                    <circle cx="8" cy="8" r="7" fill="<?= $fill ?>" stroke="<?= $stroke ?>" stroke-width="2"/>
                </svg>
            </td>
            <td><?= $info["data"] ?></td>
            <td><?= $info["temps_acumulat"] ?></td>
            <td><?= $info["temps_horari"] ?></td>
            <td class="<?= $color ?>"><?= $info["temps_total"] ?></td>
            <td><?= $info["conteig_in"] ?> / <?= $info["conteig_out"] ?></td>
        </tr>

        <?php endforeach; ?>

        </tbody>
        </table>

        </div>
    </div>
</div>

<?php endforeach; ?>

</div>