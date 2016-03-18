
<?php


function genClass($object, $champs)
{

	$objectMaj = ucfirst ( $object );
	$fp = fopen("gen/". $object . "/objects/" . $object . ".php","wb");

	fwrite($fp,"<?php". PHP_EOL);
	fwrite($fp,"class $objectMaj {". PHP_EOL);
	fwrite($fp," ". PHP_EOL);
	fwrite($fp,"    // database connection and table name". PHP_EOL);
	fwrite($fp,"    private \$conn;". PHP_EOL);
	fwrite($fp," ". PHP_EOL);
	fwrite($fp,"    // object properties". PHP_EOL);
	fwrite($fp,"    public \$".$object."_id;". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"    public \$".$object."_".$champ.";" . PHP_EOL);
		}
	}
	fwrite($fp," ". PHP_EOL);
	fwrite($fp,"    public function __construct(\$db){". PHP_EOL);
	fwrite($fp,"        \$this->conn = \$db;". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    // Retourne l'objet courant". PHP_EOL);
	fwrite($fp,"    function charge".$objectMaj."()". PHP_EOL);
	fwrite($fp,"    {". PHP_EOL);
	fwrite($fp,"      // Requete pour retrouver un objet pour un ID donné". PHP_EOL);
	fwrite($fp,"      \$query = \"select * from ".$object." where ".$object."_id=:id LIMIT 0, 1\";". PHP_EOL);
	fwrite($fp,"      \$stmt = \$this->conn->prepare( \$query );". PHP_EOL);
	fwrite($fp,"      \$stmt->bindParam(':id', \$this->".$object."_id);". PHP_EOL);
	fwrite($fp,"      \$stmt->execute();". PHP_EOL);
	fwrite($fp,"      if (\$stmt->rowCount()>0) {". PHP_EOL);
	fwrite($fp,"        \$row = \$stmt->fetch(PDO::FETCH_ASSOC);". PHP_EOL);
	fwrite($fp,"        extract(\$row);". PHP_EOL);
	fwrite($fp,"        \$this->".$object."_id=\$".$object."_id;". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"        \$this->".$object."_".$champ."=\$".$object."_".$champ.";". PHP_EOL);
		}
	}
	fwrite($fp,"        return true;". PHP_EOL);
	fwrite($fp,"      }". PHP_EOL);
	fwrite($fp,"      return false;". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    // Retourne l'objet $object courant". PHP_EOL);
	fwrite($fp,"    function charge".$objectMaj."Courant()". PHP_EOL);
	fwrite($fp,"    {". PHP_EOL);
	fwrite($fp,"      // Requete pour retrouver le dernier ID inséré". PHP_EOL);
	fwrite($fp,"      \$query = \"select * from ".$object." order by ".$object."_id desc LIMIT 0, 1\";". PHP_EOL);
	fwrite($fp,"      \$stmt = \$this->conn->prepare( \$query );". PHP_EOL);
	fwrite($fp,"      \$stmt->execute();". PHP_EOL);
	fwrite($fp,"      \$row = \$stmt->fetch(PDO::FETCH_ASSOC);". PHP_EOL);
	fwrite($fp,"      extract(\$row);". PHP_EOL);
	fwrite($fp,"      \$this->".$object."_id=\$".$object."_id;". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"      \$this->".$object."_".$champ."=\$".$object."_".$champ.";". PHP_EOL);
		}
	}
	fwrite($fp,"      return \$this;". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    // Retourne tous les objets". PHP_EOL);
	fwrite($fp,"    function lit".$objectMaj."s()". PHP_EOL);
	fwrite($fp,"    {". PHP_EOL);
	fwrite($fp,"      // Requete pour retrouver tous les objets dans l'ordre d'insertion en base". PHP_EOL);
	fwrite($fp,"      \$query = \"select * from ".$object." order by ".$object."_id asc\";". PHP_EOL);
	fwrite($fp,"      \$stmt = \$this->conn->prepare( \$query );". PHP_EOL);
	fwrite($fp,"      \$stmt->execute();". PHP_EOL);
	fwrite($fp,"      return \$stmt;". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"   // Ajoute un objet". PHP_EOL);
	fwrite($fp,"    function ajoute".$objectMaj."(){". PHP_EOL);
	fwrite($fp,"         try {". PHP_EOL);
	$liste="";
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			$liste = $liste.",".$object."_".$champ;
		}
	}
	$liste = substr($liste, 1);
	fwrite($fp,"          \$query= \"insert into ".$object." (".$liste.") ". PHP_EOL);
	$liste="";
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			$liste = $liste.", :".$object."_".$champ;
		}
	}
	 $liste = substr($liste, 1);
	fwrite($fp,"                  values (".$liste.")\";". PHP_EOL);
	fwrite($fp,"         \$stmt = \$this->conn->prepare(\$query);". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"        \$stmt->bindParam(':".$object."_".$champ."', \$this->".$object."_".$champ.");". PHP_EOL);
		}
	}
	fwrite($fp,"        if (\$stmt->execute()) {". PHP_EOL);
	fwrite($fp,"            return true;". PHP_EOL);
	fwrite($fp,"        }   else{". PHP_EOL);
	fwrite($fp,"            return \$stmt->errorInfo();". PHP_EOL);
	fwrite($fp,"        }". PHP_EOL);
	fwrite($fp,"        }". PHP_EOL);
	fwrite($fp,"        catch(PDOException \$exception) {". PHP_EOL);
	fwrite($fp,"            echo \"Ajoute un objet ".$object." : \" . \$this->host . \" : \" . \$exception->getMessage();". PHP_EOL);
	fwrite($fp,"        }". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    // Modifie un objet". PHP_EOL);
	fwrite($fp,"    function modifie".$objectMaj."(){ ". PHP_EOL);
	fwrite($fp,"        try {". PHP_EOL);
	fwrite($fp,"        \$query = \"update ".$object." set". PHP_EOL);
	$i = 1;
	$len = count($champs);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			if ($i == $len - 1) {
				fwrite($fp,"                    ".$object."_".$champ." = :".$object."_".$champ. PHP_EOL);
			} else {
				fwrite($fp,"                    ".$object."_".$champ." = :".$object."_".$champ.",". PHP_EOL);
			}
			// …
			$i++;
		}
	}
	fwrite($fp,"                WHERE". PHP_EOL);
	fwrite($fp,"                    ".$object."_id = :id\";". PHP_EOL);
	fwrite($fp,"        \$stmt = \$this->conn->prepare(\$query);". PHP_EOL);
	fwrite($fp,"        \$stmt->bindParam(':id', \$this->".$object."_id);". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"        \$stmt->bindParam(':".$object."_".$champ."', \$this->".$object."_".$champ.");". PHP_EOL);
		}
	}
	fwrite($fp,"        if (\$stmt->execute()) {". PHP_EOL);
	fwrite($fp,"            return true;". PHP_EOL);
	fwrite($fp,"        }   else{". PHP_EOL);
	fwrite($fp,"            return \$stmt->errorInfo();". PHP_EOL);
	fwrite($fp,"        }". PHP_EOL);
	fwrite($fp,"        }catch(PDOException \$exception){". PHP_EOL);
	fwrite($fp,"            echo \"Modifie un objet ".$object." : \" . \$this->host . \" : \" . \$exception->getMessage();". PHP_EOL);
	fwrite($fp,"        }". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    // Suppression un objet". PHP_EOL);
	fwrite($fp,"    function efface".$objectMaj."(){ ". PHP_EOL);
	fwrite($fp,"        try {". PHP_EOL);
	fwrite($fp,"        \$query = \"delete from ".$object." where ". PHP_EOL);
	fwrite($fp,"                    ".$object."_id = :id\";". PHP_EOL);
	fwrite($fp,"        \$stmt = \$this->conn->prepare(\$query);". PHP_EOL);
	fwrite($fp,"        \$stmt->bindParam(':id', \$this->".$object."_id);". PHP_EOL);
	fwrite($fp,"        if (\$stmt->execute()) {". PHP_EOL);
	fwrite($fp,"            return true;". PHP_EOL);
	fwrite($fp,"        }   else{". PHP_EOL);
	fwrite($fp,"            return \$stmt->errorInfo();". PHP_EOL);
	fwrite($fp,"        }". PHP_EOL);
	fwrite($fp,"        }catch(PDOException \$exception){". PHP_EOL);
	fwrite($fp,"            echo \"Modifie un objet ".$object." : \" . \$this->host . \" : \" . \$exception->getMessage();". PHP_EOL);
	fwrite($fp,"        }". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"}". PHP_EOL);
	fwrite($fp,"?>". PHP_EOL);
	fclose($fp);
}


