<?php
// includes/footer.php
?>
            </div> </div> </div> <script>
        // Script jam bisa diletakkan di sini jika dibutuhkan di semua halaman
        function updateJam() {
            const jamElement = document.getElementById('jam');
            if (jamElement) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                jamElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }
        setInterval(updateJam, 1000);
        if (document.getElementById('jam')) {
            updateJam();
        }
    </script>
</body>
</html>