<?php

// -----------------------------------------------
// Cryptographp v1.4
// (c) 2006-2007 Sylvain BRISON 
//
// www.cryptographp.com 
// cryptographp@alphpa.com 
//
// Licence CeCILL modifi馥
// => Voir fichier Licence_CeCILL_V2-fr.txt)
// -----------------------------------------------


// -------------------------------------
// Configuration du fond du cryptogramme
// -------------------------------------

$cryptwidth  = 350; // Largeur du cryptogramme (en pixels)
$cryptheight = 50;  // Hauteur du cryptogramme (en pixels)

$bgR  = 255;        // Couleur du fond au format RGB: Red (0->255)
$bgG  = 255;        // Couleur du fond au format RGB: Green (0->255)
$bgB  = 255;        // Couleur du fond au format RGB: Blue (0->255)

$bgclear = false;    // Fond transparent (true/false)
                     // Uniquement valable pour le format PNG

$bgimg = '';                // Le fond du cryptogramme peut-黎re une image  
                             // PNG, GIF ou JPG. Indiquer le fichier image
                             // Exemple: $fondimage = 'photo.gif';
				                     // L'image sera redimensionn馥 si n馗essaire
                             // pour tenir dans le cryptogramme.
                             // Si vous indiquez un r駱ertoire plut qu'un 
                             // fichier l'image sera prise au hasard parmi 
                             // celles disponibles dans le r駱ertoire

$bgframe = false;   // Ajoute un cadre de l'image (true/false)


// ----------------------------
// Configuration des caract鑽es
// ----------------------------

// Couleur de base des caract鑽es

$charR = 0;    // Couleur des caract鑽es au format RGB: Red (0->255)
$charG = 0;    // Couleur des caract鑽es au format RGB: Green (0->255)
$charB = 0;    // Couleur des caract鑽es au format RGB: Blue (0->255)

$charcolorrnd = true;     // Choix al饌toire de la couleur.
$charcolorrndlevel = 2;   // Niveau de clart・des caract鑽es si choix al饌toire (0->4)
                           // 0: Aucune s駘ection
                           // 1: Couleurs tr鑚 sombres (surtout pour les fonds clairs)
                           // 2: Couleurs sombres
                           // 3: Couleurs claires
                           // 4: Couleurs tr鑚 claires (surtout pour fonds sombres)

$charclear = 0;  // Intensit・de la transparence des caract鑽es (0->127)
                  // 0=opaques;127=invisibles
	                // interessant si vous utilisez une image $bgimg
	                // Uniquement si PHP >=3.2.1

// Polices de caract鑽es

//$tfont[] = 'Alanden_.ttf';      // Les polices seront al饌toirement utilis馥s.
//$tfont[] = 'bsurp___.ttf';      // Vous devez copier les fichiers correspondants
//$tfont[] = 'ELECHA__.TTF';      // sur le serveur.
$tfont[] = 'luggerbu.ttf';        // Ajoutez autant de lignes que vous voulez   
//$tfont[] = 'RASCAL__.TTF';      // Respectez la casse ! 
//$tfont[] = 'SCRAWL.TTF'; 
//$tfont[] = 'WAVY.TTF';  
//$tfont[] = 'verdana.ttf';

// Caracteres autoris駸
// Attention, certaines polices ne distinguent pas (ou difficilement) les majuscules 
// et les minuscules. Certains caract鑽es sont faciles ・confondre, il est donc
// conseill・de bien choisir les caract鑽es utilis駸.

$charel = '012345689';      // Caract鑽es autoris駸

$crypteasy = false;      // Cr饌tion de cryptogrammes "faciles ・lire" (true/false)
                         // compos駸 alternativement de consonnes et de voyelles.

$charelc = 'BCDFGKLMPRTVWXZ';  // Consonnes utilis馥s si $crypteasy = true
$charelv = 'AEIOUY';             // Voyelles utilis馥s si $crypteasy = true

$difuplow = false;         // Diff駻encie les Maj/Min lors de la saisie du code (true, false)

$charnbmin = 6;        // Nb minimum de caracteres dans le cryptogramme
$charnbmax = 6;        // Nb maximum de caracteres dans le cryptogramme

