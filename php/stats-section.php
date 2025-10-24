<div id="stats-wrapper" class="host-stats-wrapper expanded">
  <section id="stats-section" class="host-stats">

    <!-- SECTION donut-graph -->
    <div class="donut-graph">
      <canvas id="hostDonutChart" width="120" height="120"></canvas>
      <ul class="legend">
        <li><span class="dot up"></span> UP</li>
        <li><span class="dot down"></span> DOWN</li>
        <li><span class="dot unreachable"></span> UNREACHABLE</li>
        <li><span class="dot pending"></span> PENDING</li>
      </ul>
    </div>

    <!-- SECTION stat-cards -->
    <div class="stat-cards vertical-layout">
      <div class="stat-card vertical up">
        <i class="fas fa-check-circle"></i>
        <div class="content">
          <strong class="stat-up"></strong>
          <span>UP</span>
        </div>
      </div>
      <div class="stat-card vertical unreachable">
        <i class="fas fa-minus-circle"></i>
        <div class="content">
          <strong class="stat-unreachable"></strong>
          <span>UNREACHABLE</span>
        </div>
      </div>
      <div class="stat-card vertical down">
        <i class="fas fa-exclamation-circle"></i>
        <div class="content">
          <strong class="stat-down"></strong>
          <span>DOWN</span>
        </div>
      </div>
      <div class="stat-card vertical pending">
        <i class="fas fa-clock"></i>
        <div class="content">
          <strong class="stat-pending"></strong>
          <span>PENDING</span>
        </div>
      </div>
    </div>

    <!-- SECTION health-response -->
    <div class="health-response-blocks">
      <div class="health-card">
        <h4>Santé du réseau</h4>
        <div class="health-line">
          <span>HOST SANTÉ</span>
          <div class="progress">
            <div class="progress-bar host-health-bar" style="width: 0%;"></div>
          </div>
          <span class="percent host-health-percent">0%</span>
        </div>
        <div class="health-line">
          <span>SERVICE SANTÉ</span>
          <div class="progress">
            <div class="progress-bar service-health-bar" style="width: 0%;"></div>
          </div>
          <span class="percent service-health-percent">0%</span>
        </div>
      </div>

      <!-- Temps moyen de réponse -->
      <div class="response-card">
        <h4>Temps moyen de réponse</h4>
        <div class="response-value">⏱️ <strong class="avg-response">... ms</strong></div>
        <p class="subtext">basé sur les 5 derniers checks</p>
      </div>
    </div>

    <!-- SECTION total-boxes -->
    <div class="total-boxes">
      <div class="stat-card vertical">
        <i class="fas fa-server"></i>
        <div class="content">
          <strong class="total-hosts">0</strong>
          <span>Total Hosts</span>
        </div>
      </div>
      <div class="stat-card vertical">
        <i class="fas fa-cogs"></i>
        <div class="content">
          <strong class="total-services">0</strong>
          <span>Total Services</span>
        </div>
      </div>
    </div>

  </section>
</div>

<div style="text-align: center; margin-top: 10px;">
  <i id="toggle-stats" class="fas fa-chevron-up" style="cursor: pointer; font-size: 20px; margin: 10px;"></i>
</div>
