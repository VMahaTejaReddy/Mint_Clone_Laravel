<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transactions</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col items-center p-6">

  <!-- Transactions Form -->
  <div class="w-full max-w-lg bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-semibold text-center mb-6">Add Transaction</h2>
    <form id="transactionsForm" class="space-y-4" method="POST" action="{{ route('transactions.store') }}">
      @csrf
      <input name="description" type="text" placeholder="Description (e.g., Starbucks)"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">

      <input name="amount" type="number" placeholder="Amount"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">

      <input name="date" type="date"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">

      <select name="category_id"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
        <option value="">-- Select Category --</option>
        @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
      </select>
      <select name="account_id"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
        <option value="">-- Select Account --</option>
        @foreach($accounts as $account)
        <option value="{{ $account->id }}">{{ $account->name }}</option>
        @endforeach
      </select>

      <!-- Back to Dashboard -->
      <p class="text-sm text-gray-400 mt-4 text-center">
        <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
      </p>

      <button type="submit"
        class="w-full bg-orange-600 hover:bg-orange-700 py-3 rounded-lg text-white font-medium">Save
        Transaction</button>
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

    // Handle Transaction Form Submit
    document.getElementById('transactionsForm').addEventListener('submit', async function (event) {
      event.preventDefault();

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
        alert("Transaction added successfully!");

        // Append new transaction dynamically
        document.getElementById('transactionsList').innerHTML += `
          <div class="bg-gray-800 p-5 rounded-xl shadow-md w-full">
            <h3 class="text-lg font-semibold">${data.description}</h3>
            <p class="text-gray-300">Amount: ₹${data.amount}</p>
            <p class="text-gray-300">Date: ${data.date}</p>
            <p class="text-gray-400 text-sm">Category: ${data.category ? data.category.name : "N/A"}</p>
            <p class="text-gray-400 text-sm">Account: ${data.account ? data.account.name : "N/A"}</p>
          </div>
        `;

        // Reset form
        document.getElementById('transactionsForm').reset();
      } else {
        alert('Transaction failed: ' + JSON.stringify(data));
      }
    });

    // Load Transactions List on Page Load
    async function loadTransactions() {
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
        list.innerHTML = "";

        transactions.forEach(data => {
  document.getElementById('transactionsList').innerHTML += `
  <div class="bg-gray-800 p-5 rounded-xl shadow-md w-full">
    <h3 class="text-lg font-semibold">${data.description}</h3>
    <p class="text-gray-300">Amount: ₹${data.amount}</p>
    <p class="text-gray-300">Date: ${data.date}</p>
    <p class="text-gray-400 text-sm">Category: ${data.category ? data.category.name : "N/A"}</p>
    <p class="text-gray-400 text-sm">Account: ${data.account ? data.account.name : "N/A"}</p>
  </div>
`;
});
      }
    }

    // Call loadTransactions when page loads
    window.onload = loadTransactions;
  </script>

</body>

</html>