$charspace = 23;       // Espace entre les caracteres (en pixels)
$charsizemin = 15;     // Taille minimum des caract鑽es
$charsizemax = 22;     // Taille maximum des caract鑽es

$charanglemax  = 0;    // Angle maximum de rotation des caracteres (0-360)
$charup   = false;       // D駱lacement vertical al饌toire des caract鑽es (true/false)

// Effets suppl駑entaires

$cryptgaussianblur = false;// Transforme l'image finale en brouillant: m騁hode Gauss (true/false)
                            // uniquement si PHP >= 5.0.0
$cryptgrayscal = false;    // Transforme l'image finale en d馮rad・de gris (true/false)
                            // uniquement si PHP >= 5.0.0

// ----------------------
// Configuration du bruit
// ----------------------

$noisepxmin = 0;     // Bruit: Nb minimum de pixels al饌toires
$noisepxmax = 0;     // Bruit: Nb maximum de pixels al饌toires

$noiselinemin = 0;    // Bruit: Nb minimum de lignes al饌toires
$noiselinemax = 0;    // Bruit: Nb maximum de lignes al饌toires

$nbcirclemin = 0;     // Bruit: Nb minimum de cercles al饌toires 
$nbcirclemax = 0;     // Bruit: Nb maximim de cercles al饌toires

$noisecolorchar  = 2; // Bruit: Couleur d'ecriture des pixels, lignes, cercles: 
                       // 1: Couleur d'馗riture des caract鑽es
                       // 2: Couleur du fond
                       // 3: Couleur al饌toire
                       
$brushsize = 1;       // Taille d'ecriture du princeaiu (en pixels) 
                       // de 1 ・25 (les valeurs plus importantes peuvent provoquer un 
                       // Internal Server Error sur certaines versions de PHP/GD)
                       // Ne fonctionne pas sur les anciennes configurations PHP/GD

$noiseup = false;     // Le bruit est-il par dessus l'ecriture (true) ou en dessous (false) 

// --------------------------------
// Configuration syst鑪e & s馗urit・
// --------------------------------

$cryptformat = "jpg";  // Format du fichier image g駭駻・"GIF", "PNG" ou "JPG"
				                // Si vous souhaitez un fond transparent, utilisez "PNG" (et non "GIF")
				                // Attention certaines versions de la bibliotheque GD ne gerent pas GIF !!!

$cryptsecure = "md5";   // M騁hode de crytpage utilis馥: "md5", "sha1" ou "" (aucune)
                         // "sha1" seulement si PHP>=4.2.0
                         // Si aucune m騁hode n'est indiqu馥, le code du cyptogramme est stock・
                         // en clair dans la session.
                       
$cryptusetimer = 0;       // Temps (en seconde) avant d'avoir le droit de reg駭駻er un cryptogramme

$cryptusertimererror = 3; // Action ・r饌liser si le temps minimum n'est pas respect・
                           // 1: Ne rien faire, ne pas renvoyer d'image.
                           // 2: L'image renvoy馥 est "images/erreur2.png" (vous pouvez la modifier)
                           // 3: Le script se met en pause le temps correspondant (attention au timeout
                           //    par d馭aut qui coupe les scripts PHP au bout de 30 secondes)
                           //    voir la variable "max_execution_time" de votre configuration PHP

$cryptusemax = 1000; // Nb maximum de fois que l'utilisateur peut g駭駻er le cryptogramme
                      // Si d駱assement, l'image renvoy馥 est "images/erreur1.png"
                      // PS: Par d馭aut, la dur馥 d'une session PHP est de 180 mn, sauf si 
                      // l'hebergeur ou le d騅eloppeur du site en ont d馗id・autrement... 
                      // Cette limite est effective pour toute la dur馥 de la session. 
                      
$cryptoneuse = false; // Si vous souhaitez que la page de verification ne valide qu'une seule 
                       // fois la saisie en cas de rechargement de la page indiquer "true".
                       // Sinon, le rechargement de la page confirmera toujours la saisie.                          
                      
?>
