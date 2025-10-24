const ctx_service = document.getElementById('service-chart');
const ctx_host = document.getElementById('host-chart');

const nagiosColors = {
  'UP': '#10b981',
  'OK': '#10b981',
  'DOWN': '#ef4444',
  'CRITICAL': '#ef4444',
  'WARNING': '#f59e0b',
  'UNKNOWN': '#9E9E9E',
  'PENDING': '#facc15',
  'UNREACHABLE': '#f59e0b'
};

let serviceChart;
let hostChart;
let jsonData;

// Count status frequencies
function countStatuses(items, key = 'status') {
  const count = {};
  items.forEach(item => {
    const status = item[key].toUpperCase();
    count[status] = (count[status] || 0) + 1;
  });
  return count;
}

// Fetch JSON
fetch('/PFEnagios/api/get_nagios_data.php')
  .then(response => response.ok ? response.json() : null)
  .then(data => {
    jsonData = data;
    createService_Chart(data, 'doughnut');
    createHost_Chart(data, 'doughnut');
  });

//to switch type
function sethost_chartType(type) {
  if (hostChart) hostChart.destroy();
  createHost_Chart(jsonData, type);
}

function setservice_chartType(type) {
  if (serviceChart) serviceChart.destroy();
  createService_Chart(jsonData, type);
}
//to create  service chart
function createService_Chart(data, type) {
  const isDoughnut = type === 'doughnut' || type === 'polarArea';
  let labels, dataValues, backgroundColors;
//if there is no data to show
  if (!data || !data.services || data.services.length === 0) {

    labels = ['No Data'];
    dataValues = [1];
    backgroundColors = ['#9E9E9E'];
  } else {
    const serviceStatusCounts = countStatuses(data.services, 'status');
    labels = Object.keys(serviceStatusCounts);
    dataValues = Object.values(serviceStatusCounts);
    backgroundColors = labels.map(label => nagiosColors[label.toUpperCase()] || '#9E9E9E');
  }

 // Clear previous chart instance
  if (window.serviceChart) {
    window.serviceChart.destroy();
  }
//to create the chart if there is a data
  window.serviceChart = new Chart(ctx_service, {
    type: type,
    data: {
      labels: labels,
      datasets: [{
        label: 'Service Status',
        data: dataValues,
        backgroundColor: backgroundColors,
        borderWidth: 0
      }]
    },
    options: {
      cutout: '65%',
      plugins: {
        legend: { display: false }
      },
      scales: isDoughnut ? {} : {
        y: { beginAtZero: true }
      }
    }
  });

  const legendHtml = labels.map((label, i) => `
    <li><span style="background-color: ${backgroundColors[i]}"></span>${label}</li>
  `).join('');
  document.getElementById('service-legend').innerHTML = `<ul>${legendHtml}</ul>`;
}


// Create Host Chart
function createHost_Chart(data, type) {
  const isDoughnut = type === 'doughnut' || type === 'polarArea';
  let labels, dataValues, backgroundColors;
//if there is no data to show
  if (!data || !data.hosts || data.hosts.length === 0) {

    labels = ['No Data'];
    dataValues = [1];
    backgroundColors = ['#9E9E9E'];
  } else {
    const hostStatusCount = countStatuses(data.hosts, 'status');
    labels = Object.keys(hostStatusCount);
    dataValues = Object.values(hostStatusCount);
    backgroundColors = labels.map(label => nagiosColors[label.toUpperCase()] || '#9E9E9E');
  }
 // Clear previous chart instance
  if (window.hostChart) {
    window.hostChart.destroy();
  }

//to create the chart if there is a data
  window.hostChart = new Chart(ctx_host, {
    type: type,
    data: {
      labels: labels,
      datasets: [{
        label: 'Host Status',
        data: dataValues,
        backgroundColor: backgroundColors,
        borderWidth: 0
      }]
    },
    options: {
      cutout: '65%',
      plugins: {
        legend: { display: false }
      },
      scales: isDoughnut ? {} : {
        y: { beginAtZero: true }
      }
    }
  });

  const legendHtml = labels.map((label, i) => `
    <li><span style="background-color: ${backgroundColors[i]}"></span>${label}</li>
  `).join('');
  document.getElementById('host-legend').innerHTML = `<ul>${legendHtml}</ul>`;
}
