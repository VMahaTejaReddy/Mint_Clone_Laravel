<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col lg:flex-row">

  <!-- Navbar -->
  <nav class="bg-gray-800 p-3 sm:p-4 flex justify-around lg:flex-col lg:justify-start lg:w-64 lg:min-h-screen">
    <!-- Logo (only desktop shows big logo) -->
    <h1 class="text-lg sm:text-xl font-bold mb-4 hidden lg:block">Dashboard</h1>

    <!-- Links -->
    <div class="flex space-x-2 sm:space-x-4 lg:space-x-0 lg:flex-col lg:space-y-3 w-full">
      <a href="{{ route('accounts') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left text-sm sm:text-base">Accounts</a>
      <a href="{{ route('bills') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left text-sm sm:text-base">Bills</a>
      <a href="{{ route('budgets') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left text-sm sm:text-base">Budgets</a>
      <a href="{{ route('categories') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left text-sm sm:text-base">Categories</a>
      <a href="{{ route('transactions') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left text-sm sm:text-base">Transactions</a>
      <a href="{{ route('goals') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left text-sm sm:text-base">Goals</a>
      <a href="#" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left text-sm sm:text-base">Notifications</a>
      <a href="#" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left text-sm sm:text-base">Profile</a>
    </div>

    <!-- Logout Button -->
    <button id="logoutBtn" class="mt-2 sm:mt-4 lg:mt-auto bg-red-600 hover:bg-red-700 w-full px-2 py-1 sm:px-4 sm:py-2 rounded-lg font-semibold text-sm sm:text-base">
      Logout
    </button>
  </nav>

  <!-- Main Content -->
  <main class="flex-1 p-4 sm:p-6">
    <h2 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">Welcome!</h2>
    <div class="bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-lg">
      <p class="text-base sm:text-lg text-gray-300">
        This is your dashboard where you can manage your accounts, bills, budgets, transactions, goals, and more.
      </p>
    </div>
  </main>
  <script>
        // Check if user is authenticated
        const token = localStorage.getItem("jwt_token");
        if (!token) {
            window.location.href = "/login";
        }

        // Logout button logic
        document.getElementById("logoutBtn").addEventListener("click", async function() {
            try {
                let res = await fetch("/api/logout", {
                    method: "POST",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Accept": "application/json"
                    }
                });

                // Clear local storage no matter what
                localStorage.removeItem("jwt_token");

                if (res.ok) {
                    alert("Logged out successfully!");
                } else {
                    alert("Session ended, please login again.");
                }

                // Redirect to login
                window.location.href = "/login";

            } catch (error) {
                console.error("Logout error:", error);
                localStorage.removeItem("jwt_token");
                window.location.href = "/login";
            }
        });
    </script>

</body>
</html>