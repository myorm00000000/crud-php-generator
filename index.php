
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
a.btn {
    width: 32px;
}
div {
    margin-top:5px;
}
</style>

<body>
<?php
    include dirname(__FILE__)."/generate.php";
    $object="";
    $host = "<machine>";
    $user = "<user>";
    $pass = "<password>";
    $bd = "<base>";

    // Formulaire
    if (isset($_POST["submit"])) {
        if (isset( $_POST['object'])) {
            // 0. Récupère le formulaire
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
            if (isset($object) && !empty($object) ) {
                create_directory('gen/'.$object);
                recurse_copy('dist','gen/'.$object.'/dist');
                create_directory('gen/'.$object.'/objects');
                create_directory('gen/'.$object.'/inc');
                create_directory('gen/'.$object.'/config');
                create_directory('gen/'.$object.'/dist/js');
                create_directory('gen/'.$object.'/scripts');

            }
            // 2. Génère la classe
            genClass($object, $champs);
            genPageAccueil($object, $champs);
            genDatabase($object,$host, $user, $pass, $bd);
            genPageHead($object, $champs);
            genPageNav($object, $champs);
            genJavascript($object, $champs);
            genCss($object, $champs);
            genScriptJson($object, $champs);
            genCreateTable($object, $champs);
        }
    }
?>

<div class="container">
    <div class="row">
        <h1>Générateur CRUD PHP</h1>
        <form class="input-append" method="post" action="index.php">

            <label class="control-label" for="field1">Base de données</label>
            <div>
                <input autocomplete="off" class="input" id="input<?php echo $host; ?>" name="host" type="text" placeholder="Nom du serveur" data-items="8" value="<?php echo $host; ?>" />
                <input autocomplete="off" class="input" id="input<?php echo $bd; ?>" name="bd" type="text" placeholder="Nom de la BD" data-items="8" value="<?php echo $bd; ?>" />
                <input autocomplete="off" class="input" id="input<?php echo $user; ?>" name="user" type="text" placeholder="Utilisateur" data-items="8" value="<?php echo $user; ?>" />
                <input autocomplete="off" class="input" id="input<?php echo $pass; ?>" name="pass" type="text" placeholder="Password" data-items="8" value="<?php echo $pass; ?>" />
            </div>
            <br>
            <label class="control-label" for="object">Nom de l'objet (sans le <i>'s'</i> à la fin)</label>
            <div id="objet">
                <input autocomplete="off" class="input" id="objet" name="object" type="text" placeholder="Nom de l'objet" data-items="8" value="<?php echo $object; ?>" />
            </div>
            <label class="control-label" for="field1">Liste des champs </label>
            <?php
            $count=1;
            if (isset($champs)) {
            foreach ($champs as $champ) {
                if (!empty($champ)) {
                    echo "<div id=\"field".$count."\">";
                    echo "    <input autocomplete=\"off\" class=\"input\" id=\"".$count."\" name=\"champ[]\" type=\"text\" placeholder=\"Nom de la colonne\" data-items=\"8\" value=\"".$champ."\"/>";
                    echo "    <a id=\"b".$count."\" class=\"btn btn-danger remove-me\" type=\"button\">-</a>";
                    echo "</div>";
                    $count++;
                }
            }
            }
            ?>
            <div id="field<?php echo $count; ?>">
                <input autocomplete="off" class="input" id="input<?php echo $count; ?>" name="champ[]" type="text" placeholder="Nom de la colonne" data-items="8"/>
                <a id="b<?php echo $count; ?>" class="btn btn-primary add-more" type="button">+</a>
            </div>
            <div class="form-group">
                <input id="submit" name="submit" type="submit" value="Génération" class="btn btn-primary">
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){
    var next = <?php echo $count; ?>;
    $(document).on('click', '.add-more', function(e) {
        e.preventDefault();
        var precField = "#field" + next;
        next = next + 1;
        var newField = $('<div id="field'+next+'"></div>');
        var newInput = $('<input autocomplete="off" class="input" name="champ[]"  id="input'+next+'" type="text" placeholder="Nom de la colonne" data-items="8"/>');
        var newBtn = $('<a id="b'+next+'" class="btn btn-primary add-more" type="button">+</a>');
        $(precField).after(newField);
        $(newField).append(newInput);
        $(newField).append(newBtn);
        var removeBtn = $('<a id="remove'+ (next - 1) + '" class="btn btn-danger remove-me" >-</a>');
         $(precField + " a").remove();
         $(precField).append(removeBtn);
    });
    
    $(document).on('click', '.remove-me', function(e) {
        e.preventDefault();
        var fieldNum = this.id.charAt(this.id.length-1);
        var fieldID = $("#field" + fieldNum);
        $(fieldID).remove();   
    });    
});
</script>
</body></html>
