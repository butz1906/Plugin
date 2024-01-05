//Gestion des boutons shorcodes liens
// Sélectionnez tous les boutons avec la classe "button-shortcode"
var buttons = document.querySelectorAll('.button-shortcode');

// Bouclez sur chaque bouton pour ajouter un événement "click"
buttons.forEach(function(button) {
  button.addEventListener('click', function() {

    // Créez un nouvel input
    var input = document.createElement('input');
    input.type = 'text';
    input.className ='favicon';
    input.id = this.textContent;
    input.required = 'true';
    input.maxLength = 100;

    var label = document.createElement('label');
    label.htmlfor = input.id
    label.className ="favlabel";
    label.textContent = input.id

    // Créer un élément "span" pour le symbole "+"
    var span = document.createElement('span');
    span.className = 'ajouter';
    span.textContent = '+';

    // Créer un élément "img" pour le favicon
    var favicon = document.createElement('img');
    favicon.className = 'favicon-icon';
    favicon.style.display = 'none';

    // Ajouter l'événement "input" à l'input
    input.addEventListener('input', function() {
      var url = input.value.trim();

      if (url !== '') {
        // Récupérer le favicon de l'URL saisie
        var faviconUrl = 'https://www.google.com/s2/favicons?domain=' + url;
        favicon.src = faviconUrl;

        // Afficher le favicon dès qu'il est téléchargé
        favicon.addEventListener('load', function() {
          favicon.style.display = 'inline-block';
        });
      } else {
        favicon.style.display = 'none';
      }
    });

    // Créer un conteneur pour l'input, le favicon et le symbole "+"
    var container = document.createElement('div');
    container.className = 'input-container';
    container.appendChild(favicon);
    container.appendChild(input);
    container.appendChild(label);
    container.appendChild(span);

    input.addEventListener('keydown', function(event) {
      // Vérifier si la touche appuyée est "Entrée"
      if (event.keyCode === 13) {
        // Déclencher la fonction du span
        span.click();
      }
    });
// Ajouter l'événement "click" à la span
span.addEventListener('click', function() {
  input.insertAdjacentHTML('afterend', '<div class="fill"></div>');
  var fill = document.querySelector('.fill');
  
  setTimeout(function() {
    fill.style.width = '93%';
  });
  setTimeout(function() {
    span.textContent = 'MERCI !';
    span.style.width = 'auto';
  span.classList.add('rotated');
  },200);
  setTimeout(function() {
    span.style.animation = 'explode-hide 0.2s 1';
  },500);
  setTimeout(function() {
    span.style.animation = 'explode-show .5s 1';
    span.textContent = '+';

  }, 700);

  container.style.transition = 'opacity 0.5s ease-in-out';
  setTimeout(function() {
    container.style.opacity = '0';
  }, 900);
  setTimeout(function() {
    container.parentNode.replaceChild(button, container);
    button.style.opacity = '1';
  }, 1200);    
  setTimeout(function() {
    span.textContent = '+';
    input.style.opacity = '1';
    fill.style.width = '0%'; 
    fill.parentNode.removeChild(fill);
    span.classList.remove('rotated'); 
  }, 1300); 


  // Récupérer la valeur saisie dans l'input
      var list = this.parentNode.previousElementSibling;
      var value = input.value.trim();
      if (value === '' || value === '0' || value.length > 255) {
        // Arrêter le code
        return;
      }

      // Créer un nouvel élément pour afficher la valeur saisie
      var newValueElement = document.createElement('a');
      newValueElement.textContent = value;
// Vérifier si la valeur saisie est une URL ou une adresse email
if (/((https?:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,63}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?(fr|com|org|ca|net|[a-z]{2,3})$/gmi) {
  // Le code à exécuter si la chaîne correspond au
  if (/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/g.test(value)) {
    // Si la valeur saisie est une adresse email, créer un lien 'mailto'
    newValueElement = document.createElement('a');
    newValueElement.href = 'mailto:' + value; // ajouter le préfixe 'mailto:' pour créer un lien cliquable
    newValueElement.textContent = value;
    newValueElement.target = "_blank";
  } else {
    // Si la valeur saisie est une URL, créer un lien 'a' et modifier le lien si nécessaire
    newValueElement = document.createElement('a');
    if (/^www\./i.test(value)) {
      value = 'https://' + value;
    } else if(/^(?!www\.)[\d\w][-\d\w]{0,253}[\d\w]\.(fr|com|org|ca|net|[a-z]{2,3})$/i.test(value)){
      value = 'https://' + value;

    } else if (/^http:/i.test(value)) {
      value = value.replace(/^http:/i, 'https:');
    } else if (value.indexOf('.') === -1){
      // Si la valeur saisie n'est ni une URL ni une adresse email, créer un élément 'p'
      newValueElement = document.createElement('p');
      newValueElement.textContent = value;
    }
    newValueElement.href = value;
    newValueElement.textContent = value;
    newValueElement.setAttribute('href', value);
    newValueElement.target = "_blank";
  }
}
      newValueElement.setAttribute('title', 'En attente de validation');
    
      // Créer un nouvel élément "img" pour le favicon
      var newFaviconElement = document.createElement('img');
      newFaviconElement.className = 'favicon-icon';
      newFaviconElement.src = favicon.src;
    
// Créer un nouvel élément "li" pour englober l'ensemble des éléments créés
var newLiElement = document.createElement('li');
newLiElement.classList.add('proposition');
newLiElement.appendChild(newFaviconElement);
newLiElement.appendChild(document.createTextNode('\u00A0\u231B ')); // ajouter l'icône horloge avec un espace insécable
newLiElement.appendChild(newValueElement);

// Enregistrer le lien dans la base de données
var link = {
  url: value,
  statut: '\u231B', // par défaut, mettre l'icône horloge
  articleTitle: document.title, // titre de l'article où le lien a été soumis
  titresection: titleText,
  favicon : favicon.src
  };
  
  function sendToAkismet(link, apiKey) {
  // Construire les données à envoyer à Akismet
  var data = {
  user_ip: window.location.hostname,
  user_agent: navigator.userAgent,
  comment_type: 'pingback',
  comment_author: 'VeF Contributeur',
  comment_author_email: '',
  comment_author_url: '',
  comment_content: '',
  referrer: window.location.href,
  permalink: window.location.href,
  blog_lang: '',
  blog_charset: '',
  is_test: 'true',
  apiKey: apiKey
  };
  data = Object.assign(data, link);
  
  // Construire l'URL d'API Akismet
  var url = 'https://' + apiKey + '.rest.akismet.com/1.1/submit-pingback';
  
  // Envoyer les données à Akismet via une requête POST
  var xhr = new XMLHttpRequest();
  xhr.open('POST', url, true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
  if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
    console.log(xhr.responseText);
  }
  };
  var params = Object.keys(data).map(function(key) {
  return encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
  }).join('&');
  xhr.send(params);
  }

// Insérer le nouvel élément "li" dans le DOM avant l'input
list.parentNode.insertBefore(newLiElement, list.nextSibling);

// Recalculer la hauteur de la div .reponse correspondante
var reponse = this.closest('.reponse');
if (reponse) {
setTimeout(function() {
reponse.style.height = reponse.scrollHeight + 'px';
});
}

var title = this.parentNode.parentNode.querySelector('h4');
 if (title == null || title == undefined) {
  var title = this.parentNode.parentNode.previousElementSibling;
 }else if (title == null || title == undefined) {
  title = this.parentNode.previousElementSibling.previousElementSibling.previousElementSibling;
}

var titleText = title.textContent;

  sendToAkismet(link, 'c851d751f4b6')

  var xhr = new XMLHttpRequest();
  xhr.open('POST', vef_ajax_object.ajax_url, true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      console.log(xhr.responseText);
    }
  };
  xhr.send('action=vef_liens_contributeurs&url=' + encodeURIComponent(value) + '&titre_section='+ encodeURIComponent(titleText) + '&status=' +  encodeURIComponent('\u231B') + '&favicon=' + encodeURIComponent(favicon.src) + '&article_title=' + encodeURIComponent(document.title) + '&nonce=' + encodeURIComponent(vef_ajax_object.nonce));
});
    

// Remplacer le bouton par le conteneur
this.parentNode.replaceChild(container, this);
input.focus();
});
});