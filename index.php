
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CRUD Generator">
    <meta name="author" content="L.Capdecomme">
    <title>CRUD Generator</title>
    <script src="dist/js/jquery.min.js"></script>
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <script src="dist/js/bootstrap.min.js"></script>
</head>

<style>
input[type="text"] {margin-right:20px;border: 1px solid #ccc;border-radius:4px;box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
color: #555;padding: 6px 12px;width: 80%;}
input#submitGen {width: 100%;margin: 15px 0 50px;}
label {margin-top: 8px;}
input.readonly {width: 80%;}
input.btn.load {width: 60px;display: inline;}
div {margin-top:5px;}
#objects, .project, .bases {border-radius: 4px; background-color: #E5E5E5;padding: 2px 10px 25px 20px;margin-top: 20px;}
.object {padding-top:10px;}
.ope {display: inline-block;width: 10px;}
.newObjectButton {padding-top:20px;}
</style>

<body>
<?php
    ini_set('display_errors',1);
    error_reporting(E_ALL);
    include dirname(__FILE__)."/generate.php";
    $MAX_OBJECT = 100;
    $fileTemplate = "";
    $object="";
    $project="";
    $host = "<machine>";
    $user = "<user>";
    $pass = "<password>";
    $bd = "<base>";
    if (isset( $_POST['project']) && !empty($_POST['project']) ) {
        $project = strtolower($_POST['project']);
        // Nom du fichier
        $fileTemplate  = "modeles/" . $project . ".crud_tmplt";
    }
    if (isset( $_POST['host']) && !empty( $_POST['host']) ) {
        $host = $_POST['host'];
    }
    if (isset( $_POST['user']) && !empty( $_POST['user'])) {
        $user = $_POST['user'];
    }
    if (isset( $_POST['pass']) && !empty( $_POST['pass'])) {
        $pass = $_POST['pass'];
    }
    if (isset( $_POST['bd']) && !empty( $_POST['bd'])) {
                $bd = $_POST['bd'];
    }

    // Formulaire - Bouton Generation
    if (isset($_POST["generation"]) && isset($project)) {
        // 1 Génére la structure de ce projet
        create_directory('modeles');
        create_directory('gen/'.$project);
        recurse_copy('dist','gen/'.$project.'/dist');
        create_directory('gen/'.$project.'/objects');
        create_directory('gen/'.$project.'/inc');
        create_directory('gen/'.$project.'/config');
        create_directory('gen/'.$project.'/dist/js');
        create_directory('gen/'.$project.'/scripts');

        // 2. Sauve ce modèle sur disque
        sauveModele($fileTemplate, $project, $host, $user, $pass, $bd);

        $cpt = 1;
        $inc = 1;
        // 3. Recherche des objets pour les générer
        while($inc<$MAX_OBJECT){
            $nomObjet = "object" . $inc;
            $nomChamps = "champ" . $inc;
            // Si cet objet est recu du formulaire et qu'il existe vraiment
            if (isset( $_POST[$nomObjet]) && !empty($_POST[$nomObjet]) ) {
                $object = strtolower($_POST[$nomObjet]);
                // trim sur chaque élement du tableau + supp. des élements vides
                $champs = array_filter(array_map('trim', $_POST[$nomChamps]));

                // 3.1 Génère les classes PHP pour cet objet
                genClass($project, $object, $champs);
                genPageAccueil($project, $object, $champs);
                genDatabase($project, $object,$host, $user, $pass, $bd);
                genPageHead($project, $object, $champs);
                genPageNav($project, $object, $champs);
                genJavascript($project, $object, $champs);
                genCss($project, $object, $champs);
                genScriptJson($project, $object, $champs);
                genCreateTable($project, $object, $champs);
                // 3.2 Ajoute cet objet dans le fichier modèle
                ajouteModele($fileTemplate, $project, $cpt, $object, $champs);
                $cpt++;
            }
            $inc++;
        }
    }
?>

<div class="container">
    <div class="row">
        <h1>Générateur CRUD PHP</h1>
        <form class="input-append" method="post" action="index.php">

            <div class="project">
                <h2>Nom du projet</h2>
                <div id="project">
                    <input autocomplete="off" class="input" id="nameProject" name="project" required type="text" placeholder="Nom du projet" value="<?php echo $project; ?>" />
                    <input id="submitLect" name="chargement" type="submit" value="Lect." class="btn btn-primary load">
                    <diV>Les modèles existants :<strong> 
<?php
if ($handle = opendir('modeles')) {

    while (false !== ($entry = readdir($handle))) {

        if ($entry != "." && $entry != "..") {
            $fileName = substr($entry, 0, strpos($entry, '.'));
            echo "<span class='fileTemplate'>".$fileName."</span>&nbsp;&nbsp;&nbsp;";
        }
    }

    closedir($handle);
}
?>
                </strong></div>
                </div>
            </div>

            <div class="bases">
                <h2>Base de données</h2>
                <div>
                    <input autocomplete="off" class="input" id="input<?php echo $host; ?>" name="host" type="text" placeholder="Nom du serveur"  value="<?php echo $host; ?>" />
                    <input autocomplete="off" class="input" id="input<?php echo $bd; ?>" name="bd" type="text" placeholder="Nom de la BD" value="<?php echo $bd; ?>" />
                    <input autocomplete="off" class="input" id="input<?php echo $user; ?>" name="user" type="text" placeholder="Utilisateur" value="<?php echo $user; ?>" />
                    <input autocomplete="off" class="input" id="input<?php echo $pass; ?>" name="pass" type="text" placeholder="Password" value="<?php echo $pass; ?>" />
                </div>
            </div>

            <div id="objects">
            <h2>Objets à générer</h2>

            <?php    
            $nbObject=0;
            $numChamp=0;
            if (isset($fileTemplate) && trim($fileTemplate)!="" && file_exists($fileTemplate)) {
                $handle = fopen($fileTemplate, "r");
                if ($handle) {
                    while (($line = fgets($handle)) !== false) 
                    {
                        if (startsWith($line, "objet"))
                        {    
                            // Si ce n'est pas le premier objet, on ferme le div de l'objet précédent                
                            if ($nbObject!=0) {
                                echo "</div>";
                            }
                            $nbObject++;    
                            $numChamp=0;
                            $objet = strstr($line, ':');
                            $objet = ltrim($objet, ':');
                            echo "<div  id='div-object".$nbObject."' class='object'>";
                            echo "<label class='control-label' for='object'>".$nbObject."/ Nom de l'objet (sans le <i>'s'</i> à la fin)</label>";
                            echo "<input autocomplete='off' class='input' id='object".$nbObject."' name='object".$nbObject."' type='text' placeholder='Nom de l\'objet' value='".trim($objet)."' />";
                            echo "<a class='btn btn-danger remove-me' type='button' data-id='div-object".$nbObject."'><span class='ope'>-</span></a>";
                            echo "<div><label class='control-label'>Liste des champs </label></div>";
                            echo "<input readonly class='form-control readonly' type='text' value='id' />";
                        }
                        // Nombre d'éléments
                        if (startsWith($line, "nombre")) 
                        {                 
                            $nombreChamps = strstr($line, ':');
                            $nombreChamps = ltrim($nombreChamps, ':');
                        }                        
                        if (startsWith($line, "champ")) 
                        {                    
                            $numChamp++;
                            $champ = strstr($line, ':');
                            $champ = ltrim($champ, ':');
                            echo "<div id='div-field". $nbObject."-".$numChamp."'>";
                            echo "<input autocomplete='off' class='input' id='object".$nbObject."-".$numChamp."' name='champ".$nbObject."[]' type='text' placeholder='Nom de la colonne' value='".trim($champ)."'/>";
                            if ($numChamp<$nombreChamps) {
                                echo "<a data-id='div-field".$nbObject."-".$numChamp."' class='btn btn-danger remove-me' type='button'><span class='ope'>-</span></a>";
                            }
                            else {
                                echo "<a class='btn btn-primary add-more-column' type='button' data-input='1' data-object='".$nbObject."' ><span class='ope'>+</span></a>";
                            }
                            echo "</div>";

                        }
                    }
                    // Si ce n'est pas le premier objet, on ferme le div de l'objet précédent                
                    if ($nbObject!=0) {
                        echo "</div>";
                    }

                }                   
            }

            ?>
                <div class="form-group newObjectButton">
                    <a class='btn btn-primary add-more-object' type='button'>Nouvel Objet</a>
                </div>
            </div>
            <div class="form-group">
                <input id="submitGen" name="generation" type="submit" value="Génération" class="btn btn-primary">
                <input type="hidden" id="nbObjects" value="<?php echo $nbObject; ?>"/>
            </div>
        </form>
    </div>
</div>


<script>
$(document).ready(function(){

    /* 
    * Ajoute un objet
    */
    $(document).on('click', '.add-more-object', function(e) {
        e.preventDefault();
        var next = $("#nbObjects").val();
        next = parseFloat(next) + 1;
        var newObject = $("<div id='div-object"+next+"' class='object'/>");
        var newLabel = $("<label class='control-label' for='object'>"+next+"/ Nom de l'objet (sans le <i>'s'</i> à la fin)</label>");
        //var newDiv = $("<div id='divObject"+next+"' />");
        var newInput = $("<input autocomplete='off' class='input' id='object"+next+"' name='object"+next+"' type='text' placeholder=\"Nom de l'objet\" />");
        var removeBtn = $("<a class='btn btn-danger remove-me' data-id='div-object"+next+"'><span class='ope'>-</span></a>");
        var newLabelChamp = $("<div><label class='control-label'>Liste des champs</label></div>");
        var newDivChamp = $("<div id='div-field"+next+"-1'>");
        var newInputChamp = $("<input autocomplete='off' class='input' id='object"+next+"-1' name='champ"+next+"[]' type='text' placeholder='Nom de la colonne'/>");
        var addNewBtn = $("<a class='btn btn-primary add-more-column' type='button' data-input='1' data-object='"+next+"' ><span class='ope'>+</span></a>");
        // Champ id desactive
        var newInputChampId = $("<input readonly class='form-control readonly' type='text' value='id' />");

        // Ajout d'un objet : ajout un titre, un input pour l'objet, un bouton pour le supprimer, et un premier champ
        $(newObject).append(newLabel);
        $(newObject).append(newInput);
        $(newObject).append(removeBtn);
        $(newObject).append(newLabelChamp);
        // Un nouveau champ id desactive
        $(newObject).append(newInputChampId);
        // Un nouveau champ avec un input et un bouton
        $(newDivChamp).append(newInputChamp);
        $(newDivChamp).append(addNewBtn);
        $(newObject).append(newDivChamp);
        // Ajout de cet objet dans la liste des objets
        $("#objects").append(newObject);
        // Recompte le nombre d'objets
        recompteObjects();
    });
    

    /* 
    * Ajoute une colonne
    */
    $(document).on('click', '.add-more-column', function(e) {
        e.preventDefault();
        var idObject = $(this).attr("data-object");
        var idInput = $(this).attr("data-input");
        var existingObject = $("#div-object"+idObject);
        nextInput = parseFloat(idInput) + 1;
        var newDivChamp = $("<div id='div-field"+idObject+"-"+nextInput+"'>");
        var newInputChamp = $("<input autocomplete='off' class='input' id='object"+idObject+"-"+nextInput+"' name='champ"+idObject+"[]' type='text' placeholder='Nom de la colonne'/>");
        var addNewBtn = $("<a class='btn btn-primary add-more-column' type='button' data-input='"+nextInput+"' data-object='"+idObject+"' ><span class='ope'>+</span></a>");

        $(existingObject).append(newDivChamp);
        $(newDivChamp).append(newInputChamp);
        $(newDivChamp).append(addNewBtn);

        // Suppression du bouton "add" sur le champ précédent
        var precAddBtn = $("#div-field"+idObject+"-"+idInput+" a");
        $(precAddBtn).remove();
        // Ajout du bouton "remove" sur le champ précédent
        var precField = $("#div-field"+idObject+"-"+idInput);
        var newRemoveBtn = $("<a class='btn btn-danger remove-me' type='button'  data-id='div-field"+idObject+"-"+idInput+"'><span class='ope'>-</span></a>");
        $(precField).append(newRemoveBtn);
    });
      

    /* 
    * Click sur un fichier modele 
    */
    $(document).on('click', '.fileTemplate', function(e) {
        e.preventDefault();
        var fichierModele = $(this).text();
        $("#nameProject").val(fichierModele);
    });    


    /* 
    * Supprime une colonne ou un objet
    */
    $(document).on('click', '.remove-me', function(e) {
        e.preventDefault();
        var idObject = "#"+$(this).attr("data-id");
        $(idObject).remove();   
        // Recompte le nombre d'objets
        recompteObjects();
    });    


    /**
     * Initialisation de l'application 
     * 
     */
    function recompteObjects() {
        var numItems = $('.object').length;
        $("#nbObjects").val(numItems);
    };

    
    /**
     * Initialisation de l'application dès que le DOM est chargé
     */
    $(document).ready(recompteObjects);


});
</script>
</body></html>
