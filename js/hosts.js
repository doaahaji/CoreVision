document.addEventListener("DOMContentLoaded", () => {
  let hosts = [];
  let currentFilter = "all";
  let sortBy = null;
  let sortAsc = true;

  const tbody = document.getElementById("hostsBody");
  const searchInput = document.getElementById("searchInput");
  const filterButtons = document.querySelectorAll(".filter-btn");
  const tableHeaders = document.querySelectorAll("th[data-sort]");
  const resetSortBtn = document.getElementById("resetSortBtn");

  function getStateText(code) {
    return code === "0" ? "UP" : code === "1" ? "DOWN" : "UNREACHABLE";
  }

  function formatTimestamp(ts) {
    const timestamp = parseInt(ts, 10);
    if (!timestamp || isNaN(timestamp)) return "-";
    const date = new Date(timestamp * 1000);
    return date.toLocaleString("fr-FR");
  }

  function highlightText(cell, keyword) {
    const regex = new RegExp(`(${keyword})`, "gi");
    cell.innerHTML = cell.textContent.replace(regex, `<span class="highlight">$1</span>`);
  }

  function highlightSearchResult() {
    const params = new URLSearchParams(window.location.search);
    const query = params.get("q")?.toLowerCase();
    if (!query) return;

    const rows = document.querySelectorAll("tbody tr");
    let found = false;

    rows.forEach(row => {
      let rowMatches = false;
      const cells = row.querySelectorAll("td");

      cells.forEach(cell => {
        const originalText = cell.textContent.toLowerCase();
        if (originalText.includes(query)) {
          highlightText(cell, query);
          rowMatches = true;
        }
      });

      if (rowMatches) {
        row.scrollIntoView({ behavior: "smooth", block: "center" });
        found = true;
      }
    });

    if (!found) {
      console.warn("Aucun résultat trouvé dans les cellules.");
    }
  }

  function renderHosts() {
    const search = searchInput?.value.toLowerCase() || "";

    let filtered = hosts.filter(h => {
      const matchSearch = h.name.toLowerCase().includes(search);
      const matchFilter = currentFilter === "all" || getStateText(h.state).toLowerCase() === currentFilter;
      return matchSearch && matchFilter;
    });

    if (sortBy) {
      filtered.sort((a, b) => {
        let valA = a[sortBy]?.toLowerCase?.() ?? a[sortBy];
        let valB = b[sortBy]?.toLowerCase?.() ?? b[sortBy];
        if (valA < valB) return sortAsc ? -1 : 1;
        if (valA > valB) return sortAsc ? 1 : -1;
        return 0;
      });
    }

    tbody.innerHTML = "";
    for (const h of filtered) {
      const state = getStateText(h.state);
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${h.name}</td>
        <td>${h.ip}</td>
        <td><span class="status ${state.toLowerCase()}">${state}</span></td>
        <td>${h.group}</td>
        <td>${h.info}</td>
        <td>${formatTimestamp(h.last_check)}</td>
      `;
      tbody.appendChild(tr);
    }
  }

  searchInput?.addEventListener("input", renderHosts);

  filterButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      currentFilter = btn.dataset.filter;
      renderHosts();
    });
  });

  tableHeaders.forEach(th => {
    th.style.cursor = "pointer";
    th.addEventListener("click", () => {
      const key = th.dataset.sort;
      if (sortBy === key) sortAsc = !sortAsc;
      else {
        sortBy = key;
        sortAsc = true;
      }

      tableHeaders.forEach(header => {
        const icon = header.querySelector(".sort-icon");
        if (icon) icon.className = "fa-solid fa-sort sort-icon";
      });

      const icon = th.querySelector(".sort-icon");
      if (icon) {
        icon.className = `fa-solid ${sortAsc ? "fa-sort-up" : "fa-sort-down"} sort-icon`;
      }

      renderHosts();
    });
  });

  resetSortBtn?.addEventListener("click", () => {
    sortBy = null;
    sortAsc = true;

    tableHeaders.forEach(header => {
      const icon = header.querySelector(".sort-icon");
      if (icon) icon.className = "fa-solid fa-sort sort-icon";
    });

    renderHosts();
  });

  fetch("/PFEnagios/api/hosts.php")
    .then(res => res.json())
    .then(data => {
      hosts = data;
      renderHosts();
      setTimeout(highlightSearchResult, 300);
    })
    .catch(err => {
      console.error("Erreur lors du chargement des hôtes :", err);
      tbody.innerHTML = "<tr><td colspan='6'>Erreur de chargement des données.</td></tr>";
    });
});