function genJavascript($object, $champs)
{
	$objectMaj = ucfirst ( $object );
	$fp = fopen("gen/". $object . "/dist/js/" . $object . ".js","wb");

	fwrite($fp,"(function(\$) {". PHP_EOL);
	fwrite($fp,"	'use strict';". PHP_EOL);
	fwrite($fp,"		". PHP_EOL);
	fwrite($fp,"	/**". PHP_EOL);
	fwrite($fp,"	 * Initialisation de l'application ". PHP_EOL);
	fwrite($fp,"	 * ". PHP_EOL);
	fwrite($fp,"	 */". PHP_EOL);
	fwrite($fp,"	function initApplication() {". PHP_EOL);
	fwrite($fp,"			toastr.options = {". PHP_EOL);
	fwrite($fp,"				  \"closeButton\": false,". PHP_EOL);
	fwrite($fp,"				  \"debug\": false,". PHP_EOL);
	fwrite($fp,"				  \"newestOnTop\": false,". PHP_EOL);
	fwrite($fp,"				  \"progressBar\": false,". PHP_EOL);
	fwrite($fp,"				  \"positionClass\": \"toast-top-center\",". PHP_EOL);
	fwrite($fp,"				  \"preventDuplicates\": false,". PHP_EOL);
	fwrite($fp,"				  \"onclick\": null,". PHP_EOL);
	fwrite($fp,"				  \"showDuration\": \"300\",". PHP_EOL);
	fwrite($fp,"				  \"hideDuration\": \"1000\",". PHP_EOL);
	fwrite($fp,"				  \"timeOut\": \"5000\",". PHP_EOL);
	fwrite($fp,"				  \"extendedTimeOut\": \"1000\",". PHP_EOL);
	fwrite($fp,"				  \"showEasing\": \"swing\",". PHP_EOL);
	fwrite($fp,"				  \"hideEasing\": \"linear\",". PHP_EOL);
	fwrite($fp,"				  \"showMethod\": \"fadeIn\",". PHP_EOL);
	fwrite($fp,"				  \"hideMethod\": \"fadeOut\"". PHP_EOL);
	fwrite($fp,"				}". PHP_EOL);
	fwrite($fp,"		// Bug Safari sur iphone : ". PHP_EOL);
	fwrite($fp,"		//http://stackoverflow.com/questions/2898740/iphone-safari-web-app-opens-links-in-new-window". PHP_EOL);
	fwrite($fp,"		\$(\"a\").click(function (event) {". PHP_EOL);
	fwrite($fp,"		    event.preventDefault();". PHP_EOL);
	fwrite($fp,"	    	window.location = \$(this).attr(\"href\");". PHP_EOL);
	fwrite($fp,"		});". PHP_EOL);
	fwrite($fp,"	};". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"	/**". PHP_EOL);
	fwrite($fp,"	* Sauver ".$objectMaj. PHP_EOL);
	fwrite($fp,"	*/". PHP_EOL);
	fwrite($fp,"	\$(function() {". PHP_EOL);
	fwrite($fp,"	  	\$('#sauver".$objectMaj."').click( function(e) {". PHP_EOL);
	fwrite($fp,"			e.preventDefault();". PHP_EOL);
	fwrite($fp,"			var id".$objectMaj." = \$('#id".$objectMaj."').val(); ". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"			var ".$champ." = \$('#".$champ.$objectMaj."').val(); ". PHP_EOL);
		}
	}
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"			\$.ajax({". PHP_EOL);
	fwrite($fp,"				type: \"POST\",". PHP_EOL);
	fwrite($fp,"				cache: false,". PHP_EOL);
	$listeChamps="";
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			$listeChamps = $listeChamps . ", " . $champ . " : " .$champ;
		}
	}
	fwrite($fp,"				data: {	op : 'M', id : id".$objectMaj.$listeChamps." },". PHP_EOL);
	fwrite($fp,"				url : \"scripts/ws".$objectMaj.".php\",". PHP_EOL);
	fwrite($fp,"				success : function( msg, status,xhr ) {". PHP_EOL);
	fwrite($fp,"					var p = msg.resultat;". PHP_EOL);
	fwrite($fp,"					if (p==true) {". PHP_EOL);
	fwrite($fp,"						toastr.success(\"Modification enregistrée\", \"".$objectMaj."\");". PHP_EOL);
	fwrite($fp,"						}	". PHP_EOL);
	fwrite($fp,"						else {". PHP_EOL);
	fwrite($fp,"						toastr.error(p, \"".$objectMaj."\");". PHP_EOL);
	fwrite($fp,"						}			". PHP_EOL);
	fwrite($fp,"				},". PHP_EOL);
	fwrite($fp,"				error : function( msg, status,xhr ) {". PHP_EOL);
	fwrite($fp,"					toastr.error(msg + \"(\"+status+\")\", \"".$objectMaj."\");". PHP_EOL);
	fwrite($fp,"				}". PHP_EOL);
	fwrite($fp,"			});  		". PHP_EOL);
	fwrite($fp,"		});". PHP_EOL);
	fwrite($fp,"	});". PHP_EOL);
	fwrite($fp,"	/**". PHP_EOL);

	fwrite($fp,"	* Effacer ".$objectMaj. PHP_EOL);
	fwrite($fp,"	*/". PHP_EOL);
	fwrite($fp,"	\$(function() {". PHP_EOL);
	fwrite($fp,"	  	\$('#effacer".$objectMaj."').click( function(e) {". PHP_EOL);
	fwrite($fp,"			e.preventDefault();". PHP_EOL);
	fwrite($fp,"			var id".$objectMaj." = \$('#id".$objectMaj."Delete').val(); ". PHP_EOL);
	fwrite($fp,"			\$.ajax({". PHP_EOL);
	fwrite($fp,"				type: \"POST\",". PHP_EOL);
	fwrite($fp,"				cache: false,". PHP_EOL);
	fwrite($fp,"				data: {	op : 'D', id : id".$objectMaj." },". PHP_EOL);
	fwrite($fp,"				url : \"scripts/ws".$objectMaj.".php\",". PHP_EOL);
	fwrite($fp,"				success : function( msg, status,xhr ) {". PHP_EOL);
	fwrite($fp,"					var p = msg.resultat;". PHP_EOL);
	fwrite($fp,"					if (p==true) {". PHP_EOL);
	fwrite($fp,"						toastr.success(\"Suppression effectuée\", \"".$objectMaj."\");". PHP_EOL);
	fwrite($fp,"						}	". PHP_EOL);
	fwrite($fp,"						else {". PHP_EOL);
	fwrite($fp,"						toastr.error(p, \"".$objectMaj."\");". PHP_EOL);
	fwrite($fp,"						}			". PHP_EOL);
	fwrite($fp,"				},". PHP_EOL);
	fwrite($fp,"				error : function( msg, status,xhr ) {". PHP_EOL);
	fwrite($fp,"					toastr.error(msg + \"(\"+status+\")\", \"".$objectMaj."\");". PHP_EOL);
	fwrite($fp,"				}". PHP_EOL);
	fwrite($fp,"			});  		". PHP_EOL);
	fwrite($fp,"		});". PHP_EOL);
	fwrite($fp,"	});". PHP_EOL);

	fwrite($fp,"	/**". PHP_EOL);
	fwrite($fp,"	* Recherche ".$objectMaj. PHP_EOL);
	fwrite($fp,"	*/". PHP_EOL);
	fwrite($fp,"  	\$('.recherche".$objectMaj."').click( function(e) {". PHP_EOL);
	fwrite($fp,"		e.preventDefault();". PHP_EOL);
	fwrite($fp,"		var id".$objectMaj." = \$(this).attr(\"data-id\");". PHP_EOL);
	fwrite($fp,"		\$.ajax({". PHP_EOL);
	fwrite($fp,"			cache: false,". PHP_EOL);
	fwrite($fp,"			data: {	op : 'R', id : id".$objectMaj." },". PHP_EOL);
	fwrite($fp,"			url : \"scripts/ws".$objectMaj.".php\",". PHP_EOL);
	fwrite($fp,"			success : function( msg, status,xhr ) {". PHP_EOL);
	fwrite($fp,"				var p = msg.resultat;". PHP_EOL);
	fwrite($fp,"				if (p==true) {". PHP_EOL);
	fwrite($fp,"						\$('#id".$objectMaj."').val(msg.id);". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"						\$('#".$champ.$objectMaj."').val(msg.".$champ.");". PHP_EOL);
		}
	}
	fwrite($fp,"						\$('#my".$objectMaj."Popup').modal();". PHP_EOL);
	fwrite($fp,"					}				". PHP_EOL);
	fwrite($fp,"			},". PHP_EOL);
	fwrite($fp,"			error : function( msg, status,xhr ) {". PHP_EOL);
	fwrite($fp,"				toastr.error(msg + \"(\"+status+\")\", \"".$objectMaj."\");". PHP_EOL);
	fwrite($fp,"			}". PHP_EOL);
	fwrite($fp,"		});  		". PHP_EOL);
	fwrite($fp,"	});". PHP_EOL);

	fwrite($fp,"	/**". PHP_EOL);
	fwrite($fp,"	* Suppression ".$objectMaj. PHP_EOL);
	fwrite($fp,"	*/". PHP_EOL);
	fwrite($fp,"  	\$('.suppression".$objectMaj."').click( function(e) {". PHP_EOL);
	fwrite($fp,"		e.preventDefault();". PHP_EOL);
	fwrite($fp,"		var id".$objectMaj." = \$(this).attr(\"data-id\");". PHP_EOL);
	fwrite($fp,"		\$.ajax({". PHP_EOL);
	fwrite($fp,"			cache: false,". PHP_EOL);
	fwrite($fp,"			data: {	op : 'R', id : id".$objectMaj." },". PHP_EOL);
	fwrite($fp,"			url : \"scripts/ws".$objectMaj.".php\",". PHP_EOL);
	fwrite($fp,"			success : function( msg, status,xhr ) {". PHP_EOL);
	fwrite($fp,"				var p = msg.resultat;". PHP_EOL);
	fwrite($fp,"				if (p==true) {". PHP_EOL);
	fwrite($fp,"						\$('#id".$objectMaj."Delete').val( msg.id);". PHP_EOL);
	fwrite($fp,"						\$('#id".$objectMaj."DeleteTitre').html(\"Objet id : \" + msg.id);". PHP_EOL);
	fwrite($fp,"						\$('#my".$objectMaj."DeletePopup').modal();". PHP_EOL);
	fwrite($fp,"					}				". PHP_EOL);
	fwrite($fp,"			},". PHP_EOL);
	fwrite($fp,"			error : function( msg, status,xhr ) {". PHP_EOL);
	fwrite($fp,"				toastr.error(msg + \"(\"+status+\")\", \"".$objectMaj."\");". PHP_EOL);
	fwrite($fp,"			}". PHP_EOL);
	fwrite($fp,"		});  		". PHP_EOL);
	fwrite($fp,"	});". PHP_EOL);

	fwrite($fp,"	/**". PHP_EOL);
	fwrite($fp,"	* Ajouter ".$object. PHP_EOL);
	fwrite($fp,"	*/". PHP_EOL);
	fwrite($fp,"  	\$('#Ajouter".$objectMaj."').click( function(e) {". PHP_EOL);
	fwrite($fp,"		e.preventDefault();". PHP_EOL);
	fwrite($fp,"		\$('#".$object."TitreOperation').text(\"Nouvel objet ".$objectMaj."\");". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"		\$('#".$champ.$objectMaj."').val(\"\");". PHP_EOL);
		}
	}
	fwrite($fp,"		\$('#my".$objectMaj."Popup').modal();". PHP_EOL);
	fwrite($fp,"	});". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"	/**". PHP_EOL);
	fwrite($fp,"	 * Initialisation de l'application dès que le DOM est chargé". PHP_EOL);
	fwrite($fp,"	 */". PHP_EOL);
	fwrite($fp,"	\$(document).ready(initApplication);". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"})(jQuery);". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fclose($fp);
}

