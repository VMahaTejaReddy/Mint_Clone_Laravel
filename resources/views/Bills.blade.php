
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bills</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex">

  <!-- Navbar -->
  <nav class="bg-gray-800 fixed left-0 top-0 h-full w-64 p-6 flex flex-col">
    <h1 class="text-2xl font-bold mb-8">Mint</h1>
    <div class="flex flex-col space-y-3 flex-grow">
      <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Dashboard</a>
      <a href="{{ route('accounts') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Accounts</a>
      <a href="{{ route('bills') }}" class="px-3 py-2 rounded-lg bg-gray-700">Bills</a>
      <a href="{{ route('budgets') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Budgets</a>
      <a href="{{ route('categories') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Categories</a>
      <a href="{{ route('transactions') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Transactions</a>
      <a href="{{ route('goals') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Goals</a>
      <a href="#" class="px-3 py-2 rounded-lg hover:bg-gray-700">Notifications</a>
      <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Profile</a>
    </div>
    <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <!-- Main Content (stacked layout) -->
  <main class="ml-64 flex-1 p-8 space-y-8">

    <!-- Bills Form -->
    <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-2xl mx-auto">
      <h2 class="text-2xl font-semibold text-center mb-4">Add Bill</h2>
      <form id="billsForm" class="space-y-4 flex flex-col" method="POST" action="{{ route('bills.store') }}">
        @csrf
        <div>
          <input type="text" name="name" placeholder="Bill Name (e.g., Electricity)"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-400 text-sm mt-1 hidden" id="nameError">Bill name is required.</p>
        </div>

        <div>
          <input type="number" name="amount" placeholder="Amount"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-400 text-sm mt-1 hidden" id="amountError">Amount must be a valid number.</p>
        </div>

        <div>
          <input type="date" name="due_date"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-400 text-sm mt-1 hidden" id="dueDateError">Due date is required.</p>
        </div>

        <button type="submit"
          class="w-full bg-green-600 hover:bg-green-700 py-3 rounded-lg text-white font-medium">Save Bill</button>

        <p class="text-sm text-gray-400 mt-6 text-center">
          <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
        </p>
      </form>
    </div>

    <!-- Bills List -->
    <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-5xl mx-auto">
      <h2 class="text-2xl font-semibold text-center mb-6">Bills List</h2>

      <!-- Upcoming Bills -->
      <div>
        <h3 class="text-xl font-semibold text-green-400 mb-3">Upcoming Bills</h3>
        <div id="upcomingBills"
          class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[250px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
        </div>
      </div>

      <!-- Overdue Bills -->
      <div class="mt-8">
        <h3 class="text-xl font-semibold text-red-400 mb-3">Overdue Bills</h3>
        <div id="overdueBills"
          class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[250px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
        </div>
      </div>
    </div>
  </main>
<script>
  const token = localStorage.getItem("jwt_token");
  if (!token) {
    window.location.href = "/login";
  }

  const upcomingBills = document.getElementById("upcomingBills");
  const overdueBills = document.getElementById("overdueBills");

  // Function to render a single bill card
  function renderBill(bill, isOverdue = false) {
    const container = isOverdue ? overdueBills : upcomingBills;

    container.innerHTML += `
      <div class="p-4 rounded-lg shadow-md w-full h-[150px] flex flex-col justify-between
        ${isOverdue ? 'bg-red-700/30 border border-red-500' : 'bg-green-900 border border-green-500'}">
        <h3 class="text-lg font-semibold truncate">${bill.name}</h3>
        <p>Amount: <span class="font-medium">₹${bill.amount}</span></p>
        <p>Due Date: ${bill.due_date}</p>
      </div>
    `;
  }

  // ✅ Load all bills on page load
  async function loadBills() {
    try {
      let res = await fetch('/api/bills', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token
        }
      });

      let data = await res.json();

      if (res.ok) {
        upcomingBills.innerHTML = "";
        overdueBills.innerHTML = "";

        let today = new Date().toISOString().split("T")[0];

        data.forEach(bill => {
          if (bill.due_date < today) {
            renderBill(bill, true); // Overdue
          } else {
            renderBill(bill, false); // Upcoming
          }
        });
      } else {
        alert("Failed to load bills: " + JSON.stringify(data));
      }
    } catch (error) {
      console.error("Error loading bills", error);
    }
  }

  // ✅ Handle form submission (Add new bill)
  document.getElementById('billsForm').addEventListener('submit', async function (event) {
  event.preventDefault();

  // Reset errors
  document.getElementById("nameError").classList.add("hidden");
  document.getElementById("amountError").classList.add("hidden");
  document.getElementById("dueDateError").classList.add("hidden");

  let name = document.querySelector('input[name="name"]').value.trim();
  let amount = document.querySelector('input[name="amount"]').value.trim();
  let due_date = document.querySelector('input[name="due_date"]').value;

  let valid = true;

  if (!name) {
    document.getElementById("nameError").classList.remove("hidden");
    valid = false;
  }

  if (!amount || isNaN(amount)) {
    document.getElementById("amountError").classList.remove("hidden");
    valid = false;
  }

  if (!due_date) {
    document.getElementById("dueDateError").classList.remove("hidden");
    valid = false;
  }

  if (!valid) return; // stop if validation fails

  let billData = { name, amount, due_date };

  let res = await fetch('/api/bills', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify(billData)
  });

  let data = await res.json();

  if (res.ok) {
    let today = new Date().toISOString().split("T")[0];
    renderBill(data, data.due_date < today);
    document.getElementById('billsForm').reset();
  } else {
    // Show backend validation errors inline
    if (data.errors) {
      if (data.errors.name) {
        document.getElementById("nameError").innerText = data.errors.name[0];
        document.getElementById("nameError").classList.remove("hidden");
      }
      if (data.errors.amount) {
        document.getElementById("amountError").innerText = data.errors.amount[0];
        document.getElementById("amountError").classList.remove("hidden");
      }
      if (data.errors.due_date) {
        document.getElementById("dueDateError").innerText = data.errors.due_date[0];
        document.getElementById("dueDateError").classList.remove("hidden");
      }
    }
  }
});


  // Call loadBills() when page loads
  window.onload = loadBills;
</script>


</body>

</html>
