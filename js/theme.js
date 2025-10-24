const themeBtn = document.getElementById('theme-toggle');
const themeIcon = themeBtn.querySelector('i');
const body = document.body;

const darkRadio = document.getElementById('dark-mode');
const lightRadio = document.getElementById('light-mode');

// Récupérer le thème depuis le localStorage
let isDark = localStorage.getItem('theme') === 'dark';

// Appliquer le thème au chargement
body.classList.toggle('dark-mode', isDark);
themeIcon.classList.add(isDark ? 'fa-sun' : 'fa-moon');

// Cocher la bonne radio au chargement
darkRadio.checked = isDark;
lightRadio.checked = !isDark;

// Bouton toggle
themeBtn.addEventListener('click', () => {
  isDark = !isDark;

  // Appliquer le thème
  body.classList.toggle('dark-mode', isDark);

  // Changer l'icône
  themeIcon.classList.remove('fa-sun', 'fa-moon');
  themeIcon.classList.add(isDark ? 'fa-sun' : 'fa-moon');

  // Sauvegarder
  localStorage.setItem('theme', isDark ? 'dark' : 'light');

  // Mettre à jour les radios
  darkRadio.checked = isDark;
  lightRadio.checked = !isDark;
});

// Radios : changement manuel via sélection
darkRadio.addEventListener('change', () => {
  if (darkRadio.checked) {
    body.classList.add('dark-mode');
    themeIcon.classList.remove('fa-moon');
    themeIcon.classList.add('fa-sun');
    localStorage.setItem('theme', 'dark');
  }
});

lightRadio.addEventListener('change', () => {
  if (lightRadio.checked) {
    body.classList.remove('dark-mode');
    themeIcon.classList.remove('fa-sun');
    themeIcon.classList.add('fa-moon');
    localStorage.setItem('theme', 'light');
  }
});