function genDatabase($object, $host, $user, $pass, $bd)
{
	$fp = fopen("gen/". $object . "/config/database.php","wb");
	fwrite($fp,"<?php". PHP_EOL);
	fwrite($fp,"class Database{". PHP_EOL);
	fwrite($fp,"     // specify your own database credentials of ".$object.PHP_EOL);
	fwrite($fp,"    private \$host;". PHP_EOL);
	fwrite($fp,"    private \$db_name;". PHP_EOL);
	fwrite($fp,"    private \$username;". PHP_EOL);
	fwrite($fp,"    private \$password;". PHP_EOL);
	fwrite($fp,"    public \$conn;". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    public function __construct(){". PHP_EOL);
	fwrite($fp,"        \$url_du_site = \$_SERVER['SERVER_NAME'];". PHP_EOL);
	fwrite($fp,"        // Connexion : Serveur, User, Password, BD". PHP_EOL);
	fwrite($fp,"        \$this->host= \"".$host."\";". PHP_EOL);
	fwrite($fp,"        \$this->username = \"".$user."\";". PHP_EOL);
	fwrite($fp,"        \$this->password= \"".$pass."\";". PHP_EOL);
	fwrite($fp,"        \$this->db_name= \"".$bd."\";". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp," ". PHP_EOL);
	fwrite($fp,"    // get the database connection". PHP_EOL);
	fwrite($fp,"    public function getConnection(){". PHP_EOL);
	fwrite($fp,"        \$this->conn = null;". PHP_EOL);
	fwrite($fp,"        try{". PHP_EOL);
	fwrite($fp,"            \$this->conn = new PDO(\"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name . \";charset=utf8\", \$this->username, \$this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));". PHP_EOL);
	fwrite($fp,"        }catch(PDOException \$exception){". PHP_EOL);
	fwrite($fp,"            echo \"Connection error : \" . \$this->host . \" : \" . \$exception->getMessage();". PHP_EOL);
	fwrite($fp,"        }". PHP_EOL);
	fwrite($fp,"        return \$this->conn;". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    public function getHost() {". PHP_EOL);
	fwrite($fp,"      return \$this->host;". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"    public function getDb_name() {". PHP_EOL);
	fwrite($fp,"      return \$this->db_name;". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"    public function getUsername() {". PHP_EOL);
	fwrite($fp,"      return \$this->username;". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"    public function getPassword() {". PHP_EOL);
	fwrite($fp,"      return \$this->password;". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"}". PHP_EOL);
	fwrite($fp,"?>". PHP_EOL);
	fclose($fp);
}

function genPageHead($object, $champs)
{
	$objectMaj = ucfirst ( $object );
	$fp = fopen("gen/". $object . "/inc/head.php","wb");
	fwrite($fp,"<!doctype html>". PHP_EOL);
	fwrite($fp,"<html lang=\"fr\">". PHP_EOL);
	fwrite($fp,"<head>". PHP_EOL);
	fwrite($fp,"    <meta charset=\"utf-8\">". PHP_EOL);
	fwrite($fp,"    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">". PHP_EOL);
	fwrite($fp,"    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />". PHP_EOL);
	fwrite($fp,"    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no\">". PHP_EOL);
	fwrite($fp,"    <meta name=\"apple-mobile-web-app-capable\" content=\"yes\" />". PHP_EOL);
	fwrite($fp,"    <meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\" />". PHP_EOL);
	fwrite($fp,"    <meta name=\"mobile-web-app-capable\" content=\"yes\">". PHP_EOL);
	fwrite($fp,"    <meta name=\"description\" content=\"xxx\">". PHP_EOL);
	fwrite($fp,"    <meta name=\"author\" content=\"Lionel C.\">". PHP_EOL);
	fwrite($fp,"    <title>".$object."</title>". PHP_EOL);
    fwrite($fp,"    <link rel=\"stylesheet\" href=\"dist/css/bootstrap.min.css\">". PHP_EOL);
    fwrite($fp,"    <link rel=\"stylesheet\" href=\"dist/css/toastr.min.css\">". PHP_EOL);
	fwrite($fp,"</head>". PHP_EOL);
	fclose($fp);
}

function genCreateTable($object, $champs)
{
	$objectMaj = ucfirst ( $object );
	$fp = fopen("gen/". $object . "/config/createTable".$object.".php","wb");
	fwrite($fp,"<?php". PHP_EOL);
	fwrite($fp,"// Creation de la table ".$objectMaj. PHP_EOL);
	fwrite($fp,"function createTable(\$bd) { ". PHP_EOL);
	fwrite($fp,"  \$query = \"SHOW TABLES LIKE '".$objectMaj."'\";". PHP_EOL);
	fwrite($fp,"  \$stmt = \$bd->prepare(\$query);". PHP_EOL);
	fwrite($fp,"  if (\$stmt->execute()) {". PHP_EOL);
	fwrite($fp,"     \$row = \$stmt->fetch(PDO::FETCH_ASSOC);". PHP_EOL);
	fwrite($fp,"     if(empty(\$row)) {". PHP_EOL);
	fwrite($fp,"        \$query = \"create table ".$objectMaj." (". PHP_EOL);
	fwrite($fp,"                  ".$object."_id int(11) AUTO_INCREMENT,". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"                  ".$object."_".$champ." varchar(255) NOT NULL,". PHP_EOL);
		}
	}
	fwrite($fp,"                  PRIMARY KEY  (".$object."_id)". PHP_EOL);
	fwrite($fp,"                  )\";". PHP_EOL);
	fwrite($fp,"        \$stmt = \$bd->prepare(\$query);". PHP_EOL);
	fwrite($fp,"        \$stmt->execute();". PHP_EOL);
	fwrite($fp,"     }". PHP_EOL);
	fwrite($fp,"  }". PHP_EOL);
	fwrite($fp,"}". PHP_EOL);
	fwrite($fp,"?>". PHP_EOL);
	fclose($fp);
}

function genScriptJson($object, $champs)
{
	$objectMaj = ucfirst ( $object );
	$fp = fopen("gen/". $object . "/scripts/ws".$objectMaj.".php","wb");
	fwrite($fp,"<?php ". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"header('Content-Type: application/json');". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"// Script REST pour voir/modifier un objet".$object. PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"// include database and object files". PHP_EOL);
	fwrite($fp,"include_once '../config/database.php';". PHP_EOL);
	fwrite($fp,"include_once '../objects/".$object.".php';". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"//if (isset(\$_SESSION['<acompleter>'])) {". PHP_EOL);
	fwrite($fp,"    \$debug=false;". PHP_EOL);
	fwrite($fp,"    if (isset(\$_GET['debug']))". PHP_EOL);
	fwrite($fp,"    {". PHP_EOL);
	fwrite($fp,"      // Mode debug". PHP_EOL);
	fwrite($fp,"      \$debug=true;". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    // instantie la base". PHP_EOL);
	fwrite($fp,"    \$database = new Database();". PHP_EOL);
	fwrite($fp,"    \$db = \$database->getConnection();". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    // Nouvel objet ".$objectMaj. PHP_EOL);
	fwrite($fp,"    \$".$object." = new ".$objectMaj."(\$db);". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    // Recherche (GET) -> op=R et id présent". PHP_EOL);
	fwrite($fp,"    if ( isset(\$_GET['op']) && \$_GET['op']=='R' && isset(\$_GET['id']) && strlen(\$_GET['id'])>0   )". PHP_EOL);
	fwrite($fp,"    {". PHP_EOL);
	fwrite($fp,"      \$".$object."->".$object."_id = \$_GET['id'];". PHP_EOL);
	fwrite($fp,"      if (\$debug==true)". PHP_EOL);
	fwrite($fp,"      {". PHP_EOL);
	fwrite($fp,"        echo \"Recherche de l'objet ".$objectMaj." {\$".$object."->".$object."_id}<br>\";". PHP_EOL);
	fwrite($fp,"      }". PHP_EOL);
	fwrite($fp,"      if (\$".$object."->charge".$objectMaj."()) {". PHP_EOL);
	fwrite($fp,"        \$json[\"resultat\"]=true;". PHP_EOL);
	fwrite($fp,"        \$json[\"id\"]=\$".$object."->".$object."_id;". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"        \$json[\"".$champ."\"]=\$".$object."->".$object."_".$champ.";". PHP_EOL);
		}
	}
	fwrite($fp,"      }". PHP_EOL);
	fwrite($fp,"      else {". PHP_EOL);
	fwrite($fp,"        \$json[\"resultat\"]=false;". PHP_EOL);
	fwrite($fp,"        \$json[\"commentaire\"]=\"".$objectMaj." introuvable\";". PHP_EOL);
	fwrite($fp,"      }". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"    // Modification (POST) -> OP=M et ID non nul". PHP_EOL);
	fwrite($fp,"    else if ( isset(\$_POST['op']) && \$_POST['op']=='M' && isset(\$_POST['id']) && strlen(\$_POST['id'])>0  )". PHP_EOL);
	fwrite($fp,"    {". PHP_EOL);
	fwrite($fp,"      \$".$object."->".$object."_id = \$_POST['id'];". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"      \$".$object."->".$object."_".$champ." = \$_POST['".$champ."'];". PHP_EOL);
		}
	}
	fwrite($fp,"      if (\$debug==true)". PHP_EOL);
	fwrite($fp,"      {". PHP_EOL);
	fwrite($fp,"        echo \"Modification de l'objet ".$objectMaj." {\$".$object."->".$object."_id}<br>\";". PHP_EOL);
	fwrite($fp,"      }". PHP_EOL);
	fwrite($fp,"      \$json[\"resultat\"]=\$".$object."->modifie".$objectMaj."();". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"    // Suppression (POST) -> OP=D et ID non nul". PHP_EOL);
	fwrite($fp,"    else if ( isset(\$_POST['op']) && \$_POST['op']=='D' && isset(\$_POST['id']) && strlen(\$_POST['id'])>0  )". PHP_EOL);
	fwrite($fp,"    {". PHP_EOL);
	fwrite($fp,"      \$".$object."->".$object."_id = \$_POST['id'];". PHP_EOL);
	fwrite($fp,"      if (\$debug==true)". PHP_EOL);
	fwrite($fp,"      {". PHP_EOL);
	fwrite($fp,"        echo \"Suppression de l'objet ".$objectMaj." {\$".$object."->".$object."_id}<br>\";". PHP_EOL);
	fwrite($fp,"      }". PHP_EOL);
	fwrite($fp,"      \$json[\"resultat\"]=\$".$object."->efface".$objectMaj."();". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"    // Ajout (POST) -> OP=M et peut importe l'id". PHP_EOL);
	fwrite($fp,"    else if ( isset(\$_POST['op']) && \$_POST['op']=='M' )". PHP_EOL);
	fwrite($fp,"    {". PHP_EOL);
	fwrite($fp,"      \$".$object."->".$object."_id = \$".$object."->".$object."_id;". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"      \$".$object."->".$object."_".$champ." = \$_POST['".$champ."'];". PHP_EOL);
		}
	}
	fwrite($fp,"      if (\$debug==true)". PHP_EOL);
	fwrite($fp,"      {". PHP_EOL);
	fwrite($fp,"        echo \"Ajout de l'objet ".$objectMaj." {\$".$object."->".$object."_id}<br>\";". PHP_EOL);
	fwrite($fp,"      }". PHP_EOL);
	fwrite($fp,"      \$json[\"resultat\"]=\$".$object."->ajoute".$objectMaj."();". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"    // Autre cas -> Erreur". PHP_EOL);
	fwrite($fp,"    else {". PHP_EOL);
	fwrite($fp,"      \$json[\"resultat\"]=false;". PHP_EOL);
	fwrite($fp,"      \$json[\"commentaire\"]=\"Parametres absents\";      ". PHP_EOL);
	fwrite($fp,"    }". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"//} ". PHP_EOL);
	fwrite($fp,"// Erreur si pas de session". PHP_EOL);
	fwrite($fp,"//else". PHP_EOL);
	fwrite($fp,"//{". PHP_EOL);
	fwrite($fp,"//  \$json[\"resultat\"]=false;". PHP_EOL);
	fwrite($fp,"//  \$json[\"commentaire\"]=\"Pas connecté !\";". PHP_EOL);
	fwrite($fp,"//}". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"echo json_encode(\$json);". PHP_EOL);
	fwrite($fp,"?>". PHP_EOL);
	fclose($fp);
}



