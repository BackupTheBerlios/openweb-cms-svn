<!--
// script JAVASCRIPT permettant de gerer un deversoir (deux SELECT et deux boutons AJOUTER, ENLEVER)
// le formulaire doit s'appeler frmSaisie


//-------------- deverse l'option selectionee du select1 vers le select2
function dvrMove( select1, select2)
{
var sel
var optsel
var newOpt
// on recupere l'index de l'option selectionnee
sel=select1.selectedIndex;

if (sel > -1 )
	{
	// on recupere l'option selectionnee
	optsel=select1.options[sel];

	// on creer une nouvelle option
	newOpt = new Option( optsel.text, optsel.value);

	// on ajoute cette option  a l'autre liste
	maxi=select2.length;
	select2.options[maxi]=newOpt;

	// effacement de l'option
	select1.options[sel] = null;
	}
}

//-------------- deverse Toutes les options du select1 vers le select2
function dvrMoveAll( select1, select2)
{

var optsel;
var newOpt;
var nbopt;
var i;
// on recupere l'index de l'option selectionnee
nbopt=select1.length;

for(i=0; i < nbopt;i++)
	{
	// on recupere l'option en cours
	optsel=select1.options[i];

	// on creer une nouvelle option
	newOpt = new Option( optsel.text, optsel.value);

	// on ajoute cette option  a l'autre liste
	select2.options[select2.length]=newOpt;
	}
	
// effacement des options
for(i=0; i < nbopt; i++)
	{
	select1.options[0] = null;
	}
}


//-------------- copy l'option selectionee du select1 dans le select2 en verifiant ou pas (bool verif)
//				si l'option n'est pas deja dans select2
function dvrCopy( select1, select2, verif)
{
var sel
var optsel
var newOpt
var bCopy
// on recupere l'index de l'option selectionnee
sel=select1.selectedIndex;
bCopy=true;

if (sel > -1 )
	{
	// on recupere l'option selectionnee
	optsel=select1.options[sel];

	// verification que l'option n'existe pas deja
	if(verif)
		{
		var nbopt;
		var opt;
		var i;
		
		nbopt=select2.length;
		
		for(i=0; i < nbopt;i++)
			{
			opt=select2.options[i];
			if(opt.value==optsel.value)
				bCopy=false;
			}
		}
	
	if(bCopy)
		{
		// on creer une nouvelle option
		var str;
		str=optsel.text;
		// str=str.replace(/ /,".");
		
		newOpt = new Option( str, optsel.value);

		// on ajoute cette option  a l'autre liste
		maxi=select2.length;
		select2.options[maxi]=newOpt;
		}
	}
}

//-------------- enleve l'option selectionee du select1
function dvrRemove( select1)
{
var sel;

// on recupere l'index de l'option selectionnee
sel=select1.selectedIndex;
if (sel > -1 )
	{
	// effacement de l'option
	select1.options[sel] = null;
	}
}



//----------------  monte d'un cran l'element selectionne dans la liste
function dvrUp(select1)
{
var sel
var optsel
var optold
var newOpt
var newOptOld

// on recupere l'index de l'option selectionnee
sel=select1.selectedIndex;

if (sel > 0 )
	{
	// on recupere l'option selectionnee
	optsel=select1.options[sel];
	
	// on recupere l'option du dessus
	optold=select1.options[sel-1];
	
	// creation nouvelle option
	
	newOpt = new Option( optsel.text, optsel.value,false,true);
	newOptOld = new Option( optold.text, optold.value);
	
	select1.options[sel]=newOptOld
	select1.options[sel-1]=newOpt
	}
}

//----------------  descend d'un cran l'element selectionne dans la liste
function dvrDwn(select1)
{
var sel
var optsel
var optold
var newOpt
var newOptOld

// on recupere l'index de l'option selectionnee
sel=select1.selectedIndex;
if (sel > -1  && sel < select1.length -1 )
	{
	// on recupere l'option selectionnee
	optsel=select1.options[sel];
	
	// on recupere l'option du dessous
	optold=select1.options[sel+1];
	
	// creation nouvelle option
	
	newOpt = new Option( optsel.text, optsel.value,false,true);
	newOptOld = new Option( optold.text, optold.value);
	
	// ajout nouvelle option
	select1.options[sel]=newOptOld
	select1.options[sel+1]=newOpt
	}
}

//---- selectionne toutes les options d'un select.
//---- a utiliser lors du submit du formulaire, pour transmettre tout ce qu'a selectionne l'utilisateur

function AllSelect(sel)
{
var i
if (sel.length > 0)
	{
	for(i=0; i < sel.length; i++)
		{
		sel.options[i].selected=true;
		}
	}
}

//-->
