# Manual d'ús — Gestió de Guàrdies i Reserves (Guardies4, versió 2026-07-27)

Aplicació web en PHP per gestionar les guàrdies docents, les reserves d'aulari/recursos i el control horari d'un centre de Secundària i FP (contextualitzada a l'IES Sant Vicent Ferrer d'Algemesí). Autor: Ferran Pelechano (f.pelechanogarcia@edu.gva.es), sobre un desenvolupament previ en Java de Jose Chamorro Molina. Llicència Creative Commons BY-NC-SA.

Aquest manual té dues parts:
- **Part 1 — Manual d'usuari**: pensat per a qualsevol docent o membre del personal que faça servir l'aplicació del dia a dia.
- **Part 2 — Manual d'administrador**: instal·lació, configuració, gestió de dades i manteniment, pensat per a la persona (habitualment cap d'estudis o coordinació TIC) amb l'usuari privilegiat.

---

# PART 1 — MANUAL D'USUARI

## 1. Accedir a l'aplicació

L'accés es fa des del navegador, a la URL on estiga instal·lada l'aplicació (per exemple `https://elteucentre.es/guardies/`). El sistema demana un usuari i una contrasenya (autenticació HTTP Basic gestionada pel servidor, no per l'aplicació mateixa).

Hi ha tres nivells d'usuari, segons el nom d'usuari amb què s'entra:

| Nivell | Qui l'usa normalment | Què pot fer |
|---|---|---|
| **Usuari mínim** (p. ex. `consergeria`) | Consergeria / recepció | Només veure el dia **actual** (no pot navegar a altres dies), consultar llistes de correu i el quadre de guàrdies. Sense accés a absències, reserves ni menús d'usuari/administració. |
| **Usuari normal** (qualsevol altre nom, p. ex. el nom d'un professor/a) | Professorat en general | Veure qualsevol dia, registrar absències pròpies, fer reserves d'aules, consultar el seu horari, consultar llistes de correu. |
| **Usuari privilegiat** (`admin` per defecte) | Cap d'estudis / direcció | Tot l'anterior + gestió completa: professorat, substitucions, informes, importació de dades, control horari, control de vaga, API. |

## 2. La pantalla principal (quadre de guàrdies del dia)

En entrar, es mostra una taula amb totes les franges horàries del dia (files) i, per a cada franja, tres columnes:

1. **Professorat de guàrdia**: qui té guàrdia assignada eixa hora, amb indicació de si ha sigut **substituït** (marca **[S]**) perquè algú altre cobreix la seua guàrdia durant un període de substitució.
   - El botó **C** (Control) obri una finestra amb el detall de guàrdies fetes/convivències per eixe professor/a, amb un mini-gràfic comparatiu.
   - El botó **+** obri el "protocol" amb el professorat de reserva/recolzament disponible eixa hora (caps d'estudis, coordinacions, etc., segons els tipus configurats).
   - El botó **R** (si està activat, vore `$config_mostrar_reserves`) mostra les reserves d'aula fetes eixa hora.
2. **Absències / alumnat sense professor**: les hores on cal cobrir una absència, amb botons per **assignar** un professor/a de guàrdia.
3. **Convivència / consergeria / patis / banys**: botons especials configurables (consergeria, banys, patis, convivència) que apareixen només a les hores definides per l'administrador.

### Navegació de dates
A la barra superior:
- **Avui**: torna al dia actual.
- **<< / <**: retrocedeix 7 dies / 1 dia.
- **> / >>**: avança 1 dia / 7 dies.
- El botó amb la data mostra el dia seleccionat en format llarg (p. ex. "Dilluns, 15 de setembre de 2026").
- Si consultes un dia passat o futur, apareix un avís (**Passat**/**Futur**) i alguns botons d'acció es bloquegen per a usuaris normals (no es pot registrar sobre dies passats, excepte l'usuari `admin`).

L'usuari mínim (consergeria) no veu aquests botons de navegació: sempre veu el dia d'avui.

## 3. Registrar una absència pròpia

Menú superior → **Absència**.

1. Seleccioneu el vostre nom (o els noms, si sou més d'un/a) en la llista de professorat.
2. Trieu una o diverses **dates** (amb el calendari desplegable).
3. Marqueu les **hores/franges** afectades (caselles per cada franja horària del dia).
4. Opcionalment, indiqueu una **activitat**/motiu i **observacions**.
5. Hi ha un interruptor **Afegir/Eliminar** (botó de commutació) per canviar entre donar d'alta l'absència o eliminar-la.
6. Premeu enviar.

Notes importants:
- Si en eixa data hi ha una **substitució** activa (algú cobreix un altre professor/a de manera continuada), l'absència es registra automàticament sobre la persona substituta, no sobre la titular.
- Els usuaris normals només poden donar d'alta o eliminar absències en el dia actual o en dies **futurs**; l'usuari `admin` pot fer-ho també sobre dies passats.
- Si ja existeix una guàrdia creada per eixe professor/dia/hora, el sistema no la duplica.

## 4. Assignar professorat de guàrdia a una absència

Des del quadre principal, en una hora amb alumnat sense professor, es pot obrir l'acció **Assignar**:
- Es mostra primer el professorat que té guàrdia assignada eixa hora (prioritari).
- Després, la resta de professorat disponible (llista completa).
- També es poden consultar/afegir **notes** associades a eixa hora/dia (per exemple, indicacions per a qui faça la guàrdia).

## 5. Reserves d'aules i recursos

Menú **Reserves** → **Reserves**:
1. Trieu l'aula/espai/recurs del desplegable (mostra informació addicional entre claudàtors si l'aula té equipament especial, p. ex. "30 Portàtils").
2. Confirmeu la reserva per al dia i l'hora que esteu consultant.

Menú **Reserves** → **Llistat de reserves**: mostra totes les reserves fetes, amb filtres per aula o per professor/a (es poden combinar clicant sobre el nom d'aula o professor a la taula). Cada filtre actiu es pot llevar amb el botó **X**.

> Nota: el llistat complet (històric, sense restricció de dates) només és visible des del menú d'**Administrar**.

## 6. Consultar horaris i informació del centre (menú "Informes" d'usuari)

Al menú desplegable amb el nom del centre/usuari trobareu:
- **Aules**: ocupació setmanal de cada aula/espai (quines hores estan lliures o reservades).
- **Grup**: horari setmanal d'un grup-classe concret (seleccionable).
- **Guàrdies**: quadre setmanal de qui té guàrdia assignada cada hora/dia.
- **Reunions de coordinació** (edocent_reunio): permet triar diversos professors/es i vore quan coincideixen lliures per convocar una reunió.
- **El meu horari** (Professor - vore): consulteu el vostre horari personal setmanal seleccionant el vostre nom.
- **Equip directiu**: horari de l'equip directiu.

## 7. Llistes de correu

Menú **Llistes de correu** (visible per a tots els usuaris, inclòs l'usuari mínim): genera automàticament llistes d'adreces electròniques agrupades per **departament** i per **càrrec/tutoria** (coordinació de cicles, tutories d'ESO/Batxillerat/CF, tutories setmanals...), llestes per copiar i enganxar (botó **Copiar**) en el client de correu.

El sistema neteja automàticament els càrrecs de les persones substitutes la data de substitució de les quals ja ha caducat, per evitar que apareguen en llistes on ja no correspon.

## 8. Control horari (fitxatge amb codi QR)

Aquesta funcionalitat permet registrar entrades i eixides mitjançant un codi QR personal:

- **El vostre codi QR**: es genera automàticament (visible des de la vostra fitxa de professor/a o des del llistat de QR de l'administrador) i és únic per persona.
- **Registre automàtic**: en escanejar el QR amb el mòbil/càmera (via `index_control.php`), el sistema registra l'ENTRADA o l'EIXIDA amb data, hora i, si el dispositiu ho permet, ubicació GPS. Si s'intenta registrar dues vegades el mateix esdeveniment, el sistema avisa que ja consta ("JA EN SISTEMA").
- **Control manual**: si no es pot escanejar el QR (per exemple, no hi ha connexió o el dispositiu no té càmera), l'usuari privilegiat pot introduir manualment una entrada/eixida indicant l'usuari i un **codi de validació** personal.

Aquesta és una funcionalitat encara **en desenvolupament**, segons indica el mateix codi/README.

---

# PART 2 — MANUAL D'ADMINISTRADOR

## 1. Requisits i arquitectura

- **Llenguatge**: PHP (sense frameworks web, arxius `.php` independents inclosos des de `index.php` segons el paràmetre `?accio=`).
- **Base de dades**: SQLite, gestionada amb la llibreria **Medoo** (`lib/Medoo.php`), en un únic fitxer `.db` (per defecte `bd-SVF/Guardies.db`).
- **Front-end**: Bootstrap 5 (CSS/JS inclosos localment a `css/` i `js/`), jQuery, bootstrap-datepicker (localitzat en català).
- **Llibreries incloses**:
  - `lib/Medoo.php`: capa d'accés a bases de dades (ORM lleuger).
  - `lib/phpqrcode.php` i `js/html5-qrcode.min.js`: generació i lectura de codis QR per al control horari.
- **Autenticació**: HTTP Basic Auth gestionada pel servidor web (Apache `.htaccess`/`.htpasswd`); l'aplicació **no** té sistema propi de login ni de gestió d'usuaris/contrasenyes.
- **Idioma**: interfície en català, amb totes les etiquetes centralitzades a `index_idioma_ca.php` (es poden traduir creant `index_idioma_XX.php` i canviant la variable `$idioma` a `index.php`).

No hi ha cap fitxer `.htaccess` inclòs al paquet descarregat: cal crear-lo manualment (vore apartat 4).

## 2. Estructura de fitxers

```
index.php                     Punt d'entrada principal (menú + enrutament per $_GET['accio'])
index_config.php              Configuració general del centre (vore apartat 3)
index_idioma_ca.php           Totes les etiquetes de la interfície en català
index_funcions.php            Funcions auxiliars (format de dates, càlcul de dia de la setmana, codi QR...)
index_control.php             Endpoint que rep la lectura del QR (entrada/eixida)
index_control_manual.php      Formulari de validació manual del control horari
index_generar.php             Genera automàticament una còpia de seguretat diària de la BD
index_sense-permisos.php      Tall d'execució si l'usuari no és el privilegiat (usat per accions d'admin)
index_sense-permisos-minims.php  Tall d'execució si l'usuari és el mínim (consergeria)

accio_*.php                   Una acció = una pantalla/funcionalitat (vore taula de l'apartat 5)
api_powerautomate.php         Endpoint HTTP independent per rebre absències des de Power Automate

bd-SVF/default-Guardies.db    Base de dades SQLite "plantilla" (es copia a Guardies.db si no existeix)
backup-SVF/                   Carpeta on es desen les còpies de seguretat diàries automàtiques
import-SVF/default-import.csv Plantilla/exemple del fitxer d'importació d'horaris

css/, js/, images/, sounds/   Recursos estàtics (Bootstrap, datepicker, logo, sons del lector QR)
lib/                          Medoo (BD) i phpqrcode (generació de QR)
```

## 3. Fitxer de configuració (`index_config.php`)

Aquest és el fitxer clau a personalitzar en desplegar l'aplicació a un centre nou. Principals paràmetres:

| Variable | Funció |
|---|---|
| `$usuari_privilegiat` | Nom d'usuari (HTTP Auth) amb accés total/administració. Per defecte `"admin"`. |
| `$usuari_minim` | Nom d'usuari amb accés restringit al dia actual (p. ex. consergeria). Per defecte `"consergeria"`. |
| `$config_columnes` | `true`/`false`: mostra la 2a hora en 2 columnes o en 1. |
| `$config_semafor` | `true`/`false`: colors d'advertència en "semàfor" o en fons de cel·la. |
| `$config_paginacio_control_diari` | Cada quantes files es repeteix la capçalera al full de control diari. |
| `$config_mostrar_reserves` | Mostra o no el botó de reserves (R) a la pantalla principal. |
| `$config_bd_carpeta`, `$config_bd_nomfitxer` | Ruta i nom del fitxer SQLite de treball. |
| `$config_bd_carpeta_backup` | Carpeta on es deixen els backups diaris automàtics. |
| `$config_csv_carpeta`, `$config_csv_nomfitxer` | Ruta del fitxer CSV d'importació. |
| `$config_mati`, `$config_vesprada`, `$config_dia`, `$config_intervals`, `$config_intervals_hores`, `$config_hores`, `$config_temps` | Definició de les franges horàries del centre (matí/vesprada, hores lectives i patis "R1/R2/R3", durada de cada franja). **Cal adaptar-les a l'horari real del centre.** |
| `$config_dies_setmana` | Dies lectius (per defecte L, M, X, J, V). |
| `$config_info_aules` | Text descriptiu opcional per a cada aula (equipament, incidències temporals, etc.), mostrat als desplegables de reserva. |
| `$config_departaments` | Llista de departaments didàctics del centre (per a llistes de correu i fitxes de professorat). |
| `$hores_consergeria`, `$hores_banys`, `$hores_banys_exclusió`, `$hores_patis`, `$hores_convivencia` | Hores concretes on apareixen els botons especials al quadre principal. |
| `$tipus_no_recolzament` | Codis de tasca (GD, FD, ACD, AFD, LEC, G, ATENCIÓ ALUMNAT...) que **no** compten com a recolzament/reserva disponible al botó "+". |
| `$columnes_qr_max` | Nombre màxim de columnes en el llistat de codis QR per imprimir. |
| `$calendari_inicial`, `$calendari_final`, `$calendari_festius` | Dates d'inici/final de curs i dies festius, usats pel control horari anual. **Cal actualitzar cada curs.** |
| `$config_control_llista_paginació` | Nombre de registres per pàgina al llistat de control horari. |
| `$config_control` | Colors i textos del gràfic de control horari anual (SVG). |

També es pot personalitzar el logo substituint `images/logo.png`.

## 4. Instal·lació

### 4.1 Requisits del servidor
- PHP amb extensió **SQLite3/PDO_SQLITE** activada, i extensió **GD** (per generar els codis QR amb `phpqrcode.php`).
- Apache amb suport per a `.htaccess` (`AllowOverride` activat) per gestionar l'autenticació.
- Escriptura habilitada per l'usuari del servidor web sobre les carpetes `bd-SVF/`, `backup-SVF/` i `import-SVF/`.

### 4.2 Passos
1. Pujar tot el contingut del projecte a l'arrel pública del domini/subdomini (o a una subcarpeta).
2. Editar `index_config.php` amb les dades del centre (franges horàries, aules, departaments, calendari, usuari privilegiat...).
3. Crear el fitxer `.htaccess` a l'arrel amb, com a mínim:
   ```apache
   AuthName "Restricted Area"
   AuthType Basic
   AuthUserFile /ruta/absoluta/al/.htpasswd
   AuthGroupFile /dev/null
   require valid-user
   ```
4. Crear el `.htpasswd` corresponent (per exemple amb `htpasswd -c /ruta/.htpasswd admin` i afegir després la resta de professorat amb `htpasswd /ruta/.htpasswd nomusuari`, sense `-c`).
5. **Molt important — seguretat**: moure (o com a mínim protegir) les carpetes `bd-SVF/`, `backup-SVF/` i `import-SVF/` fora de l'arrel navegable, o bloquejar-hi l'accés directe (per exemple amb un `.htaccess` addicional dins d'eixes carpetes amb `Deny from all`, o directament ubicant-les fora del `document root`). El propi README ho adverteix: *"Deuria canviar-se a una unicació no navegable!"*. Actualment el fitxer `.db` és descarregable directament si algú en coneix la URL.
6. Importar l'horari inicial (vore apartat 5).

## 5. Importació de l'horari des de GHC Peñalara

Procés (documentat també al README original):

1. Des de **GHC Peñalara**, generar el fitxer XRHO d'exportació: **Transferir l'horari a** → **Altres formats d'intercanvi**.
2. Marcar el format: **CSV (Identificatius de les llistes + Tipus 1)**.
3. Triar separador **`;`** i format **UTF-8**.
4. Guardar el fitxer resultant com `import-SVF/import.csv` (o la ruta configurada a `$config_csv_ruta`).
5. Al menú **Administrar → Importació de Dades**, l'aplicació mostrarà primer un avís de confirmació (perquè la importació **esborra i recrea** totes les taules de dades). Cal accedir amb el paràmetre especial afegit a la URL: `...&accio=importar_definitiu66` (aquest sufix numèric actua com a doble confirmació perquè no s'execute per error).

### Què fa exactament la importació (`accio_importar.php`)
- Si no existeix encara el fitxer de base de dades de treball, el crea copiant `bd-SVF/default-Guardies.db`.
- **Elimina i torna a crear** totes les taules: `Guardia`, `Professor`, `Horari`, `Notes`, `Substitucions`, `GuardiaLog`, `Reserves`, `Control`.
- Llig el CSV, n'extrau la llista única de professorat i la insereix a `Professor`.
- Substitueix el nom de professor/a per l'ID intern a la taula `Horari`, junt amb dia, hora, matèria, grup, aula i tipus de sessió.
- Insereix igualment els espais "sense docència" (aules que es poden reservar encara que no tinguen classe assignada) — aquestes línies estan comentades per defecte al codi, ja que ara es poden donar d'alta des del menú **Gestió**.

⚠️ **Atenció**: com que la importació esborra dades existents (guàrdies assignades, substitucions, reserves, control horari), es recomana fer-la només a l'inici de curs o quan es canvie completament l'horari, i sempre després de fer una còpia de seguretat manual de `bd-SVF/Guardies.db`.

## 6. Esquema de la base de dades

Taula creada per la importació (SQLite):

| Taula | Camps principals | Ús |
|---|---|---|
| `Professor` | ID, NOM, MAIL, TUTESO, TUTBAT, TUTCF, TUTSEMI, COCOPE, DEP, DIR | Fitxa de cada professor/a: càrrecs de tutoria, coordinació de cicles (COCOPE), departament, si forma part de l'equip directiu (DIR). |
| `Horari` | ID, IDPROFESSOR, DATA (dia de la setmana L-V), HORA, MATERIA, GRUP, AULA, TIPUS | L'horari setmanal complet: classes, guàrdies (TIPUS='G'), reserves de recolzament (CD/AC/AF/RDP...), etc. |
| `Guardia` | ID, IDPROFESSOR, DATA (data concreta), HORA, COBERTAPER, ACTIVITAT, OBSERVACIONS | Absències puntuals reals i qui les cobreix (`COBERTAPER`). |
| `GuardiaLog` | ID, IDPROFESSOR, DATA, HORA, LOGTIME | Registre/historial d'entrades de guàrdia (auditoria). |
| `Substitucions` | ID, PROFE, SUBSTITUT, DE, A | Substitucions de llarga durada (una persona cobreix tot l'horari d'una altra entre dues dates). |
| `Reserves` | ID, IDPROFESSOR, DATA, HORA, AULA, OBSERVACIONS | Reserves puntuals d'aules/recursos. |
| `Notes` | ID, NOTES, DATA, HORA | Notes/observacions associades a una franja concreta. |
| `Control` | ID, TIPO (IN/OUT), IDPROFESSOR, DATA, HORA, UBICACIO, LATITUD, LONGITUD, DATAHORA_CODI_APP, DATAHORA_QR_GENERAT | Registres del control horari per QR/manual. |

La base de dades s'accedeix sempre a través de **Medoo** (`$db->select`, `$db->insert`, `$db->update`, `$db->delete`, `$db->query` per a consultes SQL directes més complexes).

## 7. Menú d'administració (usuari privilegiat)

Accessible només si `$_SERVER['PHP_AUTH_USER']` coincideix amb `$usuari_privilegiat`. Opcions disponibles (`index.php?accio=...`):

| Menú | `accio` | Funció |
|---|---|---|
| Informes | `informes` | Generació d'informes: informe base (llistat d'absències des d'una data), informe per rang de dates/professor, informe amb gràfic de rànquing de guàrdies fetes vs. cobertes. |
| Full de Control Diari | `control_vaga` | Graella d'assistència/control per a situacions especials (p. ex. vaga), amb totes les franges del dia en columnes. |
| Full de Control Actes | `control_actes` | Llistat agrupat per grup-classe i professor/matèria, pensat per imprimir com a full d'actes. |
| Professor (crear/vore) | `professor` | Alta i edició bàsica del nom d'un professor/a. |
| Professor (horari) | `professor2` | Consulta/edició de les sessions setmanals d'un professor/a concret. |
| Substitucions | `substitucio` | Alta/baixa de substitucions de llarga durada (professor titular ↔ substitut, dates de/a). |
| Llistat de reserves complet | `reserves_llista_completa` | Vista sense restriccions de totes les reserves fetes, amb filtres. |
| Gestió | `gestio` | Alta manual de noves matèries, grups o aules al sistema (per si cal afegir-ne fora de la importació). |
| Editar professor | `professor_editar` | Formulari complet: nom, correu, departament, tutories (ESO/Batxillerat/CF/setmanal), coordinació de cicles, marca de direcció. |
| Importació de Dades | `importar` | Reimportació completa de l'horari des del CSV (vore apartat 5). |
| API Power Automate | `api_powerautomate` | Llistat de les absències donades d'alta automàticament via la integració amb Power Automate (es reconeixen per l'etiqueta `[PW-...]` al camp activitat). |
| Control Horari (manual) | `control_manual` | Registre manual d'entrada/eixida introduint usuari i codi de validació personal. |
| Control Llista Detalls | `control_llista` | Llistat paginat de tots els registres de control horari, amb filtres per professor/data. |
| Control Professor Mensual | `control_llista_professor` | Vista anual/mensual d'un professor concret: hores treballades, diferències respecte a l'horari teòric, calendari amb codi de colors (festius, caps de setmana, sense dades, positiu/negatiu). |
| Llistat de QR | `professor_qr` | Genera i mostra en graella tots els codis QR personals del professorat (per imprimir i repartir), amb el logo del centre incrustat. |

## 8. Integració amb Power Automate

El fitxer independent **`api_powerautomate.php`** (fora del flux normal d'`index.php`) actua com a endpoint HTTP que rep peticions GET amb els paràmetres:

- `ID`: identificador/tipus de permís (p. ex. tipus DT3.8...).
- `Jornada`: Jornada Completa / Incompleta.
- `Data` / `DataFinal`: dates i hora d'inici/fi en format ISO (`AAAA-MM-DDTHH:MM:SSZ`).
- `mail`: correu del professor/a, usat per localitzar-ne l'ID intern a la taula `Professor`.

Amb aquestes dades, crea automàticament una absència (`Guardia`) etiquetada amb `[PW-<ID>]` a l'activitat, perquè es puga identificar posteriorment com a entrada automàtica (vistes des del menú **API Power Automate**). Cal configurar en el flux de Power Automate la crida HTTP a aquesta URL amb els paràmetres indicats.

⚠️ Aquest endpoint no té cap validació addicional més enllà de l'autenticació HTTP Basic del servidor (si s'aplica també sobre aquest fitxer). Cal assegurar-se que el flux de Power Automate incloga les credencials necessàries, o bé restringir l'accés per IP si el servidor ho permet.

## 9. Còpies de seguretat

- **Automàtiques**: cada vegada que es carrega qualsevol pàgina en un dia nou, `index_generar.php` comprova si ja existeix una còpia de la base de dades per al dia d'avui dins de `backup-SVF/` i, si no, en fa una (`AAAA-MM-DD-Guardies.db`). Això genera **una còpia diària automàtica**, sense necessitat d'intervenció.
- **Manuals**: es recomana, a més, fer còpies periòdiques externes (fora del servidor) del fitxer `bd-SVF/Guardies.db`, especialment abans de qualsevol reimportació d'horari.
- La carpeta `backup-SVF/` ha de tindre permisos d'escriptura per al servidor web i, igual que `bd-SVF/`, hauria de quedar fora de l'arrel navegable per seguretat.

## 10. Manteniment i seguretat — punts d'atenció

- **Ubicació de la base de dades**: com adverteix el mateix README, el fitxer SQLite hauria d'ubicar-se fora del directori arrel accessible per web, o protegir-se explícitament, ja que si algú en coneix o endevina la ruta pot descarregar-lo directament.
- **Injecció SQL**: diverses accions construeixen consultes SQL concatenant directament valors de `$_GET`/`$_POST` (per exemple als filtres de `accio_reserves_llista.php`, `accio_control_llista.php`, o a la importació). No hi ha sanejament sistemàtic dels paràmetres d'entrada; es recomana revisar-ho i afegir validació/escapament si el centre té preocupacions de seguretat més enllà de l'ús intern amb autenticació.
- **Gestió d'usuaris**: no hi ha un panell per crear/eliminar usuaris des de l'aplicació; tot es gestiona amb `.htpasswd` directament al servidor.
- **Control horari**: funcionalitat marcada explícitament com "en desenvolupament" pel mateix autor; cal revisar-ne el comportament abans de confiar-hi per a usos amb valor legal/laboral.
- **Calendari i franges horàries**: cal revisar i actualitzar cada curs `$calendari_inicial`, `$calendari_final`, `$calendari_festius` i les franges horàries a `index_config.php`, ja que no s'actualitzen soles.
- **Reimportació d'horari**: esborra dades existents de guàrdies/reserves/substitucions/control horari acumulades. Fer-ho amb precaució i sempre amb còpia de seguretat prèvia.

---

## Annex — Glossari ràpid de termes

- **Guàrdia**: hora en què un professor/a està disponible per cobrir absències d'altres.
- **Recolzament / Reserva**: hores de l'equip directiu o coordinacions (CD, AC, AF, RDP...) que poden acudir a cobrir necessitats puntuals.
- **Substitució**: assignació de llarga durada on una persona substituta assumeix tot l'horari d'una altra durant un període (baixa, permís...).
- **Control horari**: fitxatge d'entrada/eixida mitjançant codi QR personal o validació manual.
- **Usuari mínim**: perfil restringit (p. ex. consergeria) que només veu el dia actual.
- **Usuari privilegiat**: perfil administrador (`admin` per defecte) amb accés total.
