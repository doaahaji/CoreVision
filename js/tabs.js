let monitoringData;

document.addEventListener("DOMContentLoaded", () => {
  const pageId = document.body.id;

  fetch('/PFEnagios/api/get_nagios_data.php')
    .then(response => response.json())
    .then(data => {
      monitoringData = data;
      
      if (pageId === 'index-page') {
        // Limit to 3 problems per type for index.php
        renderHostTable(3);
        renderServiceTable(3);
      } else {
        updateStatusSummary();
        // Show all problems for problems.php
        showAllProb();
      }
    })
    .catch(error => console.error('Erreur lors du chargement des données de surveillance :', error));
});

// Button event listeners
document.getElementById('all-prob').addEventListener("click", showAllProb);
document.getElementById('host-prob').addEventListener("click", showHostProb);
document.getElementById('service-prob').addEventListener("click", showServiceProb);

// Show functions
function showHostProb() {
  document.getElementById('host-table-container').style.display = 'block';
  document.getElementById('service-table-container').style.display = 'none';
  renderHostTable();
}

function showServiceProb() {
  document.getElementById('host-table-container').style.display = 'none';
  document.getElementById('service-table-container').style.display = 'block';
  renderServiceTable();
}

function showAllProb() {
  document.getElementById('host-table-container').style.display = 'block';
  document.getElementById('service-table-container').style.display = 'block';
  renderHostTable();
  renderServiceTable();
}

// Render host table
function renderHostTable(limit) {
  const container = document.getElementById('host-table-container');
  const hostsWithProblems = monitoringData.hosts.filter(host => host.status !== "UP");

  if (hostsWithProblems.length === 0) {
    container.innerHTML = `<h3 class="title-prob">Problèmes d’hôtes</h3><p class="no-problems">Aucun hôte avec des problèmes non traités pour le moment</p>`;
    const seeBtn = document.getElementById('see-host-prob');
    if (seeBtn) seeBtn.style.visibility = 'hidden';
    return;
  }

  const hostsToShow = limit ? hostsWithProblems.slice(0, limit) : hostsWithProblems;

  let table = `<h3 class="title-prob">Problèmes d’hôtes</h3><table class="problems-table" id="host-problems-table">
  <thead>
    <tr class="table-header">
      <th>Hôte</th>
      <th>Status</th>
      <th>Dernière vérif</th>
      <th>Durée</th>
      <th>Infos statut</th>
    </tr>
  </thead><tbody>`;

  hostsToShow.forEach(host => {
    table += `<tr>
      <td>${host.host}</td>
      <td><span class="badge ${host.status.toLowerCase()}">${host.status}</span></td>
      <td>${host.last_check}</td>
      <td>${host.duration}</td>
      <td>${host.status_info}</td>
    </tr>`;
  });

  table += '</tbody></table>';
  container.innerHTML = table;
}

// Render service table with services grouped by host
function renderServiceTable(limit) {
  const container = document.getElementById('service-table-container');
  const servicesWithProblems = monitoringData.services.filter(service => service.status !== "OK");

  if (servicesWithProblems.length === 0) {
    container.innerHTML = `<h3 class="title-prob">Problèmes de services</h3><p class="no-problems">Actuellement, aucun service avec des problèmes non traités</p>`;
    const seeBtn = document.getElementById('see-service-prob');
    if (seeBtn) seeBtn.style.visibility = 'hidden';
    return;
  }

  // Group services by host
  const servicesByHost = {};
  servicesWithProblems.forEach(service => {
    if (!servicesByHost[service.host]) {
      servicesByHost[service.host] = [];
    }
    servicesByHost[service.host].push(service);
  });

  // Apply limit per host if needed
  const servicesToShow = {};
  if (limit) {
    Object.keys(servicesByHost).forEach(host => {
      servicesToShow[host] = servicesByHost[host].slice(0, limit);
    });
  } else {
    Object.assign(servicesToShow, servicesByHost);
  }

  let table = `<h3 class="title-prob">Problèmes de services</h3><table class="problems-table" id="service-problems-table">
  <thead>
    <tr class="table-header">
      <th>Hôte</th>
      <th>Service</th>
      <th>Status</th>
      <th>Dernière vérif</th>
      <th>Durée</th>
      <th>Tentative</th>
      <th>Infos statut</th>
    </tr>
  </thead><tbody>`;

  // Generate rows grouped by host
  Object.keys(servicesToShow).forEach(host => {
    const hostServices = servicesToShow[host];
    
    // Add first service row with host name
    table += `<tr>
      <td rowspan="${hostServices.length}" class="host-cell">${host}</td>
      <td>${hostServices[0].service}</td>
      <td><span class="badge ${hostServices[0].status.toLowerCase()}">${hostServices[0].status}</span></td>
      <td>${hostServices[0].last_check}</td>
      <td>${hostServices[0].duration}</td>
      <td>${hostServices[0].attempt}</td>
      <td>${hostServices[0].status_info}</td>
    </tr>`;

    // Add remaining services for this host (without host name)
    for (let i = 1; i < hostServices.length; i++) {
      table += `<tr>
        <td>${hostServices[i].service}</td>
        <td><span class="badge ${hostServices[i].status.toLowerCase()}">${hostServices[i].status}</span></td>
        <td>${hostServices[i].last_check}</td>
        <td>${hostServices[i].duration}</td>
        <td>${hostServices[i].attempt}</td>
        <td class="status-info">${hostServices[i].status_info}</td>
      </tr>`;
    }
  });

  table += '</tbody></table>';
  container.innerHTML = table;
}

function updateStatusSummary() {
  const hosts = monitoringData.hosts;
  const services = monitoringData.services;

  // Compter les statuts des hôtes
  const downHosts = hosts.filter(h => h.status === "DOWN").length;
  const unreachableHosts = hosts.filter(h => h.status === "UNREACHABLE").length;
  const pendingHosts = hosts.filter(h => h.status === "PENDING").length;

  // Compter les services en WARNING
  const warningServices = services.filter(s => s.status === "WARNING").length;
  const criticalServices = services.filter(s => s.status === "CRITICAL").length;
  const unknownServices = services.filter(s => s.status === "UNKNOWN").length;
  const pendingServices = services.filter(s => s.status === "PENDING").length;
  const totalproblems = downHosts + unreachableHosts + pendingHosts + warningServices + criticalServices + unknownServices + pendingServices;
  
  // Mettre à jour le HTML
  document.getElementById("down-hosts").textContent = downHosts;
  document.getElementById("unreachable-hosts").textContent = unreachableHosts;
  document.getElementById("pending-hosts").textContent = pendingHosts;
  document.getElementById("warning-services").textContent = warningServices;
  document.getElementById("critical-services").textContent = criticalServices;
  document.getElementById("unknown-services").textContent = unknownServices;
  document.getElementById("pending-services").textContent = pendingServices;
  document.getElementById("total-problems").textContent = totalproblems;
}