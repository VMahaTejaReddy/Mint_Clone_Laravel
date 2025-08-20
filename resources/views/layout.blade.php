<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mint Clone')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 flex">

    <!-- Fixed Sidebar Navigation -->
    <nav class="bg-gray-800 p-4 flex flex-col justify-between w-64 min-h-screen fixed">
        <div>
            <h1 class="text-xl font-bold mb-6 text-white">Mint Clone</h1>
            <div class="space-y-3">
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Dashboard</a>
                <a href="{{ route('accounts.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Accounts</a>
                <a href="{{ route('bills.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Bills</a>
                <a href="{{ route('budgets') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Budgets</a>
                <a href="{{ route('categories') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Categories</a>
                <a href="{{ route('transactions') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Transactions</a>
                <a href="{{ route('goals') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Goals</a>
                <a href="{{ route('profile') }}" class="block px-4 py-2 rounded-lg hover:bg-gray-700">Profile</a>
            </div>
        </div>
        <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 w-full px-4 py-2 rounded-lg font-semibold">Logout</button>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 p-6 ml-64">
        @yield('content')
    </main>

    <script>
        const token = localStorage.getItem("jwt_token");
        if (!token) {
            window.location.href = "/login";
        }
        document.getElementById("logoutBtn").addEventListener("click", function() {
            localStorage.removeItem("jwt_token");
            window.location.href = "/login";
        });
    </script>
</body>
</html>