//Set les valeurs présentent dans chaque boutons, pour qu'il interagissent bien avec leur widget
for (var i = 0; i < document.getElementsByClassName('allClbus').length; i++) {
    for (var u = 0; u < document.getElementsByClassName('allClbus')[i].getElementsByClassName('blockClub').length; u++) {
        for (var k = 0; k < document.getElementsByClassName('allClbus')[i].getElementsByClassName('blockClub')[u].getElementsByTagName('button').length; k++) {
            document.getElementsByClassName('allClbus')[i].getElementsByClassName('blockClub')[u].getElementsByTagName('button')[k].setAttribute('onclick','eventusNextClub('+i+')');
        }
    }
    eventusResizeClub(i);
}


//Resize le widget apres update de la windo
window.addEventListener("resize", eventusResizeClubTemp);
function eventusResizeClubTemp() {
    //Resize une premiere fois pour tous les widget
    for (var i = 0; i < document.getElementsByClassName('allClbus').length; i++) {
        eventusResizeClub(i);
    }    
    //Puis une seconde fois apres que le theme est update certains elements
    setTimeout( function(){ 
        for (var i = 0; i < document.getElementsByClassName('allClbus').length; i++) {
            eventusResizeClub(i); 
        }
    }
    , 1000);
}

//Passe au club suivant
function eventusNextClub(numWidget) {   
    var monWidget = document.getElementsByClassName('allClbus')[numWidget];
    var totalClub = monWidget.getElementsByClassName('blockClub').length;
    eventusResizeClub(numWidget);
    //Desactive tout les boutons suivant
    for (var i = 0; i < monWidget.getElementsByClassName('clubSuivant').length; i++) {
        monWidget.getElementsByClassName('clubSuivant')[i].disabled = true;
    } 

    //Déplace les club de droite a gauche
	for (var i = 0; i < totalClub; i++) { 
        var pos = monWidget.getElementsByClassName('blockClub')[i].style.right;
        pos = pos.substring(0, pos.length - 1);
        monWidget.getElementsByClassName('blockClub')[i].style.right = parseInt(pos)+100+'%';
        //Fait disparaitre le club passé
        if (monWidget.getElementsByClassName('blockClub')[i].style.right=="100%") {            
            monWidget.getElementsByClassName('blockClub')[i].style.opacity = '0';  
        } 
    }  

    //Replace le club a gauche, sur la droite
    setTimeout(function() {
        for (var i = 0; i < totalClub; i++) {
            if (monWidget.getElementsByClassName('blockClub')[i].style.right=="100%") {
                monWidget.getElementsByClassName('blockClub')[i].style.transition="none";
                monWidget.getElementsByClassName('blockClub')[i].style.right = '-'+(totalClub-1)*100+'%';  
            }
            //Fait réapparaite les clubs (mais invisible car en dehors de la div)
            monWidget.getElementsByClassName('blockClub')[i].style.opacity = '1';
        }
        //Réactive les boutons suivants
        for (var i = 0; i < monWidget.getElementsByClassName('clubSuivant').length; i++) {
            monWidget.getElementsByClassName('clubSuivant')[i].disabled = false;
        } 
    }, 500);
}

function eventusResizeClub(numWidget){
    var monWidget = document.getElementsByClassName('allClbus')[numWidget];
    var totalClub = monWidget.getElementsByClassName('blockClub').length;

    //Solution permmettant de récuperer certaines valeurs en 'em' du theme : titre widget, son margin et la padding des widgets
    if (monWidget.getElementsByClassName('widget-title')[0]) {
        var heightTitle = 42+22+monWidget.getElementsByClassName('widget-title')[0].offsetHeight;
    } else {
        var heightTitle = 42;
    }

    //Recherche le club afficher et le suivant
    var block1 = 0;
    var block2 = 0;
    for (var i = 0; i < totalClub; i++) {
        monWidget.getElementsByClassName('blockClub')[i].style.transition="";         
        if (monWidget.getElementsByClassName('blockClub')[i].style.right=="0%") {  
            block1 = i;       
        } 
        if (monWidget.getElementsByClassName('blockClub')[i].style.right=="-100%") {  
            block2 = i;       
        }                   
    }
    //Change la taille du widget en fonction du club afficher et son suivant, et garde la taille du plus grand
    if (monWidget.getElementsByClassName('blockClub')[block1].offsetHeight > monWidget.getElementsByClassName('blockClub')[block2].offsetHeight) {
        monWidget.style.height = monWidget.getElementsByClassName('blockClub')[block1].offsetHeight+heightTitle+"px"; 
    } else {
        monWidget.style.height = monWidget.getElementsByClassName('blockClub')[block2].offsetHeight+heightTitle+"px"; 
    }

    //Changer la taille du widget via celle du club afficher
    setTimeout(function() {
        for (var i = 0; i < totalClub; i++) {
            monWidget.getElementsByClassName('blockClub')[i].style.transition=""; 
            if (monWidget.getElementsByClassName('blockClub')[i].style.right=="0%") {  
                monWidget.style.height = monWidget.getElementsByClassName('blockClub')[i].offsetHeight+heightTitle+"px";              
            }            
        }
    }, 500); 
}