function genPageNav($object, $champs)
{
	$objectMaj = ucfirst ( $object );
	$fp = fopen("gen/". $object . "/inc/nav.php","wb");
	fwrite($fp,"<!-- Navigation -->". PHP_EOL);
	fwrite($fp,"<nav class=\"navbar navbar-default navbar-static-top\" role=\"navigation\" style=\"margin-bottom: 0\">". PHP_EOL);
	fwrite($fp,"    <div class=\"navbar-header\">". PHP_EOL);
	fwrite($fp,"        <a href=\"index.php\">                ". PHP_EOL);
	fwrite($fp,"            <img src=\"dist/css/logo.png\" alt=\"logo\" class=\"hidden-xs\"/>". PHP_EOL);
	fwrite($fp,"        </a>". PHP_EOL);
	fwrite($fp,"        <a class=\"navbar-default\" href=\"index.php\" style=\"font-size:2em;padding:10px\">".$objectMaj."</a>". PHP_EOL);
	fwrite($fp,"    </div>". PHP_EOL);
	fwrite($fp,"    <!-- /.navbar-header -->". PHP_EOL);
	fwrite($fp,"</nav>". PHP_EOL);
	fclose($fp);
}


function genPageAccueil($object, $champs)
{
	$objectMaj = ucfirst ( $object );
	$fp = fopen("gen/". $object . "/admin_" . $object . ".php","wb");

	fwrite($fp,"<?php include(\"inc/head.php\"); ?>". PHP_EOL);
	fwrite($fp,"<body>". PHP_EOL);
	fwrite($fp,"    <div id=\"wrapper\">". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"        <?php". PHP_EOL);
	fwrite($fp,"        // include database and object files". PHP_EOL);
	fwrite($fp,"        include_once 'config/database.php';". PHP_EOL);
	fwrite($fp,"        include_once 'config/createTable".$object.".php';". PHP_EOL);
	fwrite($fp,"        include_once 'objects/".$object.".php';". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"        // instantiate database and product object". PHP_EOL);
	fwrite($fp,"        \$database = new Database();". PHP_EOL);
	fwrite($fp,"        \$db = \$database->getConnection();". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"        // Creation de la table ".$objectMaj. PHP_EOL);
	fwrite($fp,"        createTable(\$db);". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"        // Recherche de tous les objets ".$objectMaj. PHP_EOL);
	fwrite($fp,"        \$".$object." = new ".$objectMaj."(\$db);". PHP_EOL);
	fwrite($fp,"        \$stmt = \$".$object."->Lit".$objectMaj."s();". PHP_EOL);
	fwrite($fp,"        \$num = \$stmt->rowCount();". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"        include_once 'inc/nav.php';". PHP_EOL);
	fwrite($fp,"        ?>". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"        <div id=\"page-wrapper\">". PHP_EOL);
	fwrite($fp,"            <div class=\"row\">". PHP_EOL);
	fwrite($fp,"                <div class=\"col-lg-12\">". PHP_EOL);
	fwrite($fp,"                    <h1 class=\"page-header\">Administration des ".$object."s</h1>". PHP_EOL);
	fwrite($fp,"                </div>". PHP_EOL);
	fwrite($fp,"                <!-- /.col-lg-12 -->". PHP_EOL);
	fwrite($fp,"            </div>". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"            <!-- /.row -->". PHP_EOL);
	fwrite($fp,"            <div class=\"row\">". PHP_EOL);
	fwrite($fp,"                <div class=\"col-lg-12\">". PHP_EOL);
	fwrite($fp,"                    <button type=\"button\" class=\"btn btn-primary\" id=\"Ajouter".$objectMaj."\">". PHP_EOL);
	fwrite($fp,"                        Ajouter ".$objectMaj. PHP_EOL);
	fwrite($fp,"                    </button>". PHP_EOL);
	fwrite($fp,"                    <br><br>". PHP_EOL);
	fwrite($fp,"                </div>". PHP_EOL);
	fwrite($fp,"                <div class=\"col-lg-12\">". PHP_EOL);
	fwrite($fp,"                    <div class=\"panel panel-default\">". PHP_EOL);
	fwrite($fp,"                        <!-- /.panel-heading -->". PHP_EOL);
	fwrite($fp,"                        <div class=\"panel-body\">". PHP_EOL);
	fwrite($fp,"                            <div class=\"dataTable_wrapper\">". PHP_EOL);
	fwrite($fp,"                                <table class=\"table table-striped table-bordered table-hover\" id=\"dataTables-".$object."\">". PHP_EOL);
	fwrite($fp,"                                    <thead class=\"bg-primary\">". PHP_EOL);
	fwrite($fp,"                                        <tr>". PHP_EOL);
	fwrite($fp,"                                            <th>Id</th>". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"                                            <th>".$champ."</th>". PHP_EOL);
		}
	}
	fwrite($fp,"                                            <th class='text-center' >MAJ</th>". PHP_EOL);
	fwrite($fp,"                                        </tr>". PHP_EOL);
	fwrite($fp,"                                    </thead>". PHP_EOL);
	fwrite($fp,"                                    <tbody>". PHP_EOL);
	fwrite($fp,"                                        <?php". PHP_EOL);
	fwrite($fp,"                                        \$total=0;". PHP_EOL);
	fwrite($fp,"                                        if(\$num>0) {". PHP_EOL);
	fwrite($fp,"                                            while (\$row = \$stmt->fetch(PDO::FETCH_ASSOC)) {". PHP_EOL);
	fwrite($fp,"                                                extract(\$row);". PHP_EOL);
	fwrite($fp,"                                                echo \"<tr>\";". PHP_EOL);
	fwrite($fp,"                                                    echo \"<td>{\$".$object."_id}</td>\";". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"                                                    echo \"<td>{\$".$object."_".$champ."}</td>\";". PHP_EOL);
		}
	}
	fwrite($fp,"                                                    echo \"<td class='text-center' >\";". PHP_EOL);
	fwrite($fp,"                                                    echo \"<a href='#' data-id={\$".$object."_id}  class='recherche".$objectMaj." btn btn-primary btn-xs' title='Modification'>\";". PHP_EOL);
	fwrite($fp,"                                                    echo \"<span class='glyphicon glyphicon-ok' aria-hidden='true'></span></a>&nbsp;\";". PHP_EOL);
	fwrite($fp,"                                                    echo \"<a href='#' data-id={\$".$object."_id}  class='suppression".$objectMaj." btn btn-primary btn-xs' title='Suppression'>\";". PHP_EOL);
	fwrite($fp,"                                                    echo \"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></td>\";". PHP_EOL);
	fwrite($fp,"                                                echo \"</tr>\";". PHP_EOL);
	fwrite($fp,"                                            }    ". PHP_EOL);
	fwrite($fp,"                                        }". PHP_EOL);
	fwrite($fp,"                                        ?>". PHP_EOL);
	fwrite($fp,"                                    </tbody>". PHP_EOL);
	fwrite($fp,"                                </table>". PHP_EOL);
	fwrite($fp,"                            </div>". PHP_EOL);
	fwrite($fp,"                        </div>". PHP_EOL);
	fwrite($fp,"                        <!-- /.panel-body -->". PHP_EOL);
	fwrite($fp,"                    </div>". PHP_EOL);
	fwrite($fp,"                    <!-- /.panel -->". PHP_EOL);
	fwrite($fp,"                </div>". PHP_EOL);
	fwrite($fp,"                <!-- /.col-lg-12 -->". PHP_EOL);
	fwrite($fp,"            </div>". PHP_EOL);
	fwrite($fp,"        </div>". PHP_EOL);
	fwrite($fp,"        <!-- /#page-wrapper -->". PHP_EOL);
	fwrite($fp,"    </div>". PHP_EOL);
	fwrite($fp,"    <!-- /#wrapper -->". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
    fwrite($fp,"    <script src=\"dist/js/jquery.min.js\"></script>". PHP_EOL);
    fwrite($fp,"    <script src=\"dist/js/bootstrap.min.js\"></script>". PHP_EOL);
    fwrite($fp,"    <script src=\"dist/js/toastr.min.js\"></script>". PHP_EOL);
    fwrite($fp,"    <script src=\"dist/js/".$object.".js\"></script>". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"    <!-- Modal - MAJ d'un objet -->". PHP_EOL);
	fwrite($fp,"   <div class=\"modal fade\" id=\"my".$objectMaj."Popup\" tabindex=\"-1\">". PHP_EOL);
	fwrite($fp,"      <div class=\"modal-dialog\" role=\"document\">". PHP_EOL);
	fwrite($fp,"        <div class=\"modal-content\">". PHP_EOL);
	fwrite($fp,"          <div class=\"modal-header\">". PHP_EOL);
	fwrite($fp,"            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>". PHP_EOL);
	fwrite($fp,"            <h4 class=\"modal-title\" id=\"".$object."TitreOperation\">Modification ".$objectMaj."</h4>". PHP_EOL);
	fwrite($fp,"          </div>". PHP_EOL);
	fwrite($fp,"            <form>". PHP_EOL);
	fwrite($fp,"            <div class=\"modal-body\">". PHP_EOL);
	fwrite($fp,"                <input type=\"hidden\" class=\"form-control\" id=\"id".$objectMaj."\">". PHP_EOL);
	foreach ($champs as $champ) {
		if (!empty($champ)) {
			fwrite($fp,"                <div class=\"form-group\">". PHP_EOL);
			fwrite($fp,"                    <label for=\"message-text\" class=\"control-label\">".$champ."</label>". PHP_EOL);
			fwrite($fp,"                    <input type=\"text\" class=\"form-control\" id=\"".$champ.$objectMaj."\">". PHP_EOL);
			fwrite($fp,"                </div>". PHP_EOL);
		}
	}

	fwrite($fp,"            </div>". PHP_EOL);
	fwrite($fp,"            <div class=\"modal-footer\">". PHP_EOL);
	fwrite($fp,"             	<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Annuler</button>". PHP_EOL);
	fwrite($fp,"                <button type=\"button\" class=\"btn btn-primary\" id=\"sauver".$objectMaj."\">Sauver</button>". PHP_EOL);
	fwrite($fp,"            </div>". PHP_EOL);
	fwrite($fp,"        </form>". PHP_EOL);
	fwrite($fp,"        </div>". PHP_EOL);
	fwrite($fp,"      </div>". PHP_EOL);
	fwrite($fp,"    </div>". PHP_EOL);
	fwrite($fp,"". PHP_EOL);
	fwrite($fp,"	<div class=\"modal fade\" id=\"my".$objectMaj."DeletePopup\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">". PHP_EOL);
	fwrite($fp,"      <div class=\"modal-dialog\" role=\"document\">". PHP_EOL);
	fwrite($fp,"        <div class=\"modal-content\">". PHP_EOL);
	fwrite($fp,"          <div class=\"modal-header\">". PHP_EOL);
	fwrite($fp,"            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>". PHP_EOL);
	fwrite($fp,"            <h4 class=\"modal-title\" id=\"".$object."TitreOperationDelete\">Suppression ".$objectMaj."</h4>". PHP_EOL);
	fwrite($fp,"          </div>". PHP_EOL);
	fwrite($fp,"          <div class=\"modal-body\">". PHP_EOL);
	fwrite($fp,"              <input type=\"hidden\" class=\"form-control\" id=\"id".$objectMaj."Delete\">". PHP_EOL);
	fwrite($fp,"              <p id=\"id".$objectMaj."DeleteTitre\"></p>". PHP_EOL);
	fwrite($fp,"          </div>". PHP_EOL);
	fwrite($fp,"          <div class=\"modal-footer\">". PHP_EOL);
	fwrite($fp,"           	<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Annuler</button>". PHP_EOL);
	fwrite($fp,"            <button type=\"button\" class=\"btn btn-primary\" id=\"effacer".$objectMaj."\">Sauver</button>". PHP_EOL);
	fwrite($fp,"            </div>". PHP_EOL);
	fwrite($fp,"       </div>". PHP_EOL);
	fwrite($fp,"     </div>". PHP_EOL);
	fwrite($fp,"   </div>". PHP_EOL);
	fwrite($fp,"</body>". PHP_EOL);
	fwrite($fp,"</html>". PHP_EOL);
	fclose($fp);
}


function recurse_copy ($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
} 


function create_directory($rep) {
	 if (!file_exists($rep)) {
	    mkdir($rep, 0777, true);
	 }
}
                


?>