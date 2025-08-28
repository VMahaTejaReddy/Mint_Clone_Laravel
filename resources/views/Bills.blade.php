
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
  <main class="flex-1 ml-0 sm:ml-64 p-6 flex flex-col items-center space-y-10">
    <button id="addBillBtn" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg font-semibold">+ Add Bill</button>
    <!-- Bills Form -->
    <div id="addBillForm" class="hidden bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-2xl mx-auto">
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

    {{-- Edit Modal --}}
    <div id="editBillModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
      <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Edit Bill</h2>
        <form id="editBillForm" class="space-y-4">
          @csrf
          <input type="hidden" id="editBillId">
          <div>
            <input type="text" id="editName" name="name" placeholder="Bill Name"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
            <p class="text-red-400 text-sm mt-1 hidden" id="editNameError">Bill name is required.</p>
          </div>
          <div>
            <input type="number" id="editAmount" name="amount" placeholder="Amount"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
            <p class="text-red-400 text-sm mt-1 hidden" id="editAmountError">Amount must be a valid number.</p>
          </div>
          <div>
            <input type="date" id="editDueDate" name="due_date"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
            <p class="text-red-400 text-sm mt-1 hidden" id="editDueDateError">Due date is required.</p>
          </div>
          <button type="submit"
            class="w-full bg-green-600 hover:bg-green-700 py-3 rounded-lg text-white font-medium">Update Bill</button>
        </form>
        <button id="closeEditModal" onclick="closeEditModal()" class="mt-4 text-gray-400 hover:underline">Cancel</button>
      </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteBillModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
      <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Delete Bill</h2>
        <p class="mb-6">Are you sure you want to delete this bill?</p>
        <div class="flex space-x-4">
          <button id="confirmDeleteBill" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Delete</button>
          <button id="cancelDeleteBill" onclick="closeDeleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Cancel</button>
        </div>
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

  // Toggle Add Bill Form
  let addBillBtn = document.getElementById("addBillBtn");
  let addBillForm = document.getElementById("addBillForm");
  addBillBtn.addEventListener("click", function() {
    addBillForm.classList.toggle("hidden");
    if(addBillBtn.textContent==="+ Add Bill"){
        addBillBtn.textContent="X Close Form";
      } else {
        addBillBtn.textContent="+ Add Bill";
      }
  });
    
  const upcomingBills = document.getElementById("upcomingBills");
  const overdueBills = document.getElementById("overdueBills");
  const editBillModal = document.getElementById("editBillModal");
  const deleteBillModal = document.getElementById("deleteBillModal");
  const confirmDeleteBillBtn = document.getElementById("confirmDeleteBill");
  let billToEdit = null;

  // Function to render a single bill card
  function renderBill(bill, isOverdue = false) {
  const container = isOverdue ? overdueBills : upcomingBills;

  container.innerHTML += `
    <div id="bill-${bill.id}" 
         class="p-4 rounded-lg shadow-md w-full h-[150px] flex flex-col justify-between
         ${isOverdue ? 'bg-red-700/30 border border-red-500' : 'bg-green-900 border border-green-500'}">
         
      <h3 class="text-lg font-semibold truncate">${bill.name}</h3>
      <p>Amount: <span class="font-medium">₹${bill.amount}</span></p>
      <p>Due Date: ${bill.due_date}</p>
      <div class="flex space-x-4 mt-2">
        <button class="bg-blue-500 text-white p-1 hover:bg-blue-600 rounded-md" 
                onclick="openEditModal(${bill.id})">Edit</button>
        <button class="bg-red-500 text-white p-1 hover:bg-red-600 rounded-md" 
                onclick="openDeleteModal(${bill.id})">Delete</button>
      </div>
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

// --- Edit Bill ---

document.getElementById('editBillModal').addEventListener('submit', async function (event) {
  event.preventDefault();
  const billId = document.getElementById('editBillId').value;
  const name = document.getElementById('editName').value.trim();
  const amount = document.getElementById('editAmount').value.trim();
  const due_date = document.getElementById('editDueDate').value;

  // Reset errors
  document.getElementById("editNameError").classList.add("hidden");
  document.getElementById("editAmountError").classList.add("hidden");
  document.getElementById("editDueDateError").classList.add("hidden");

  let valid = true;

  if (!name) {
    document.getElementById("editNameError").classList.remove("hidden");
    valid = false;
  }

  if (!amount || isNaN(amount)) {
    document.getElementById("editAmountError").classList.remove("hidden");
    valid = false;
  }

  if (!due_date) {
    document.getElementById("editDueDateError").classList.remove("hidden");
    valid = false;
  }

  if (!valid) return; // stop if validation fails

  let billData = { name, amount, due_date };

  let res = await fetch(`/api/bills/${billId}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify(billData)
  });

  if (res.ok) {
    closeEditModal();
    loadBills();
  } else{
    alert('Failed to update bill');
  }
});

// Open Edit Modal

async function openEditModal(billId) {
  const res = await fetch(`/api/bills/${billId}`, {headers: { 'Authorization': 'Bearer ' + token }});
  if (res.ok) {
    const bill = await res.json();
    document.getElementById('editBillId').value = bill.id;
    document.getElementById('editName').value = bill.name;
    document.getElementById('editAmount').value = bill.amount;
    document.getElementById('editDueDate').value = bill.due_date;

    editBillModal.classList.remove("hidden");
  } else {
    alert('Failed to load bill');
  }
}

// open Delete Modal
function openDeleteModal(billId) {
  billToDelete = billId;
  deleteBillModal.classList.remove("hidden");
}

// Close Edit Modal
function closeEditModal() {
  editBillModal.classList.add("hidden");
}

// Close Delete Modal
function closeDeleteModal() {
  billToDelete = null;
  deleteBillModal.classList.add("hidden");
}

// --- DELETE ---

confirmDeleteBillBtn.addEventListener('click', async function () {
  let res = await fetch(`/api/bills/${billToDelete}`, {
    method: 'DELETE',
    headers: { 'Authorization': 'Bearer ' + token }
  });

  if (res.ok) {
    closeDeleteModal();
    loadBills(); // reload list instead of removing manually
  } else {
    alert('Failed to delete bill');
    closeDeleteModal();
  }
});


  // Call loadBills() when page loads
  window.onload = loadBills;
</script>


</body>

</html>