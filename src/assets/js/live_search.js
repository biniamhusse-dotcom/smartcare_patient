let debounce;
const search = () => {
    const table = document.getElementById('patientTableBody');
    const form = document.getElementById('filterForm');
    const exportBtn = document.getElementById('exportBtn');
    const formData = new FormData(form);

    exportBtn.href = `export.php?${new URLSearchParams(formData).toString()}`;
    table.style.opacity = '0.4';

    fetch('fetch_data.php', { method: 'POST', body: formData })
    .then(r => r.text()).then(data => {
        table.innerHTML = data;
        table.style.opacity = '1';
    });
};

document.querySelectorAll('.search-input').forEach(i => {
    i.addEventListener('input', () => { clearTimeout(debounce); debounce = setTimeout(search, 300); });
});

document.getElementById('filterForm').addEventListener('submit', (e) => { e.preventDefault(); search(); });