document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.querySelector(".nav-search");
  const suggestionBox = document.getElementById("search-suggestions");

  const endpoints = [
    {
      url: "/PFEnagios/api/hosts.php",
      key: "name",
      label: "Hôtes",
      page: "/PFEnagios/php/pages/hosts.php",
      display: item => item.name
    },
    {
      url: "/PFEnagios/api/services.php",
      key: "service",
      label: "Services",
      page: "/PFEnagios/php/pages/services.php",
      display: item => item.service
    },
    {
      url: "/PFEnagios/api/grhosts.php",
      key: "group",
      label: "Groupes Hôtes",
      page: "/PFEnagios/php/pages/grhosts.php",
      display: item => item.group
    },
    {
      url: "/PFEnagios/api/grservices.php",
      key: "group",
      label: "Groupes Services",
      page: "/PFEnagios/php/pages/grservices.php",
      display: item => item.group
    }
  ];

  let allData = [];

  async function fetchData() {
    const promises = endpoints.map(async (ep) => {
      try {
        const res = await fetch(ep.url);
        const data = await res.json();
        return data.map(item => ({ ...item, __meta: ep }));
      } catch (err) {
        console.error(`Erreur lors du chargement de ${ep.url}`, err);
        return [];
      }
    });

    const results = await Promise.all(promises);
    allData = results.flat();
  }

  function showSuggestions(query) {
    suggestionBox.innerHTML = "";
    suggestionBox.style.display = query ? "block" : "none";
    if (!query) return;

    const matched = {};

    for (const item of allData) {
      const { key, label, page, display } = item.__meta;
      const value = item[key]?.toLowerCase?.() || "";

      if (value.includes(query.toLowerCase())) {
        if (!matched[label]) matched[label] = new Map();
        matched[label].set(display(item), { page, value: display(item) });
      }
    }

    for (const groupLabel in matched) {
      const entries = Array.from(matched[groupLabel]);

      if (entries.length > 0) {
      const header = document.createElement("li");
      header.textContent = `${entries.length} ${groupLabel}`;
      header.classList.add("group-label");
      suggestionBox.appendChild(header);

        for (const [text, { page, value }] of entries) {
          const li = document.createElement("li");
          li.textContent = text;
          li.classList.add("suggestion-item");
          li.style.padding = "6px 12px";
          li.style.cursor = "pointer";
          li.addEventListener("click", () => {
            const encodedQuery = encodeURIComponent(value);
            window.location.href = `${page}?q=${encodedQuery}`;
          });
          suggestionBox.appendChild(li);
        }
      }
    }
  }

  searchInput?.addEventListener("input", () => {
    const query = searchInput.value.trim();
    showSuggestions(query);
  });

  searchInput?.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      suggestionBox.style.display = "none";
    }
    if (e.key === "Enter") {
      suggestionBox.style.display = "none";
    }
  });

  document.addEventListener("click", (e) => {
    if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
      suggestionBox.style.display = "none";
    }
  });

  fetchData();
});
