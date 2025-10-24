fetch('/PFEnagios/api/get_nagios_data.php')
  .then(response => response.json())
  .then(data => {
    document.getElementById('total-hosts').textContent = data.hosts_count ?? "0";
    document.getElementById('total-services').textContent = data.services_count ?? "0";
    document.getElementById('total-backends').textContent = data.backends_count ?? "0";
    document.getElementById('total-contacts').textContent = data.contacts_count ?? "0";
  })
  .catch(error => {
    console.error('Erreur de chargement Nagios:', error);
    document.getElementById('total-hosts').textContent = "null";
    document.getElementById('total-services').textContent = "null";
    document.getElementById('total-backends').textContent = "null";
    document.getElementById('total-contacts').textContent = "null";
  });