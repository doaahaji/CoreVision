document.addEventListener('DOMContentLoaded', function () {
 const notifButton = document.getElementById('notif-button');
 const notifDropdown = document.getElementById('notification-dropdown');
 const notifList = document.getElementById('notification-list');

 // Ouvrir/fermer la boîte
 notifButton.addEventListener('click', () => {
  notifDropdown.classList.toggle('visible');
 });

 // Fermer en cliquant à l'extérieur
 document.addEventListener('click', (e) => {
  if (!notifDropdown.contains(e.target) && !notifButton.contains(e.target)) {
   notifDropdown.classList.remove('visible');
  }
 });

 // Requête vers API
 fetch('/PFEnagios/api/notification.php')
  .then(response => response.json())
  .then(data => {
   notifList.innerHTML = '';

   if (data.length === 0) {
    notifList.innerHTML = '<li class="notification-item">Aucun problème détecté</li>';
    return;
   }

   // Afficher badge rouge
   notifButton.classList.add('has-alert');

   data.slice(0, 3).forEach(problem => {
    const item = document.createElement('li');
    item.className = 'notification-item';
    item.innerHTML = `
     <i class="fas fa-exclamation-circle"></i>
     <div class="notif-content">
      <div class="notif-title"><strong>${problem.type} :</strong> ${problem.name}</div>
      <span class="notif-time">${problem.time}</span>
     </div>
    `;
    notifList.appendChild(item);
   });
  })
  .catch(err => {
   notifList.innerHTML = '<li class="notification-item">Erreur de chargement</li>';
   console.error('Erreur chargement notifications:', err);
  });
});

