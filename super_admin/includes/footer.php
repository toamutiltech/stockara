    </div> <!-- End padding content -->
</div> <!-- End main-content -->

<div class="footer sticky-bottom bg-white border-top py-3 px-5 text-center text-muted small">
    <p class="mb-0">&copy; <?php echo date('Y'); ?> Stockara SaaS Management Portal. Developed by <span class="fw-bold">Toa Multi Tech</span></p>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sidebarToggle').click(function() {
            $('#sidebar').toggleClass('show');
        });
    });
</script>
</body>
</html>
