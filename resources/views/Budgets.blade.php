<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Budgets</title>
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
      <a href="{{ route('budgets') }}" class="px-3 py-2 rounded-lg bg-gray-700">Budgets</a>
      <a href="{{ route('categories') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Categories</a>
      <a href="{{ route('transactions') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Transactions</a>
      <a href="{{ route('goals') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Goals</a>
      <a href="#" class="px-3 py-2 rounded-lg hover:bg-gray-700">Notifications</a>
      <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Profile</a>
    </div>
    <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <!-- Main Content -->
  <main class="ml-64 flex-1 p-8 space-y-8">

    <!-- Budget Form -->
    <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-2xl mx-auto">
      <h2 class="text-2xl font-semibold text-center mb-6">Create Budget</h2>
      <form id="budgetsForm" class="space-y-4" method="POST" action="{{ route('budgets.store') }}">
        @csrf

        <!-- Category Dropdown -->
        <div>
          <select name="category_id" id="category_id"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-purple-500 outline-none">
            <option value="">-- Select Category --</option>
            @if(isset($categories) && !$categories->isEmpty())
            @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
            @endif
          </select>
          <p class="text-red-400 text-sm mt-1 hidden" id="categoryError">Please select a category.</p>
        </div>

        <!-- Budget Amount -->
        <div>
          <input type="number" name="amount" placeholder="Budget Amount"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-purple-500 outline-none">
          <p class="text-red-400 text-sm mt-1 hidden" id="amountError">Please enter a valid amount.</p>
        </div>

        <button type="submit"
          class="w-full bg-purple-600 hover:bg-purple-700 py-3 rounded-lg text-white font-medium">
          Save Budget
        </button>

        <p class="text-sm text-gray-400 mt-6 text-center">
          <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
        </p>
      </form>
    </div>

    <!-- Budgets List -->
    <div class="bg-gray-700/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-5xl mx-auto">
      <h2 class="text-2xl font-semibold text-center mb-6">Budgets List</h2>
      <div id="budgetsList"
        class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-h-[400px] overflow-y-auto p-2">
        <!-- Budget cards will be injected -->
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

    const budgetsList = document.getElementById("budgetsList");

    function renderBudget(budget) {
      budgetsList.innerHTML += `
        <div class="bg-gray-800 p-5 rounded-xl shadow-md w-full h-40 flex flex-col justify-between">
          <div>
            <h3 class="text-lg font-semibold">${budget.category?.name ?? 'Unknown Category'}</h3>
            <p class="mt-2 text-gray-300">Amount: ₹${budget.amount}</p>
          </div>
        </div>
      `;
    }

    async function loadBudgets() {
      try {
        let res = await fetch('/api/budgets', {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token
          }
        });

        let data = await res.json();

        if (res.ok) {
          budgetsList.innerHTML = "";
          data.forEach(budget => renderBudget(budget));
        } else {
          console.error("Failed to load budgets", data);
        }
      } catch (error) {
        console.error("Error loading budgets", error);
      }
    }

    document.getElementById('budgetsForm').addEventListener('submit', async function (event) {
      event.preventDefault();

      document.getElementById("categoryError").classList.add("hidden");
      document.getElementById("amountError").classList.add("hidden");

      let category_id = document.querySelector('select[name="category_id"]').value;
      let amount = document.querySelector('input[name="amount"]').value.trim();
      let valid = true;

      if (!category_id) {
        document.getElementById("categoryError").classList.remove("hidden");
        valid = false;
      }

      if (!amount || isNaN(amount) || Number(amount) <= 0) {
        document.getElementById("amountError").classList.remove("hidden");
        valid = false;
      }

      if (!valid) return;

      let budgetData = { category_id, amount };

      let res = await fetch('/api/budgets', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify(budgetData)
      });

      let data = await res.json();

      if (res.ok) {
        renderBudget(data);
        document.getElementById('budgetsForm').reset();
      } else if (data.errors) {
        if (data.errors.category_id) {
          document.getElementById("categoryError").innerText = data.errors.category_id[0];
          document.getElementById("categoryError").classList.remove("hidden");
        }
        if (data.errors.amount) {
          document.getElementById("amountError").innerText = data.errors.amount[0];
          document.getElementById("amountError").classList.remove("hidden");
        }
      }
    });

    loadBudgets();
  </script>
</body>

</html>