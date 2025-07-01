</main>
        </div>
    </div>
    <script>
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const color = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const notifDiv = document.createElement('div');
            notifDiv.className = `px-4 py-2 text-white ${color} rounded-lg shadow-lg animate-pulse flex items-center`;
            notifDiv.innerHTML = `<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'}"></path></svg>${message}`;
            notification.appendChild(notifDiv);
            setTimeout(() => {
                notifDiv.classList.add('animate-fade-out');
                setTimeout(() => notifDiv.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>