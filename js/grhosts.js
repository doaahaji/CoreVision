document.addEventListener("DOMContentLoaded", () => {
  let hosts = [];
  let currentFilter = "all";
  let sortBy = null;
  let sortAsc = true;

  const tbody = document.getElementById("hostGroupBody");
  const searchInput = document.getElementById("searchInput");
  const filterButtons = document.querySelectorAll(".filter-btn");
  const tableHeaders = document.querySelectorAll("th[data-sort]");
  const resetSortBtn = document.getElementById("resetSortBtn");

  function renderTable() {
    const search = searchInput?.value.toLowerCase() || "";

    let filtered = hosts.filter(h => {
      const matchSearch = h.name.toLowerCase().includes(search) || h.group.toLowerCase().includes(search);
      const matchFilter = currentFilter === "all" || h.host_status.toLowerCase() === currentFilter;
      return matchSearch && matchFilter;
    });

    if (sortBy) {
      filtered.sort((a, b) => {
        let valA = a[sortBy];
        let valB = b[sortBy];
        if (typeof valA === "string") valA = valA.toLowerCase();
        if (typeof valB === "string") valB = valB.toLowerCase();
        return valA < valB ? (sortAsc ? -1 : 1) : valA > valB ? (sortAsc ? 1 : -1) : 0;
      });
    }

    const grouped = {};
    for (const host of filtered) {
      if (!grouped[host.group]) grouped[host.group] = [];
      grouped[host.group].push(host);
    }

    tbody.innerHTML = "";

    for (const groupName in grouped) {
      const groupHosts = grouped[groupName];
      groupHosts.forEach((host, index) => {
        const tr = document.createElement("tr");
        tr.id = "host-" + encodeURIComponent(host.name); // 🔹 ID unique
        const serviceStatusFormatted = formatServiceStatus(host.service_status);
        tr.innerHTML = `
          ${index === 0 ? `<td rowspan="${groupHosts.length}" class="group-cell">${groupName}</td>` : ""}
          <td>${host.name}</td>
          <td><span class="status ${host.host_status.toLowerCase()}">${host.host_status}</span></td>
          <td><span class="count count">${host.service_count}</span></td>
          <td>${serviceStatusFormatted}</td>
          <td><button class="detail-btn" onclick="showDetails('${encodeURIComponent(host.name)}')">🔍</button></td>
        `;
        tbody.appendChild(tr);
      });
    }
  }

  function formatServiceStatus(text) {
    if (!text || text.trim() === "-") return "-";
    return text.split(",").map(entry => {
      const [count, state] = entry.trim().split(" ");
      return `<span class="status ${state.toLowerCase()}">${count} ${state.toUpperCase()}</span>`;
    }).join(" ");
  }

  function highlightQueryInRow(row, query) {
    const regex = new RegExp(`(${query})`, "gi");

    Array.from(row.children).forEach(cell => {
      const hasComplexHTML = Array.from(cell.childNodes).some(node => node.nodeType === 1);
      if (!hasComplexHTML) {
        const originalText = cell.textContent;
        const highlighted = originalText.replace(regex, '<mark class="highlight">$1</mark>');
        cell.innerHTML = highlighted;
      }
    });
  }

  function highlightSearchResult() {
    const params = new URLSearchParams(window.location.search);
    const query = params.get("q")?.toLowerCase();
    if (!query) return;

    const row = document.getElementById("host-" + encodeURIComponent(query));
    if (row) {
      row.scrollIntoView({ behavior: "smooth", block: "center" });
      row.style.backgroundColor = "#ffe599";
      setTimeout(() => row.style.backgroundColor = "", 3000);
    }
  }

  searchInput?.addEventListener("input", renderTable);

  filterButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      currentFilter = btn.dataset.filter;
      renderTable();
    });
  });

  tableHeaders.forEach(th => {
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

      renderTable();
    });
  });

  resetSortBtn?.addEventListener("click", () => {
    sortBy = null;
    sortAsc = true;
    tableHeaders.forEach(header => {
      const icon = header.querySelector(".sort-icon");
      if (icon) icon.className = "fa-solid fa-sort sort-icon";
    });
    renderTable();
  });

  fetch("/PFEnagios/api/grhosts.php")
    .then(res => res.json())
    .then(data => {
      hosts = data;
      renderTable();
      setTimeout(highlightSearchResult, 300);
    })
    .catch(err => {
      console.error("Erreur API :", err);
      tbody.innerHTML = "<tr><td colspan='6'>Erreur de chargement des données.</td></tr>";
    });
});

// Fonction de redirection vers hosts.php
function showDetails(hostName) {
  window.location.href = "/PFEnagios/php/pages/hosts.php?q=" + hostName;
}
