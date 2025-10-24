document.addEventListener("DOMContentLoaded", () => {
  const select = document.getElementById('timezone-select');

  // Get the list of timezones (works in modern browsers)
  const timezones = Intl.supportedValuesOf('timeZone');

  timezones.forEach(tz => {
    const option = document.createElement('option');
    option.value = tz;
    option.textContent = tz;

    // Pré-sélectionner le timezone courant
    if (typeof currentUserTimezone !== 'undefined' && tz === currentUserTimezone) {
      option.selected = true;
    }

    select.appendChild(option);
  });
});
