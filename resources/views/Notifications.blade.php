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
      <a href="{{ route('bills') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Bills</a>
      <a href="{{ route('budgets') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Budgets</a>
      <a href="{{ route('categories') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Categories</a>
      <a href="{{ route('transactions') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Transactions</a>
      <a href="{{ route('goals') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Goals</a>
      <a href="{{ route('notifications') }}" class="px-3 py-2 rounded-lg bg-gray-700">Notifications</a>
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
    window.location.href = window.location.origin + "/login";
}

const notificationsList = document.getElementById('notificationsList');
const loadingMessage = document.getElementById('loadingMessage');
const logoutBtn = document.getElementById('logoutBtn');
const notificationBadge = document.getElementById('notification-badge');

logoutBtn.addEventListener("click", () => {
    localStorage.removeItem("jwt_token");
    window.location.href = window.location.origin + "/login";
});

async function fetchUnreadCount() {
    try {
        const res = await fetch('/api/notifications/unread-count', {
            headers: {
                'Authorization': `Bearer ${token}`,  // ✅ SEND JWT TOKEN
                'Accept': 'application/json'
            }
        });

        if (!res.ok) throw new Error("Unauthorized");
        const data = await res.json();

        if (data.count > 0) {
            notificationBadge.textContent = data.count;
            notificationBadge.classList.remove('hidden');
        } else {
            notificationBadge.classList.add('hidden');
        }
    } catch (error) {
        console.error("Could not fetch notification count:", error);
    }
}

async function loadNotifications() {
    try {
        const res = await fetch('/api/notifications', {
            headers: {
                'Authorization': `Bearer ${token}`,  // ✅ SEND JWT TOKEN
                'Accept': 'application/json'
            }
        });

        if (!res.ok) throw new Error("Unauthorized");

        const notifications = await res.json();
        notificationsList.innerHTML = '';

        if (notifications.length === 0) {
            notificationsList.innerHTML =
                '<p class="text-gray-500 text-center py-8">You have no notifications.</p>';
            return;
        }

        notifications.forEach(notification => {
            const isRead = notification.read_at !== null;
            const cardClasses = isRead
                ? 'bg-gray-800 opacity-60'
                : 'bg-gray-700 hover:bg-gray-600 cursor-pointer';

            const div = document.createElement('div');
            div.className = `p-4 rounded-lg shadow-md flex items-center space-x-4 transition ${cardClasses}`;
            div.innerHTML = `
                <div class="flex-1">
                    <p class="text-white font-semibold">${notification.title}</p>
                    <p class="text-gray-400 text-sm">${notification.message}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        ${new Date(notification.created_at).toLocaleString()}
                    </p>
                </div>
                ${!isRead ? '<span class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></span>' : ''}
            `;
            notificationsList.appendChild(div);
        });

    } catch (error) {
        loadingMessage.textContent = 'Could not load notifications (unauthorized).';
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
