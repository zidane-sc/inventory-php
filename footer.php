</div> <!-- .container -->

<script>
    // Simple script to confirm deletions
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', e => {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });
</script>
</body>
</html>
