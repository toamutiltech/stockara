    </div><!-- /.container-fluid -->
</div><!-- /#content-wrapper -->
</div><!-- /#wrapper -->

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function () {
    // Sidebar Toggle
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

    // Dark Mode Toggle
    $('#darkModeToggle').on('click', function () {
        $('body').toggleClass('dark-mode');
        let mode = $('body').hasClass('dark-mode') ? 'enabled' : 'disabled';
        document.cookie = "dark_mode=" + mode + "; path=/; max-age=" + (30*24*60*60);
        
        let icon = $(this).find('i');
        if (mode === 'enabled') {
            icon.removeClass('fa-moon').addClass('fa-sun');
        } else {
            icon.removeClass('fa-sun').addClass('fa-moon');
        }
    });

    // Set initial icon
    if ($('body').hasClass('dark-mode')) {
        $('#darkModeToggle i').removeClass('fa-moon').addClass('fa-sun');
    }
});
</script>

</body>
</html>
