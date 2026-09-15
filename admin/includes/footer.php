<?php if (basename($_SERVER['PHP_SELF']) != 'login.php'): ?>
        </div> <!-- .content-body -->
    </main> <!-- .main-content -->
</div> <!-- .admin-layout -->
<?php endif; ?>

<!-- Core JavaScript Dependencies -->
<script src="../js/jquery-3.3.1.min.js"></script>
<script src="../js/popper-1.14.3.min.js"></script>
<script src="../js/bootstrap-4.1.3.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script>
(function() {
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
