<div class="filters-bar">
  <div class="filters-actions">
  <button id="toggle-filters" class="toggle-filter-btn">Filtres des hosts</button>

  <div id="filters-popup" class="filters hidden">
    <button class="filter-btn" data-filter="all">
      <i class="fas fa-list-ul"></i> Tous
    </button>
    <button class="filter-btn" data-filter="up">
      <i class="fas fa-arrow-up"></i> UP
    </button>
    <button class="filter-btn" data-filter="down">
      <i class="fas fa-arrow-down"></i> DOWN
    </button>
    <button class="filter-btn" data-filter="unreachable">
      <i class="fas fa-unlink"></i> UNREACHABLE
    </button>
    <button class="filter-btn" data-filter="pending">
      <i class="fas fa-clock"></i> PENDING
    </button>
  </div>

  <button id="resetSortBtn" class="toggle-filter-btn">Réinitialiser le tri</button>
  </div>
  
  <input type="text" id="searchInput" class="search-input host-search" placeholder="Rechercher un hôte ou service...">
</div>

