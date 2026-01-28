    </div>
    <!-- End Main Content Wrapper -->

    <!-- Footer -->
    <footer class="footer py-4 mt-auto">
        <div class="container text-center">
            <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> Agora Campus. All rights reserved.</p>
        </div>
    </footer>

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Core JS -->
    <script src="assets/js/theme.js"></script>
    
    <!-- Global Initialization -->
    <script>
        // Initialize Animate On Scroll
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        
        // Tooltips initialization
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>
