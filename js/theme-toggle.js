const themeBtn = document.getElementById('theme-toggle');
const themeIcon = themeBtn.querySelector('i');
const body = document.body;

// Récupérer le thème depuis le localStorage ou définir par défaut à clair
let isDark = localStorage.getItem('theme') === 'dark';

// Appliquer le thème au chargement
body.classList.toggle('dark-mode', isDark);
themeIcon.classList.add(isDark ? 'fa-sun' : 'fa-moon');

themeBtn.addEventListener('click', () => {
 isDark = !isDark;

 // Appliquer ou retirer la classe dark-mode
 body.classList.toggle('dark-mode', isDark);

 // Modifier l'icône
 themeIcon.classList.remove('fa-sun', 'fa-moon');
 themeIcon.classList.add(isDark ? 'fa-sun' : 'fa-moon');

 // Sauvegarder le choix dans localStorage
 localStorage.setItem('theme', isDark ? 'dark' : 'light');
});
