document.addEventListener("DOMContentLoaded", () => {
  let services = [];
  let currentFilter = "all";
  let sortBy = null;
  let sortAsc = true;

  const tbody = document.getElementById("serviceGroupBody");
  const searchInput = document.getElementById("searchInput");
  const filterButtons = document.querySelectorAll(".filter-btn");
  const tableHeaders = document.querySelectorAll("th[data-sort]");
  const resetSortBtn = document.getElementById("resetSortBtn");

  function highlightQueryInRow(row, query) {
    const regex = new RegExp(`(${query})`, "gi");
    Array.from(row.children).forEach(cell => {
      if (cell.children.length === 0) {
        cell.innerHTML = cell.textContent.replace(regex, '<mark class="highlight">$1</mark>');
      }
    });
  }

  function renderServices() {
    const search = searchInput?.value.toLowerCase() || "";

    let filtered = services.filter(s => {
      const matchSearch =
        s.service.toLowerCase().includes(search) || s.group.toLowerCase().includes(search);
      const matchFilter = currentFilter === "all" || s.state.toLowerCase() === currentFilter;
      return matchSearch && matchFilter;
    });

    if (sortBy) {
      filtered.sort((a, b) => {
        const valA = a[sortBy]?.toLowerCase?.() ?? "";
        const valB = b[sortBy]?.toLowerCase?.() ?? "";
        return valA < valB ? (sortAsc ? -1 : 1) : valA > valB ? (sortAsc ? 1 : -1) : 0;
      });
    }

    const grouped = {};
    for (const s of filtered) {
      if (!grouped[s.group]) grouped[s.group] = [];
      grouped[s.group].push(s);
    }

    tbody.innerHTML = "";

    for (const groupName in grouped) {
      const groupServices = grouped[groupName];
      groupServices.forEach((s, index) => {
        const tr = document.createElement("tr");
        tr.id = "service-" + encodeURIComponent(s.service);
        tr.innerHTML = `
          ${index === 0 ? `<td rowspan="${groupServices.length}">${s.group}</td>` : ""}
          <td>${s.service}</td>
          <td><span class="status ${s.state.toLowerCase()}">${s.state}</span></td>
          <td><button class="detail-btn" onclick="showDetail('${encodeURIComponent(s.service)}')">🔍</button></td>
        `;
        tbody.appendChild(tr);
      });
    }

    highlightSearchResult();
  }

  function highlightSearchResult() {
    const params = new URLSearchParams(window.location.search);
    const query = params.get("q")?.toLowerCase();
    if (!query) return;

    const rows = document.querySelectorAll("tbody tr");
    rows.forEach(row => {
      if (row.innerText.toLowerCase().includes(query)) {
        row.scrollIntoView({ behavior: "smooth", block: "center" });
        highlightQueryInRow(row, query);
      }
    });
  }

  searchInput?.addEventListener("input", renderServices);

  filterButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      currentFilter = btn.dataset.filter;
      renderServices();
    });
  });

  tableHeaders.forEach(th => {
    th.style.cursor = "pointer";
    th.addEventListener("click", () => {
      const key = th.dataset.sort;
      sortBy = key === sortBy ? null : key;
      sortAsc = !sortAsc;
      renderServices();
    });
  });

  resetSortBtn?.addEventListener("click", () => {
    sortBy = null;
    sortAsc = true;
    renderServices();
  });

  fetch("/PFEnagios/api/grservices.php")
    .then(res => res.json())
    .then(data => {
      services = data;
      renderServices();
    });

  // Fonction globale pour le bouton 
  window.showDetail = function(serviceName) {
    window.location.href = "/PFEnagios/php/pages/services.php?q=" + serviceName;
  };
});
