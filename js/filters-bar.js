
  const toggleBtn = document.getElementById("toggle-filters");
  const filterPopup = document.getElementById("filters-popup");

  toggleBtn.addEventListener("click", () => {
    filterPopup.classList.toggle("hidden");
  });

  // Clic en dehors pour fermer
  window.addEventListener("click", (e) => {
    if (!document.querySelector(".filters-bar").contains(e.target)) {
      filterPopup.classList.add("hidden");
    }
  });

