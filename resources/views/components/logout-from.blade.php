<!-- Hidden logout form -->
<form method="POST" action="{{ route('logout') }}" style="display: none;" id="logout-form">
    @csrf
</form>

<script>
// Simple logout function
function simpleLogout() {
    // Simple confirmation dialog
    if (confirm('Apakah Anda yakin ingin logout?')) {
        const btn = document.getElementById('logout-btn');
        const icon = document.getElementById('logout-icon');
        const text = document.getElementById('logout-text');
        
        // Show simple loading state
        if (btn) btn.classList.add('logout-loading');
        if (icon) icon.className = 'fas fa-spinner fa-spin text-red-500 mr-3 w-5 text-center';
        if (text) text.textContent = 'Keluar...';
        
        // Clear storage
        try {
            localStorage.clear();
            sessionStorage.clear();
        } catch(e) {
            console.log('Could not clear storage:', e);
        }
        
        // Simple form submission - no AJAX
        setTimeout(() => {
            document.getElementById('logout-form').submit();
        }, 500);
    }
}
</script>