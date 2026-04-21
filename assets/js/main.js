// ============================================
// EduTrack - Main JavaScript
// ============================================

function openModal(title, bodyHtml) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-body').innerHTML = bodyHtml;
    document.getElementById('modal').classList.add('open');
    document.getElementById('modal-overlay').classList.add('open');
}

function closeModal() {
    document.getElementById('modal').classList.remove('open');
    document.getElementById('modal-overlay').classList.remove('open');
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(() => { toast.className = 'toast'; }, 3500);
}

function confirmDelete(url, name) {
    openModal('Confirm Delete', `
        <p style="color: var(--text-mid); margin-bottom:20px;">Are you sure you want to delete <strong>${name}</strong>? This action cannot be undone.</p>
        <div class="form-actions">
            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            <a href="${url}" class="btn btn-danger">Yes, Delete</a>
        </div>
    `);
}

// Auto-close success messages
document.addEventListener('DOMContentLoaded', function() {
    // Animate bars on page load
    const fills = document.querySelectorAll('[data-width]');
    fills.forEach(fill => {
        setTimeout(() => {
            fill.style.width = fill.dataset.width + '%';
        }, 200);
    });
});

// Search filter (live)
function liveFilter(inputId, tableId, colIndex) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        const rows = document.querySelectorAll(`#${tableId} tbody tr`);
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(val) ? '' : 'none';
        });
    });
}
