/**
* Fonction de récupération des données correspondant au critère de recherche
* @param   {String} condition - Chaine indiquant la condition à remplir
* @param   {Array}  table - Tableau contenant les données à extraire
* @returns {Array}  result - Tableau contenant les données extraites
*/
function getDataFromTable( condition, table) {
  // récupération de la clé et de la valeur
  var cde = condition.replace(/\s/g, '').split('='),
      key = cde[0],
      value = cde[1],
      result = [];
  
  // retour direct si *
  if (condition === '*') {
    return table.slice();
  }
  // retourne les éléments répondant à la condition
  result = table.filter( function(obj){
       return obj[key] === value;
    });
  return result;
}
/**
* Affichage du nombre d'<option> présentes dans le <select>
* @param {Object} obj - <select> parent
* @param {Number} nb - nombre à afficher
*/
function setNombre( obj, nb){
  var oElem = obj.parentNode.querySelector('.nombre');
  if( oElem){
    oElem.innerHTML = nb ? '(' +nb +')' :'';
  }
}
/**
* Fonction d'ajout des <option> à un <select>
* @param   {String} id_select - ID du <select> à mettre à jour
* @param   {Array}  liste - Tableau contenant les données à ajouter
* @param   {String} valeur - Champ pris en compte pour la value de l'<option>
* @param   {String} texte - Champ pris en compte pour le texte affiché de l'<option>
* @returns {String} Valeur sélectionnée du <select> pour chainage
*/
function updateSelect( id_select, liste, valeur, texte){
  var oOption,
      oSelect = document.getElementById( id_select),
      i, nb = liste.length;
  // vide le select
  oSelect.options.length = 0;
  // désactive si aucune option disponible
  oSelect.disabled = nb ? false : true;
  // affiche info nombre options, facultatif
  setNombre( oSelect, nb);
  // ajoute 1st option
  if( nb){
    oSelect.add( new Option( 'Tous', '0'));
    // focus sur le select
    oSelect.focus();
  }
  // création des options d'après la liste
  for (i = 0; i < nb; i += 1) {
    // création option
    oOption = new Option( liste[i][texte], liste[i][valeur]);
    // ajout de l'option en fin
    oSelect.add( oOption);
  }
  // si une seule option on la sélectionne
  oSelect.selectedIndex = nb === 1 ? 1 : 0;
  // on retourne la valeur pour le select suivant
  return oSelect.value;
}
/**
* fonction de chainage des <select>
* @param {String|Object} ID du <select> à traiter ou le <select> lui-même
*/
function chainSelect( param){
  // affectation par défaut
  param = param || 'init';
  var liste,
      id     = param.id || param,
      valeur = param.value || '';
      
  // test à faire pour récupération de la value
  if( typeof id === 'string'){
     param = document.getElementById( id);
     valeur = param ? param.value : '';
   }

  switch (id){
    case 'init':
      // récup. des données
      liste = getDataFromTable( '*', tbl_cat_parent);
      // mise à jour du select
      valeur = updateSelect( 'cat_parent', liste, 'id', 'name');
      // chainage sur le select lié
      chainSelect('cat_parent');
      break;
    case 'cat_parent':
      // récup. des données
      liste = getDataFromTable( 'parent=' +valeur, tbl_category);
      // mise à jour du select
      valeur = updateSelect( 'category', liste, 'id', 'name');
      // chainage sur le select lié
      chainSelect('category');
      break;
  }
}
// Initialisation après chargement du DOM
document.addEventListener("DOMContentLoaded", function(event) {
  var oSelects = document.querySelectorAll('#liste select'),
      i, nb = oSelects.length;
  // affectation de la fonction sur le onchange
  for( i = 0; i < nb; i += 1) {
    oSelects[i].onchange = function() {
        chainSelect(this);
      };
  }
  // init du 1st select
  if( nb){
    chainSelect('init');
  }
});