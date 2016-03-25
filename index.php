
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CRUD Generator</title>

    <script src="dist/js/jquery.min.js"></script>
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <script src="dist/js/bootstrap.min.js"></script>

  </head>


<style>
* {
  .border-radius(0) !important;
}
body {
  padding-top: 50px;
}
input {
    margin-right:20px;
}
a.btn.add-more-column, a.btn.remove-me {
    width: 32px;
}
div {
    margin-top:5px;
}
.object, .project, .bases {
    background-color: #E5E5E5;
    padding: 10px 10px 20px;
}
</style>

<body>
<?php
    include dirname(__FILE__)."/generate.php";
    $object="";
    $project="";
    $host = "<machine>";
    $user = "<user>";
    $pass = "<password>";
    $bd = "<base>";

    // Formulaire
    if (isset($_POST["submit"])) {
        if (isset( $_POST['project']) && isset( $_POST['object'])) {
            // 0. Récupère le formulaire
            $project = strtolower($_POST['project']);
            $object = strtolower($_POST['object']);
            $champs = $_POST['champ'];
            if (isset( $_POST['host']) && !empty( $_POST['host']) ) 
                $host = $_POST['host'];
            else 
                $host = "<machine>";
            if (isset( $_POST['user']) && !empty( $_POST['user'])) 
                $user = $_POST['user'];
            else 
                $user = "<user>";
            if (isset( $_POST['pass']) && !empty( $_POST['pass'])) 
                $pass = $_POST['pass'];
            else 
                $pass = "<password>";
            if (isset( $_POST['bd']) && !empty( $_POST['bd'])) 
                $bd = $_POST['bd'];
            else 
                $bd = "<base>";
           
            // 1 Génére la structure
            if (isset($project) && !empty($project) ) {
                create_directory('modele');
                create_directory('gen/'.$project);
                recurse_copy('dist','gen/'.$project.'/dist');
                create_directory('gen/'.$project.'/objects');
                create_directory('gen/'.$project.'/inc');
                create_directory('gen/'.$project.'/config');
                create_directory('gen/'.$project.'/dist/js');
                create_directory('gen/'.$project.'/scripts');

            }
            // 2. Génère la classe
            genClass($project, $object, $champs);
            genPageAccueil($project, $object, $champs);
            genDatabase($project, $object,$host, $user, $pass, $bd);
            genPageHead($project, $object, $champs);
            genPageNav($project, $object, $champs);
            genJavascript($project, $object, $champs);
            genCss($project, $object, $champs);
            genScriptJson($project, $object, $champs);
            genCreateTable($project, $object, $champs);

            // 3. Sauve le modèle
            sauveModele($project, $host, $user, $pass, $bd, $object, $champs);
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
                    <input autocomplete="off" class="input" id="project" name="project" required type="text" placeholder="Nom du projet" value="<?php echo $project; ?>" />
                </div>
            </div>
            <hr>
            <div class="bases">
                <h2>Base de données</h2>
                <div>
                    <input autocomplete="off" class="input" id="input<?php echo $host; ?>" name="host" type="text" placeholder="Nom du serveur"  value="<?php echo $host; ?>" />
                    <input autocomplete="off" class="input" id="input<?php echo $bd; ?>" name="bd" type="text" placeholder="Nom de la BD" value="<?php echo $bd; ?>" />
                    <input autocomplete="off" class="input" id="input<?php echo $user; ?>" name="user" type="text" placeholder="Utilisateur" value="<?php echo $user; ?>" />
                    <input autocomplete="off" class="input" id="input<?php echo $pass; ?>" name="pass" type="text" placeholder="Password" value="<?php echo $pass; ?>" />
                </div>
            </div>
            <hr>
            <h2>Objets à générer</h2>
            <div id="objects">
            <?php
            $count=1;
            $nbObject = 0;
            for($i = 1; $i < 10; $i++) 
            {
                if (isset(${'object' . $i})) {
                    $nbObject++;
                    echo "<div class='object'>";
                    echo "<label class='control-label' for='object'>Nom de l'objet (sans le <i>'s'</i> à la fin)</label>";
                    echo "<div id='divObject".$nbObject."'>";
                    echo "<input autocomplete='off' class='input' id='object".$nbObject."' name='object".$nbObject."' type='text' placeholder='Nom de l\'objet' value='".${'object'.$nbObject}.";' />";
                    echo "</div>";
                    echo "<label class='control-label'>Liste des champs </label>";
                    $count=1;
                    if (isset($champs)) {
                        foreach ($champs as $champ) {
                            if (!empty($champ)) {
                                echo "<div class='field'>";
                                echo "    <input autocomplete='off' class='input' id='".$nbObject."-".$count."' name='champ[]' type='text' placeholder='Nom de la colonne' value='".$champ."'/>";
                                echo "    <a id='b".$nbObject."-".$count."' class='btn btn-danger remove-me' type='button'>-</a>";
                                echo "</div>";
                                $count++;
                            }
                        }
                    }
                    echo "<div class='field'>";
                    echo "    <input autocomplete='off' class='input' id='".$count."' name='champ[]' type='text' placeholder='Nom de la colonne' />";
                    echo "    <a class='btn btn-primary add-more-column' type='button'>+</a>";
                    echo "</div>";
                    echo "</div>";
                }
            }
            ?>
            </div>
            <div class="form-group">
                <a class='btn btn-primary add-more-object' type='button'>Nouvel Objet</a>
            </div>
            <hr>
            <div class="form-group">
                <input id="submitGen" name="submit" type="submit" value="Génération" class="btn btn-primary">
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
        var newLabel = $("<div><label class='control-label' for='object'>"+next+"/ Nom de l'objet (sans le <i>'s'</i> à la fin)</label></div>");
        //var newDiv = $("<div id='divObject"+next+"' />");
        var newInput = $("<input autocomplete='off' class='input' id='object"+next+"' name='object"+next+"' type='text' placeholder=\"Nom de l'objet\" />");
        var removeBtn = $('<a class="btn btn-danger remove-me" data-id="div-object'+next+'">-</a>');
        var newLabelChamp = $("<div><label class='control-label'>Liste des champs</label></div>");
        var newDivChamp = $("<div id='field"+next+"-1'>");
        var newInputChamp = $("<input autocomplete='off' class='input' id='object"+next+"-1' name='champ"+next+"[]' type='text' placeholder='Nom de la colonne'/>");
        var addNewBtn = $("<a class='btn btn-primary add-more-column' type='button' data-input='1' data-object='"+next+"' >+</a>");

        // Ajout d'un objet : ajout un titre, un input pour l'objet, un bouton pour le supprimer, et un premier champ
        $(newObject).append(newLabel);
        $(newObject).append(newInput);
        $(newObject).append(removeBtn);
        $(newObject).append(newLabelChamp);
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
        var newDivChamp = $("<div id='field"+idObject+"-"+nextInput+"'>");
        var newInputChamp = $("<input autocomplete='off' class='input' id='object"+idObject+"-"+nextInput+"' name='champ"+idObject+"[]' type='text' placeholder='Nom de la colonne'/>");
        var addNewBtn = $("<a class='btn btn-primary add-more-column' type='button' data-input='"+nextInput+"' data-object='"+idObject+"' >+</a>");

        $(existingObject).append(newDivChamp);
        $(newDivChamp).append(newInputChamp);
        $(newDivChamp).append(addNewBtn);

        // Suppression du bouton "add" sur le champ précédent
        var precAddBtn = $("#field"+idObject+"-"+idInput+" a");
        $(precAddBtn).remove();
        // Ajout du bouton "remove" sur le champ précédent
        var precField = $("#field"+idObject+"-"+idInput);
        var newRemoveBtn = $("<a class='btn btn-danger remove-me' type='button'  data-id='field"+idObject+"-"+idInput+"'>-</a>");
        $(precField).append(newRemoveBtn);
    });
      

    /* 
    * Supprime une colonne ou un objet
    */
    $(document).on('click', '.remove-me', function(e) {
        e.preventDefault();
        var idObject = "#"+$(this).attr("data-id");
        alert(idObject);      
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
