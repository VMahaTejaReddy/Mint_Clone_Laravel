<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Goals</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex justify-center items-start p-6">

  <div class="w-full max-w-5xl space-y-8">
    <!-- Goals Form -->
    <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full">
      <h2 class="text-xl font-semibold text-center mb-4">Add Goal</h2>
      <form id="goalsForm" class="space-y-4" method="POST" action="{{ route('goals.store') }}">
        @csrf
        <input id="name" type="text" name="name" placeholder="Goal Name"
          class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">

        <input id="target_amount" type="number" name="target_amount" placeholder="Target Amount"
          class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">

        <input id="current_amount" type="number" name="current_amount" placeholder="Current Amount"
          class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">

        <!-- Back to Dashboard -->
        <p class="text-sm text-gray-400 mt-6 text-center">
            <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
        </p>
        <button type="submit"
          class="w-full bg-green-600 hover:bg-green-700 py-3 rounded-lg text-white font-medium">Save Goal</button>
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

      let goalData = {
        name: document.querySelector('input[name="name"]').value,
        target_amount: document.querySelector('input[name="target_amount"]').value,
        current_amount: document.querySelector('input[name="current_amount"]').value
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
      } else {
        alert('Goal creation failed: ' + JSON.stringify(data));
      }
    });

    window.onload = loadGoals;
  </script>
</body>
</html>