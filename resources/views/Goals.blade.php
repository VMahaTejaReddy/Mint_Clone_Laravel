<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Goals</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex justify-center items-center p-6">

    <!-- Navbar -->
  <nav class="bg-gray-800 p-3 sm:p-4 flex justify-around lg:flex-col lg:justify-start lg:w-64 lg:min-h-screen">
    <h1 class="text-lg sm:text-xl font-bold mb-4 hidden lg:block">Mint</h1>
    <div class="flex space-x-2 sm:space-x-4 lg:space-x-0 lg:flex-col lg:space-y-3 w-full">
      <a href="{{ route('dashboard') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Dashboard</a>
      <a href="{{ route('accounts') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Accounts</a>
      <a href="{{ route('bills') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Bills</a>
      <a href="{{ route('budgets') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Budgets</a>
      <a href="{{ route('categories') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Categories</a>
      <a href="{{ route('transactions') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Transactions</a>
      <a href="{{ route('goals') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg bg-gray-700 text-center lg:text-left">Goals</a>
      <a href="#" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Notifications</a>
      <a href="{{ route('profile') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Profile</a>
    </div>
    <button id="logoutBtn" class="mt-4 bg-red-600 hover:bg-red-700 w-full px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <div class="w-full max-w-5xl mx-auto flex flex-col items-center space-y-8">
    <!-- Goals Form -->
    <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md min-h-[350px] flex flex-col">
      <h2 class="text-xl font-semibold text-center mb-4">Add Goal</h2>
      
      <form id="goalsForm" class="space-y-4 flex flex-col" method="POST" action="{{ route('goals.store') }}">
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

        <!-- Back to Dashboard -->
        <p class="text-sm text-gray-400 text-center">
          <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
        </p>
      </form>
    </div>

    <!-- Goals List -->
    <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full">
      <h2 class="text-xl font-semibold text-center mb-4">Goals List</h2>
      <div id="goalsList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Dynamic goals will appear here -->
      </div>
    </div>
  </div>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
      window.location.href = "/login";
    }

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
