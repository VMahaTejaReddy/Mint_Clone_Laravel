<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transactions</title>
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
      <a href="{{ route('budgets') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Budgets</a>
      <a href="{{ route('categories') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Categories</a>
      <a href="{{ route('transactions') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg bg-gray-700 text-center lg:text-left">Transactions</a>
      <a href="{{ route('goals') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Goals</a>
      <a href="#" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Notifications</a>
      <a href="{{ route('profile') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Profile</a>
    </div>
    <button id="logoutBtn" class="mt-4 bg-red-600 hover:bg-red-700 w-full px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <!-- Transactions Form -->
  <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md min-h-[350px] flex flex-col">
    <h2 class="text-2xl font-semibold text-center mb-6">Add Transaction</h2>
    <form id="transactionsForm" class="space-y-4" method="POST" action="{{ route('transactions.store') }}">
  @csrf

  <div>
    <input name="description" type="text" placeholder="Description (e.g., Starbucks)"
      class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
    <p class="text-red-400 text-sm mt-1 hidden" id="error-description"></p>
  </div>

  <div>
    <input name="amount" type="number" placeholder="Amount"
      class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
    <p class="text-red-400 text-sm mt-1 hidden" id="error-amount"></p>
  </div>

  <div>
    <input name="date" type="date"
      class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
    <p class="text-red-400 text-sm mt-1 hidden" id="error-date"></p>
  </div>

  <div>
    <select name="category_id"
      class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
      <option value="">-- Select Category --</option>
      @foreach($categories as $category)
      <option value="{{ $category->id }}">{{ $category->name }}</option>
      @endforeach
    </select>
    <p class="text-red-400 text-sm mt-1 hidden" id="error-category_id"></p>
  </div>

  <div>
    <select name="account_id" id="account_id_select"
      class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
      <option value="">-- Select Account --</option>
    </select>
    <p class="text-red-400 text-sm mt-1 hidden" id="error-account_id"></p>
  </div>

  <button type="submit"
    class="w-full bg-orange-600 hover:bg-orange-700 py-3 rounded-lg text-white font-medium">Save
    Transaction</button>

  <p class="text-sm text-gray-400 mt-4 text-center">
    <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
  </p>
</form>

  </div>

  <!-- Transactions List -->
  <div class="w-full max-w-5xl mt-10 bg-gray-700/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-semibold text-center mb-6">Transactions List</h2>
    <div id="transactionsList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Transactions will be dynamically loaded here -->
    </div>
  </div>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
      window.location.href = "/login";
    }

    // Load Transactions List
    async function loadTransactions() {
      try {
          let res = await fetch('/api/transactions', {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'Authorization': 'Bearer ' + token
            }
          });

          let transactions = await res.json();

          if (res.ok) {
            let list = document.getElementById('transactionsList');
            list.innerHTML = ""; // Clear before populating

            transactions.forEach(data => {
              list.innerHTML += `
              <div class="bg-gray-800 p-5 rounded-xl shadow-md w-full">
                <h3 class="text-lg font-semibold">${data.description}</h3>
                <p class="text-gray-300">Amount: ₹${data.amount}</p>
                <p class="text-gray-300">Date: ${data.date}</p>
                <p class="text-gray-400 text-sm">Category: ${data.category ? data.category.name : "N/A"}</p>
                <p class="text-gray-400 text-sm">Account: ${data.account ? data.account.name : "N/A"}</p>
                <p class="text-gray-400 text-sm">Account Type: ${data.account.type ? data.account.type : "N/A"}</p>
              </div>
            `;
            });
          } else {
            console.error("Transactions fetch failed:", transactions);
          }
      } catch(error) {
          console.error("Error loading transactions:", error);
      }
    }

    // Load Accounts
    async function loadAccounts() {
        try {
            let res = await fetch('/api/accounts', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });
            let accounts = await res.json();
            if (res.ok) {
                const select = document.getElementById('account_id_select');
                select.innerHTML = '<option value="">-- Select Account --</option>';
                accounts.forEach(account => {
                    select.innerHTML += `<option value="${account.id}">${account.name}</option>`;
                });
            } else {
                console.error("Accounts fetch failed:", accounts);
            }
        } catch (error) {
            console.error("Error loading accounts", error);
        }
    }

    // Handle Transaction Form Submit
    // Handle Transaction Form Submit
document.getElementById('transactionsForm').addEventListener('submit', async function (event) {
  event.preventDefault();

  // Clear old errors
  document.querySelectorAll('[id^="error-"]').forEach(el => {
    el.textContent = "";
    el.classList.add("hidden");
  });

  let transactionData = {
    account_id: document.querySelector('select[name="account_id"]').value,
    category_id: document.querySelector('select[name="category_id"]').value,
    description: document.querySelector('input[name="description"]').value,
    amount: document.querySelector('input[name="amount"]').value,
    date: document.querySelector('input[name="date"]').value
  };

  let res = await fetch('/api/transactions', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify(transactionData)
  });

  let data = await res.json();

  if (res.ok) {
    document.getElementById('transactionsForm').reset();
    loadTransactions();
  } else if (data.errors) {
    // Show inline validation errors
    for (let field in data.errors) {
      let errorEl = document.getElementById(`error-${field}`);
      if (errorEl) {
        errorEl.textContent = data.errors[field][0];
        errorEl.classList.remove("hidden");
      }
    }
  }
});


    // Initialize page
    window.onload = () => {
        loadAccounts();
        loadTransactions();
    };
</script>


</body>

</html>
