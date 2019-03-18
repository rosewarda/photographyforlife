
<?php

error_reporting(E_ALL);
ini_set("display_errors", 1); //Affichage des erreurs

//Eviter les insertions de scripts dans le cas d'un e-mail HTML
$nom = htmlentities($_POST['nom']);
$email_from = htmlentities($_POST['email']);
$message = htmlentities($_POST['message']);

//Verifie si le fournisseur prend en charge les r
if(preg_match("#@(hotmail|live|msn).[a-z]{2,4}$#", $email_from)){
    $passage_ligne = "\n";
}else{
    $passage_ligne = "\r\n";
}

$email_to = "warda.derbli@gmail.com"; //Destinataire
$email_subject = "Contact pour rose"; //Sujet du mail
$boundary = md5(rand()); // clé aléatoire de limite

function clean_string($string) {
    $bad = array("content-type","bcc:","to:","cc:","href");
    return str_replace($bad,"",$string);
}

$headers = "From: "".$nom.""<".$email_from.">" . $passage_ligne; //Emetteur
$headers.= "Reply-to: "".$nom."" <".$email_from.">" . $passage_ligne; //Emetteur
$headers.= "MIME-Version: 1.0" . $passage_ligne; //Version de MIME
$headers.= 'Content-Type: multipart/mixed; boundary='.$boundary .' '. $passage_ligne; //Contenu du message (alternative pour deux versions ex:text/plain et text/html



$email_message = '--' . $boundary . $passage_ligne; //Séparateur d'ouverture
$email_message .= "Content-Type: text/plain; charset="utf-8"" . $passage_ligne; //Type du contenu
$email_message .= "Content-Transfer-Encoding: 8bit" . $passage_ligne; //Encodage
$email_message .= $passage_ligne .clean_string($message). $passage_ligne; //Contenu du message



//Pièce jointe
    if(isset($_FILES["fichier"]) &&  $_FILES['fichier']['name'] != ""){ //Vérifie sur formulaire envoyé et que le fichier existe
        $nom_fichier = $_FILES['fichier']['name'];
        $source = $_FILES['fichier']['tmp_name'];
        $type_fichier = $_FILES['fichier']['type'];
        $taille_fichier = $_FILES['fichier']['size'];

        if($nom_fichier != ".htaccess"){ //Vérifie que ce n'est pas un .htaccess
			 if($type_fichier == "image/jpeg"
                || $type_fichier == "image/pjpeg"
                || $type_fichier == "application/pdf"){ //Soit un jpeg soit un pdf


                if ($taille_fichier <= 2097152) { //Taille supérieure à Mo (en octets)
                    $tabRemplacement = array("é"=>"e", "è"=>"e", "à"=>"a"); //Remplacement des caractères spéciaux

                    $handle = fopen($source, 'r'); //Ouverture du fichier
                    $content = fread($handle, $taille_fichier); //Lecture du fichier
                    $encoded_content = chunk_split(base64_encode($content)); //Encodage
                    $f = fclose($handle); //Fermeture du fichier

                    $email_message .= $passage_ligne . "--" . $boundary . $passage_ligne; //Deuxième séparateur d'ouverture
                    $email_message .= 'Content-type:'.$type_fichier.';name="'.$nom_fichier.'"'."n"; //Type de contenu (application/pdf ou image/jpeg)
                    $email_message .='Content-Disposition: attachment; filename="'.$nom_fichier.'"'."n"; //Précision de pièce jointe
                    $email_message .= 'Content-transfer-encoding:base64'."n"; //Encodage
                    $email_message .= "n"; //Ligne blanche. IMPORTANT !
                    $email_message .= $encoded_content."n"; //Pièce jointe

                }else{
					//Message d'erreur
                    $email_message .= $passage_ligne ."L'utilisateur a tenté de vous envoyer une pièce jointe mais celle ci était superieure à 2Mo.". $passage_ligne;
                }
            }else{
				//Message d'erreur
                $email_message .= $passage_ligne ."L'utilisateur a tenté de vous envoyer une pièce jointe mais elle n'était pas au bon format.". $passage_ligne;
            }
        }else{
			//Message d'erreur
            $email_message .= $passage_ligne ."L'utilisateur a tenté de vous envoyer une pièce jointe .htaccess.". $passage_ligne;
        }
    }
$email_message .= $passage_ligne . "--" . $boundary . "--" . $passage_ligne; //Séparateur de fermeture


if(mail($email_to,$email_subject, $email_message, $headers)==true){  //Envoi du mail
    header('Location: index.html'); //Redirection
}




?>
