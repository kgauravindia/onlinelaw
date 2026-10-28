<?php if (basename($_SERVER['PHP_SELF']) != 'login.php'): ?>
        </div> <!-- .content-body -->
    </main> <!-- .main-content -->
</div> <!-- .admin-layout -->
<?php endif; ?>

<!-- Toast Container for Admin Alerts -->
<div id="modernToastContainer" class="modern-toast-container" aria-live="polite"></div>

<!-- Core JavaScript Dependencies -->
<script src="../js/popper-1.14.3.min.js"></script>
<script src="../js/bootstrap-4.1.3.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script>
(function() {
    // 1. Toast Notification Helper for Admin
    window.showModernToast = function(message, type) {
        type = type || 'success';
        var container = document.getElementById('modernToastContainer');
        if (!container) return;

        var toast = document.createElement('div');
        toast.className = 'modern-toast toast-' + type;

        var iconMap = {
            success: 'fas fa-check-circle',
            info: 'fas fa-info-circle',
            error: 'fas fa-exclamation-circle'
        };
        var iconClass = iconMap[type] || iconMap.info;

        toast.innerHTML = 
            '<i class="' + iconClass + ' modern-toast-icon"></i>' +
            '<div class="modern-toast-msg">' + message + '</div>' +
            '<button type="button" class="modern-toast-close" aria-label="Close">&times;</button>';

        container.appendChild(toast);

        var removeToast = function() {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(15px) scale(0.95)';
            setTimeout(function() {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 300);
        };

        toast.querySelector('.modern-toast-close').addEventListener('click', removeToast);
        setTimeout(removeToast, 3500);
    };

    // 2. Universal One-Click Copy for Admin (API Keys, CIN, IFSC, etc.)
    document.addEventListener('click', function(e) {
        var copyTarget = e.target.closest('[data-copy], .copy-btn');
        if (!copyTarget) return;

        var textToCopy = copyTarget.getAttribute('data-copy') || copyTarget.innerText.trim();
        if (!textToCopy) return;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy).then(function() {
                window.showModernToast('Copied: <strong>' + textToCopy + '</strong>', 'success');
            }).catch(function() {
                fallbackCopy(textToCopy);
            });
        } else {
            fallbackCopy(textToCopy);
        }
    });

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            document.execCommand('copy');
            window.showModernToast('Copied: <strong>' + text + '</strong>', 'success');
        } catch(err) {}
        document.body.removeChild(ta);
    }

    // 3. Sidebar Responsive & Layout Logic
    const layout = document.getElementById('adminLayout');
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const closeBtn = document.getElementById('sidebarCloseBtn');

    // Restore desktop sidebar collapsed preference
    if (localStorage.getItem('admin-sidebar-collapsed') === 'true' && layout) {
        layout.classList.add('sidebar-collapsed');
    }

    // Desktop Collapse Toggle
    if (collapseBtn && layout) {
        collapseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            layout.classList.toggle('sidebar-collapsed');
            localStorage.setItem('admin-sidebar-collapsed', layout.classList.contains('sidebar-collapsed'));
        });
    }

    // Mobile Sidebar Logic
    window.toggleMobileSidebar = function() {
        if (sidebar && backdrop) {
            sidebar.classList.add('show');
            backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    };

    function hideMobileSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    if (closeBtn) closeBtn.addEventListener('click', hideMobileSidebar);
    if (backdrop) backdrop.addEventListener('click', hideMobileSidebar);
})();
</script>
</body>
</html>
