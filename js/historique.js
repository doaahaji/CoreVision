// Variables globales
let originalData = {};

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation
    initHistory();
    
    // Gestion des événements
    document.getElementById('time-filter').addEventListener('change', function() {
        filterByTimeframe(this.value);
    });
    
    document.getElementById('refresh-btn')?.addEventListener('click', function() {
        refreshData(this);
    });
});

// Fonction d'initialisation
function initHistory() {
    updateLastUpdate();
    loadHistoryData();
}

// Chargement des données
function loadHistoryData() {
    fetch('/PFEnagios/api/get_nagios_history.php')
        .then(response => {
            if (!response.ok) throw new Error('Erreur de réseau');
            return response.json();
        })
        .then(data => {
            // Conversion et nettoyage des données
            originalData = processRawData(data);
            // Applique le filtre des 24h par défaut
            filterByTimeframe('24h'); 
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError(error);
        });
}

// Traitement des données brutes
function processRawData(data) {
    return Object.entries(data).reduce((acc, [date, entries]) => {
        acc[date] = entries.map(entry => ({
            ...entry,
            timestamp: ensureNumber(entry.timestamp),
            state_type: entry.state_type || '',
            info: entry.info || ''
        }));
        return acc;
    }, {});
}

// Filtrage temporel
function filterByTimeframe(timeframe) {
    if (!originalData) return;

    const cutoffTime = calculateCutoffTime(timeframe);
    
    if (timeframe === 'all') {
        displayHistory(originalData);
        return;
    }

    const filteredData = filterDataByTime(originalData, cutoffTime);
    displayHistory(filteredData);
}

// Calcul du temps de coupure
function calculateCutoffTime(timeframe) {
    const nowSeconds = Math.floor(Date.now() / 1000);
    const timeframes = {
        '1h': 3600,
        '12h': 43200,
        '24h': 86400,
        '7d': 604800
    };
    return nowSeconds - (timeframes[timeframe] || 86400);
}

// Filtrage des données par période
function filterDataByTime(data, cutoffTime) {
    return Object.entries(data).reduce((acc, [date, entries]) => {
        const filteredEntries = entries.filter(entry => entry.timestamp >= cutoffTime);
        if (filteredEntries.length > 0) acc[date] = filteredEntries;
        return acc;
    }, {});
}

// Rafraîchissement des données
function refreshData(button) {
    if (button) {
        button.classList.add('rotating');
        setTimeout(() => button.classList.remove('rotating'), 500);
    }
    loadHistoryData();
}

// Affichage de l'historique
function displayHistory(data) {
    const container = document.getElementById('history-data');
    if (!container) return;
    
    container.innerHTML = '';

    if (!data || Object.keys(data).length === 0) {
        container.innerHTML = '<p class="no-data">Aucune donnée disponible</p>';
        return;
    }

    // Trier et afficher les données
    const sortedDates = Object.keys(data).sort((a, b) => new Date(b) - new Date(a));
    
    sortedDates.forEach(date => {
        createDateSection(container, date, data[date]);
    });
}

// Création d'une section de date
function createDateSection(container, date, entries) {
    const dateHeader = document.createElement('div');
    dateHeader.className = 'date-header';
    dateHeader.innerHTML = `
        <h3>${formatFullDate(date)}</h3>
        <div class="separator"></div>
    `;
    container.appendChild(dateHeader);

    // Trier et afficher les entrées
    entries.sort((a, b) => b.timestamp - a.timestamp)
           .forEach(entry => createLogEntry(container, entry));
}

// Création d'une entrée de log
function createLogEntry(container, entry) {
    const entryDiv = document.createElement('div');
    entryDiv.className = 'log-entry';
    
    entryDiv.innerHTML = `
        <span class="log-time">${formatTime(entry.timestamp)}</span>
        <div class="log-content">
            <span class="log-type">${entry.type} ALERT</span>
            <span class="log-host">${entry.host}</span>
            ${entry.service ? `<span class="log-service">${entry.service}</span>` : ''}
            <span class="log-status ${getStatusClass(entry.status, entry.type)}">${entry.status}</span>
            <span class="log-state">${entry.state_type}</span>
            <span class="log-info">${entry.info}</span>
        </div>
    `;
    
    container.appendChild(entryDiv);
}

// Fonctions utilitaires
function ensureNumber(value) {
    return typeof value === 'number' ? value : parseInt(value, 10);
}

function updateLastUpdate() {
    const element = document.getElementById('last-update');
    if (element) {
        element.textContent = `Dernière actualisation: ${new Date().toLocaleString('fr-FR')}`;
    }
}

function showError(error) {
    const container = document.getElementById('history-data');
    if (container) {
        container.innerHTML = `
            <div class="error">
                Erreur : ${error.message || error}
            </div>
        `;
    }
}

function formatTime(timestamp) {
    return new Date(timestamp * 1000).toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function formatFullDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function getStatusClass(status, type) {
    if (!status) return '';
    
    status = status.toUpperCase();
    const statusClasses = {
        'HOST': {
            'UP': 'state-up',
            'DOWN': 'state-down',
            'UNREACHABLE': 'state-unreachable'
        },
        'SERVICE': {
            'OK': 'state-ok',
            'WARNING': 'state-warning',
            'CRITICAL': 'state-critical',
            'UNKNOWN': 'state-unknown'
        }
    };
    
    return statusClasses[type]?.[status] || '';
}