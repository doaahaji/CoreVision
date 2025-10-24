<div class="filters-bar">
  <div class="filters-actions">
  <button id="toggle-filters" class="toggle-filter-btn">Filtres des services</button>

  <div id="filters-popup" class="filters hidden">
    <button class="filter-btn" data-filter="all">
      <i class="fas fa-list-ul"></i> Tous
    </button>
    <button class="filter-btn" data-filter="ok">
      <i class="fas fa-arrow-up"></i> OK
    </button>
    <button class="filter-btn" data-filter="critical">
      <i class="fas fa-arrow-down"></i> CRITICAL
    </button>
    <button class="filter-btn" data-filter="unknown">
      <i class="fas fa-unlink"></i> UNKNOWN
    </button>
    <button class="filter-btn" data-filter="warning">
      <i class="fas fa-exclamation-triangle"></i> WARNING
    </button>
    <button class="filter-btn" data-filter="pending">
      <i class="fas fa-clock"></i> PENDING
    </button>
  </div>

  <button id="resetSortBtn" class="toggle-filter-btn">Réinitialiser le tri</button>
  </div>
  
  <input type="text" id="searchInput" class="search-input host-search" placeholder="Rechercher un hôte ou service...">
</div>

