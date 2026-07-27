<?php if (!defined("VERSION")) {echo "No, no, no...";exit;} ?>
<?php
//Configuració general
$usuari_privilegiat="admin";
$usuari_minim="consergeria";
$config_columnes=false; //Configurar 2n 2 columnes (true) o sols en 1 (false)
$config_semafor=false; //Configurar colors d'advertència en semafor (true) o en fons (false)
$config_paginacio_control_diari=10; //Repetir encapçalament cada x files
$config_mostrar_reserves=true; //Mostrar reserves en pàgina principal
//Conexió a BD
require_once "lib/Medoo.php";
use Medoo\Medoo;
$config_bd_carpeta="bd-SVF";
$config_bd_carpeta_backup="backup-SVF";
$config_bd_nomfitxer="Guardies.db";
$config_bd_ruta=$config_bd_carpeta."/".$config_bd_nomfitxer;
$config_bd_ruta_default=$config_bd_carpeta."/default-".$config_bd_nomfitxer;
$db = new Medoo(['type' => 'sqlite','database' => $config_bd_ruta]);

//Fitxer d'importació
$config_csv_carpeta="import-SVF";
$config_csv_nomfitxer="import.csv";
$config_csv_ruta=$config_csv_carpeta."/".$config_csv_nomfitxer;
$config_csv_rutadefault=$config_csv_carpeta."/default-".$config_csv_nomfitxer;;

//Configuració de franges horaries: R indica patis
$config_mati="1,2,3,R1,4,5,6,R2,7";
$config_vesprada="7,8,9,10,R3,11,12,13";
$config_dia="1,2,3,R1,4,5,6,R2,7,7,8,9,10,R3,11,12,13";
$config_intervals=["1" ,"2" ,"3" ,"R1","4" ,"5" ,"6" ,"R2","7" ,"8" ,"9" ,"10","R3","11","12","13"];
$config_intervals_hores=["08:00","08:55","09:50","10:45","11:15","12:10","13:05","14:00","14:20","15:15","16:10","17:05","18:00","18:20","19:15","20:10"];
$config_intervals_hores_refresh=["8:00","8:55","9:50","10:45","11:15","12:10","13:05","14:00","14:20","15:15","16:10","17:05","18:00","18:20","19:15","20:10","21:05"];
$config_hores=["1" => "8:00","2" => "8:55","3" => "9:50","R1" => "10:45","4" => "11:15","5" => "12:10","6" => "13:05","R2" => "14:00","7" => "14:20","8" => "15:15","9" => "16:10","10" => "17:05","R3" => "18:00","11" => "18:20",	"12" => "19:15","13" => "20:10"];
$config_temps=["1" => "55","2" => "55","3" => "55","R1" => "30","4" => "55","5" => "55","6" => "55","R2" => "20","7" => "55","8" => "55","9" => "55","10" => "55","R3" => "20","11" => "55", "12" => "55","13" => "55"];
$config_dies_setmana=["L","M","X","J","V"];

//Configuració dies i mesos
$config_data_dies=[DIA_1,DIA_2,DIA_3,DIA_4,DIA_5,DIA_6,DIA_7];
$config_data_mesos=[MES_1,MES_2,MES_3,MES_4,MES_5,MES_6,MES_7,MES_8,MES_9,MES_10,MES_11,MES_12];

$config_info_aules=["A01" => "CA02 28 Portàtils","B02" => "Saló Actes","B01" => "Biblioteca CA04 30 Portàtils", "C04" => "CA06 Portàtils Imatge CF Informàtica", "B24" => "24 PCs Fixes", "B25"=>"","C21" => "EOI","C22" => "EOI", "P2 GIMNÀS"=>"", "C15"=>"CA01 31 Portàtils INNOVATEC", "P6"=>"21 Tauletes", "C23" => "30 PCs Fixes", "P1"=>"TANCADA TEMPORALMENT! 30 PCs Fixes", "P5"=>"Aula TECNO", "CA03"=>"12 Portàtils CF S.PROFES", "B31"=>"CA05 30 Portàtils" ];

//Departaments
$config_departaments=["Anglés","Biologia i Geologia","Castellà","Economia","Educacio Fisica","Educació Plàstica i Visual","Filosofia","Física i Química","FOL","Francés","Geografia i Història","Hosteleria i Turisme","Informàtica i Comunicacions","Llatí i Grec","Matemàtiques","Música","Orientació","Religió","Serveis Socioculturals i a la Comunitat","Tecnologia","Valencià"];

//Botons especials 
$hores_consergeria=["10"]; //Hores on apareix el botó de consergeria
$hores_banys=["2","3","5","6","8","9","10","12","13"]; //Hores on apareix el botó de banys
$hores_banys_exclusió=["L","X","J"]; //Dies de la setmana on NO apareix el botó de banys a 8a hora
$hores_patis=["R1"]; //Hores on apareix el botó de patis
$hores_convivencia=["1" ,"2" ,"3" ,"4" ,"5" ,"6" ,"7" ,"8" ,"9" ,"10","11","12","13"]; //Hores on apareix el botó de convivència

//Tipus de tasques que no apareixen al botó +
$tipus_no_recolzament=['LEC','G','ATENCIÓ ALUMNAT','GD','FD','ACD','AFD'];
//Direcció
// GD = Guardies Direcció
// FD = Funcions Direcció
// ACD = Activitat Complementaria Direcció
// AFD = Atenció famílies Direcció

//Número màxim de columnes de QR Control per fila 
$columnes_qr_max=6; 
$calendari_inicial="2026-09-09"; //Data del primer dia de classe del curs
$calendari_final="2027-06-18"; //Data de l'últim dia de curs
$calendari_festius=[
    "2026-09-24","2026-09-25",
    "2026-10-09","2026-10-12","2026-12-07","2026-12-08",
    "2026-12-24","2026-12-25","2026-12-26","2026-12-27","2026-12-28","2026-12-29","2026-12-30","2026-12-31",
    "2027-01-01","2027-01-02","2027-01-03","2027-01-04","2027-01-05","2027-01-06",
    "2027-03-17","2027-03-18","2027-03-19",
    "2027-03-25","2027-03-26","2027-03-29","2027-03-30","2027-03-31",
	"2027-04-01","2027-04-02","2027-04-05"	
];
//Control Horari Llista
$config_control_llista_paginació=20; //Número de controls per pàgina

//Control Horari Professor Anual
$config_control = [
    
    "colors" => [
        "positiu_fill" => "green",
        "positiu_stroke" => "green",
        "negatiu_fill" => "red",
        "negatiu_stroke" => "red",
        "cap_setmana_fill" => "none",
        "cap_setmana_stroke" => "orange",
        "festiu_fill" => "none",
        "festiu_stroke" => "red",
        "sense_dades_fill" => "none",
        "sense_dades_stroke" => "black"
    ],

    "texts" => [
        "mes" => "Mes",
        "no_existeix" => "No existeix",
        "sense_dades" => "Sense dades",
        "cap_setmana" => "Cap de setmana",
        "festiu" => "Festiu",
        "treballat" => "Treballat",
        "horari" => "Horari",
        "diferencia" => "Dif",
        "unitats" => "m"
    ]

];