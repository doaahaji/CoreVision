document.addEventListener("DOMContentLoaded", () => {
  fetch("/PFEnagios/api/stats-section.php")
    .then(res => res.json())
    .then(data => {
      // Debug console
      console.log("Données stats :", data);

      // Insertion des valeurs
      document.querySelector(".stat-up").textContent = data.up;
      document.querySelector(".stat-down").textContent = data.down;
      document.querySelector(".stat-unreachable").textContent = data.unreachable;
      document.querySelector(".stat-pending").textContent = data.pending;

      document.querySelector(".total-hosts").textContent = data.total_hosts;
      document.querySelector(".total-services").textContent = data.total_services;

      document.querySelector(".host-health-percent").textContent = data.host_health + "%";
      document.querySelector(".host-health-bar").style.width = data.host_health + "%";

      document.querySelector(".service-health-percent").textContent = data.service_health + "%";
      document.querySelector(".service-health-bar").style.width = data.service_health + "%";

      document.querySelector(".avg-response").textContent = data.avg_response + " ms";

      // Donut chart
      const ctx = document.getElementById("hostDonutChart")?.getContext("2d");
      if (ctx) {
        new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: ['UP', 'DOWN', 'UNREACHABLE', 'PENDING'],
            datasets: [{
              data: [data.up, data.down, data.unreachable, data.pending],
              backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#facc15'],
              borderWidth: 0
            }]
          },
          options: {
            cutout: '65%',
            plugins: {
              legend: { display: false },
              tooltip: { enabled: false }
            }
          }
        });
      }
    })
    .catch(err => console.error("Erreur API stats :", err));
});

const toggleButton = document.getElementById("toggle-stats");
  const statsWrapper = document.getElementById("stats-wrapper");

  let isVisible = true;

  toggleButton.addEventListener("click", () => {
    isVisible = !isVisible;
    statsWrapper.classList.toggle("expanded", isVisible);
    statsWrapper.classList.toggle("collapsed", !isVisible);

    toggleButton.className = isVisible ? "fas fa-chevron-up" : "fas fa-chevron-down";
  });