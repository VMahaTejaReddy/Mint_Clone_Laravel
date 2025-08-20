
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bills</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col items-center p-6">

  <!-- Bills Form -->
  <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md h-[350px] flex flex-col justify-between">
    <h2 class="text-2xl font-semibold text-center mb-2">Add Bill</h2>
    <form id="billsForm" class="space-y-3 flex-1 flex flex-col justify-center" method="POST" action="{{ route('bills.store') }}">
      @csrf
      <input type="text" name="name" placeholder="Bill Name (e.g., Electricity)"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
      <input type="number" name="amount" placeholder="Amount"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
      <input type="date" name="due_date"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
        <!-- Back to Dashboard -->
    <p class="text-sm text-gray-400 mt-6 text-center">
      <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
    </p>
      <button type="submit"
        class="w-full bg-green-600 hover:bg-green-700 py-3 rounded-lg text-white font-medium">Save Bill</button>
    </form>
  </div>

  <!-- Bills List -->
  <div class="w-full max-w-4xl mt-10 bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-semibold text-center mb-6">Bills List</h2>
    <div id="billsList"
      class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[400px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
      <!-- Bills will be dynamically loaded here -->
    </div>
  </div>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
      window.location.href = "/login";
    }

    const billsList = document.getElementById("billsList");

    // Function to render a single bill card
    function renderBill(bill) {
      billsList.innerHTML += `
        <div class="bg-gray-700 p-4 rounded-lg shadow-md w-full h-[150px] flex flex-col justify-between">
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
          billsList.innerHTML = ""; // clear before adding
          data.forEach(bill => renderBill(bill));
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

      let billData = {
        name: document.querySelector('input[name="name"]').value,
        amount: document.querySelector('input[name="amount"]').value,
        due_date: document.querySelector('input[name="due_date"]').value
      };

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
        renderBill(data);
        document.getElementById('billsForm').reset();
      } else {
        alert('Bill creation failed: ' + JSON.stringify(data));
      }
    });

    // Call loadBills() when page loads
    window.onload = loadBills;
  </script>

</body>

</html>
