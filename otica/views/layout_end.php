    </main>
    
    <script>
        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = '../../controllers/LoginController.php?action=logout';
            }
        }
    </script>
</body>
</html>
