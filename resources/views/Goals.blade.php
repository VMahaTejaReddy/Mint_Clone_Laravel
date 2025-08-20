<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Goals</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex">

  <!-- Navbar -->
  <nav class="bg-gray-800 fixed left-0 top-0 h-full w-64 p-6 flex flex-col">
    <h1 class="text-2xl font-bold mb-8">Mint</h1>
    <div class="flex flex-col space-y-3 flex-grow">
      <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Dashboard</a>
      <a href="{{ route('accounts') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Accounts</a>
      <a href="{{ route('bills') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Bills</a>
      <a href="{{ route('budgets') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Budgets</a>
      <a href="{{ route('categories') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Categories</a>
      <a href="{{ route('transactions') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Transactions</a>
      <a href="{{ route('goals') }}" class="px-3 py-2 rounded-lg bg-gray-700">Goals</a>
      <a href="#" class="px-3 py-2 rounded-lg hover:bg-gray-700">Notifications</a>
      <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Profile</a>
    </div>
    <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <!-- Main Content -->
  <main class="ml-64 flex-1 p-8 flex flex-col items-center space-y-8">

    <!-- Goals Form -->
    <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-xl">
      <h2 class="text-2xl font-semibold text-center mb-6">Add Goal</h2>
      <form id="goalsForm" class="space-y-4" method="POST" action="{{ route('goals.store') }}">
        @csrf
        <div>
          <input id="name" type="text" name="name" placeholder="Goal Name"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-500 text-sm mt-1 hidden" id="error-name"></p>
        </div>
        <div>
          <input id="target_amount" type="number" name="target_amount" placeholder="Target Amount"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-500 text-sm mt-1 hidden" id="error-target_amount"></p>
        </div>
        <div>
          <input id="current_amount" type="number" name="current_amount" placeholder="Current Amount"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-500 text-sm mt-1 hidden" id="error-current_amount"></p>
        </div>
        <div>
          <input id="due_date" type="date" name="due_date"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-500 text-sm mt-1 hidden" id="error-due_date"></p>
        </div>
        <button type="submit"
          class="w-full bg-green-600 hover:bg-green-700 py-3 rounded-lg text-white font-medium">Save Goal</button>
        <p class="text-sm text-gray-400 text-center">
          <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
        </p>
      </form>
    </div>

    <!-- Goals List -->
    <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-5xl">
      <h2 class="text-2xl font-semibold text-center mb-6">Goals List</h2>
      <div id="goalsList" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Dynamic goals will appear here -->
      </div>
    </div>
  </main>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
      window.location.href = "/login";
    }

    // Logout
    document.getElementById("logoutBtn").addEventListener("click", async function() {
      localStorage.removeItem("jwt_token");
      window.location.href = "/login";
    });

    // Load all goals
    async function loadGoals() {
      let res = await fetch('/api/goals', {
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token
        }
      });

      let data = await res.json();

      if (res.ok) {
        let goalsList = document.getElementById('goalsList');
        goalsList.innerHTML = "";
        data.forEach(goal => {
          goalsList.innerHTML += `
            <div class="bg-gray-700 p-5 rounded-xl shadow hover:shadow-lg transition">
              <h3 class="text-lg font-semibold mb-2">${goal.name}</h3>
              <p>🎯 Target: <span class="font-medium">₹${goal.target_amount}</span></p>
              <p>💰 Current: <span class="font-medium">₹${goal.current_amount}</span></p>
              <p>📅 Due Date: <span class="font-medium">${goal.due_date}</span></p>
            </div>
          `;
        });
      } else {
        alert('Failed to load goals: ' + JSON.stringify(data));
      }
    }

    // Handle form submit
    document.getElementById('goalsForm').addEventListener('submit', async function (event) {
      event.preventDefault();

      // clear old errors
      document.querySelectorAll('[id^="error-"]').forEach(el => {
        el.textContent = "";
        el.classList.add("hidden");
      });

      let goalData = {
        name: document.querySelector('input[name="name"]').value,
        target_amount: document.querySelector('input[name="target_amount"]').value,
        current_amount: document.querySelector('input[name="current_amount"]').value,
        due_date: document.querySelector('input[name="due_date"]').value
      };

      let res = await fetch('/api/goals', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify(goalData)
      });

      let data = await res.json();

      if (res.ok) {
        document.getElementById('goalsForm').reset();
        loadGoals();
      } else if (data.errors) {
        // show inline validation errors
        for (let field in data.errors) {
          let errorElement = document.getElementById(`error-${field}`);
          if (errorElement) {
            errorElement.textContent = data.errors[field][0];
            errorElement.classList.remove("hidden");
          }
        }
      }
    });

    window.onload = loadGoals;
  </script>
</body>
</html>
