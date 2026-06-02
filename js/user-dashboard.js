/* js/user-dashboard.js */

function openUserModal() { 
    document.getElementById('userSettingsModal').style.display = 'block'; 
}

function closeUserModal() { 
    document.getElementById('userSettingsModal').style.display = 'none'; 
}

function toggleNotifDropdown() {
    const box = document.getElementById('notifBox');
    box.style.display = (box.style.display === 'block') ? 'none' : 'block';
}

// Global windows background interaction interceptor scope execution
window.onclick = function(event) {
    const userModal = document.getElementById('userSettingsModal');
    if (event.target == userModal) { 
        userModal.style.display = 'none'; 
    }
}