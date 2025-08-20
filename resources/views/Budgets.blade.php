<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Budgets</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col items-center p-6">

    <!-- Navbar -->
  <nav class="bg-gray-800 p-3 sm:p-4 flex justify-around lg:flex-col lg:justify-start lg:w-64 lg:min-h-screen">
    <h1 class="text-lg sm:text-xl font-bold mb-4 hidden lg:block">Mint</h1>
    <div class="flex space-x-2 sm:space-x-4 lg:space-x-0 lg:flex-col lg:space-y-3 w-full">
      <a href="{{ route('dashboard') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Dashboard</a>
      <a href="{{ route('accounts') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Accounts</a>
      <a href="{{ route('bills') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Bills</a>
      <a href="{{ route('budgets') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg bg-gray-700 text-center lg:text-left">Budgets</a>
      <a href="{{ route('categories') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Categories</a>
      <a href="{{ route('transactions') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Transactions</a>
      <a href="{{ route('goals') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Goals</a>
      <a href="#" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Notifications</a>
      <a href="{{ route('profile') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Profile</a>
    </div>
    <button id="logoutBtn" class="mt-4 bg-red-600 hover:bg-red-700 w-full px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <!-- Budgets Form -->
  <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md min-h-[350px] flex flex-col">
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
  <!-- Submit -->
  <button type="submit"
    class="w-full bg-purple-600 hover:bg-purple-700 py-3 rounded-lg text-white font-medium">
    Save Budget
  </button>
  <!-- Back to Dashboard -->
  <p class="text-sm text-gray-400 mt-6 text-center">
    <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
  </p>

</form>

  </div>

  <!-- Budgets List -->
  <div class="w-full max-w-4xl mt-10 bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-semibold text-center mb-6">Budgets List</h2>

    <div id="budgetsList"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-h-96 overflow-y-auto p-2">
      <!-- Budget cards will be added here -->
    </div>
  </div>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
      window.location.href = "/login";
    }

    const budgetsList = document.getElementById("budgetsList");

    // ✅ Render single budget card
    function renderBudget(budget) {
      budgetsList.innerHTML += `
        <div class="bg-gray-700 p-5 rounded-xl shadow-md w-full h-40 flex flex-col justify-between">
          <div>
            <h3 class="text-lg font-semibold">${budget.category?.name ?? 'Unknown Category'}</h3>
            <p class="mt-2 text-gray-300">Amount: ₹${budget.amount}</p>
          </div>
        </div>
      `;
    }

    // ✅ Load all budgets on page load
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
          budgetsList.innerHTML = ""; // clear old list
          data.forEach(budget => renderBudget(budget));
        } else {
          alert("Failed to load budgets: " + JSON.stringify(data));
        }
      } catch (error) {
        console.error("Error loading budgets", error);
      }
    }

    // ✅ Handle form submission (Add new budget)
    document.getElementById('budgetsForm').addEventListener('submit', async function (event) {
  event.preventDefault();

  // Reset errors
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

  if (!valid) return; // stop if frontend validation fails

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
  } else {
    // Backend validation errors inline
    if (data.errors) {
      if (data.errors.category_id) {
        document.getElementById("categoryError").innerText = data.errors.category_id[0];
        document.getElementById("categoryError").classList.remove("hidden");
      }
      if (data.errors.amount) {
        document.getElementById("amountError").innerText = data.errors.amount[0];
        document.getElementById("amountError").classList.remove("hidden");
      }
    }
  }
});

    // Load budgets on page load
    loadBudgets();
  </script>

</body>
</html>