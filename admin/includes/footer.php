  </main><!-- /.admin-main -->
</div><!-- /.admin-layout -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Sidebar toggle
  const toggle   = document.getElementById('sidebarToggle');
  const sidebar  = document.getElementById('adminSidebar');
  const overlay  = document.getElementById('sidebarOverlay');

  function openSidebar()  { sidebar.classList.add('open');   overlay.classList.add('show'); }
  function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

  toggle?.addEventListener('click', () => sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
  overlay?.addEventListener('click', closeSidebar);
</script>
</body>
</html>
