<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <nav class="bg-gray-800 fixed left-0 top-0 h-full w-64 p-6 flex flex-col">
        <h1 class="text-2xl font-bold mb-8">Mint</h1>
        <div class="flex flex-col space-y-3 flex-grow">
            <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Dashboard</a>
            <a href="{{ route('accounts') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Accounts</a>
            <a href="{{ route('transactions') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Transactions</a>
            <a href="{{ route('categories') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Categories</a>

            <!-- Notifications Link -->
            <a href="{{ route('notifications') }}" class="relative px-3 py-2 rounded-lg bg-gray-700">
                Notifications
                <span id="notification-badge"
                      class="hidden absolute top-1 right-1 h-5 w-5 bg-red-600 text-white text-xs rounded-full flex items-center justify-center">
                    0
                </span>
            </a>

            <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Profile</a>
        </div>
        <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Logout</button>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 ml-0 sm:ml-64 p-6">
        <h2 class="text-3xl font-bold text-white mb-6">Your Notifications</h2>

        <div id="notificationsList" class="space-y-4">
            <p id="loadingMessage" class="text-gray-400">Loading notifications...</p>
        </div>
    </main>

    <script>
        const token = localStorage.getItem("jwt_token");
        if (!token) {
            window.location.href = "/login";
        }

        const notificationsList = document.getElementById('notificationsList');
        const loadingMessage = document.getElementById('loadingMessage');
        const notificationBadge = document.getElementById('notification-badge');
        const logoutBtn = document.getElementById('logoutBtn');

        logoutBtn.addEventListener("click", () => {
            localStorage.removeItem("jwt_token");
            window.location.href = "/login";
        });

        async function fetchUnreadCount() {
            try {
                const res = await fetch('/api/notifications/unread-count', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();

                if (data.count > 0) {
                    notificationBadge.textContent = data.count;
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }
            } catch (error) {
                console.error("Error fetching unread count:", error);
            }
        }

        async function markAsRead(id, redirectUrl) {
            try {
                await fetch(`/api/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (redirectUrl) window.location.href = redirectUrl;
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        }

        async function loadNotifications() {
            try {
                const res = await fetch('/api/notifications', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });

                if (!res.ok) throw new Error('Failed to fetch notifications.');

                const notifications = await res.json();
                notificationsList.innerHTML = '';

                if (notifications.length === 0) {
                    notificationsList.innerHTML = '<p class="text-gray-500 text-center py-8">No notifications.</p>';
                    return;
                }

                notifications.forEach(n => {
                    const isRead = n.read_at !== null;
                    const card = document.createElement('div');

                    card.className = `p-4 rounded-lg shadow-md transition 
                        ${isRead ? 'bg-gray-700 opacity-60' : 'bg-gray-700 hover:bg-gray-600 cursor-pointer'}`;

                    card.innerHTML = `
                        <div class="flex justify-between items-center">
                            <p class="text-white">${n.message}</p>
                            <span class="text-xs text-gray-400">${new Date(n.created_at).toLocaleString()}</span>
                        </div>
                    `;

                    if (!isRead) {
                        card.addEventListener('click', () => markAsRead(n.id, n.data?.url ?? null));
                    }

                    notificationsList.appendChild(card);
                });

            } catch (error) {
                loadingMessage.textContent = 'Could not load notifications.';
                console.error(error);
            }
        }

        window.onload = () => {
            loadNotifications();
            fetchUnreadCount();
        };
    </script>
</body>
</html>
