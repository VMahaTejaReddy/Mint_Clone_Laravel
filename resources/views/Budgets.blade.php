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
      <a href="{{ route('notifications') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Notifications</a>
      <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Profile</a>
    </div>
    <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <!-- Main Content -->
  <main class="flex-1 ml-0 sm:ml-64 p-6 flex flex-col items-center space-y-10">

    <button id="addBudgetBtn" class="bg-purple-600 hover:bg-purple-700 py-3 p-5 rounded-lg text-white font-medium">Add Budget</button>

    <!-- Budget Form -->
    <div id="AddBudgetForm" class="hidden bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-2xl mx-auto">
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

    {{-- Edit Modal --}}
    <div id="EditBudgetModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Edit Budget</h2>
        <form id="editBudgetForm" class="space-y-4">
          @csrf
          <input type="hidden" name="id" id="editBudgetId">
          <div>
            <select name="category_id" id="editCategoryId"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-purple-500 outline-none">
              <option value="">-- Select Category --</option>
              @if(isset($categories) && !$categories->isEmpty())
              @foreach($categories as $category)
              <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
              @endif
            </select>
            <p class="text-red-400 text-sm mt-1 hidden" id="editCategoryError">Please select a category.</p>
          </div>

          <div>
            <input type="number" name="amount" id="editAmount"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-purple-500 outline-none">
            <p class="text-red-400 text-sm mt-1 hidden" id="editAmountError">Please enter a valid amount.</p>
          </div>

          <button type="submit"
            class="w-full bg-purple-600 hover:bg-purple-700 py-3 rounded-lg text-white font-medium">
            Save Changes
          </button>
        </form>
        <button id="closeEditModal" class="mt-4 text-gray-400 hover:underline">Cancel</button>
      </div>
    </div>

    {{-- Delete Modal --}}
    <div id="DeleteBudgetModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Delete Budget</h2>
        <p class="mb-4">Are you sure you want to delete this budget?</p>
        <div class="flex justify-end">
          <button id="confirmDeleteBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Delete</button>
          <button id="closeDeleteModal" class="ml-2 text-gray-400 hover:underline">Cancel</button>
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
    document.getElementById("logoutBtn").addEventListener("click", function () {
      localStorage.removeItem("jwt_token");
      window.location.href = "/login";
    });

    // Form toggle
    let addBudgetBtn = document.getElementById("addBudgetBtn");
    let addBudgetForm = document.getElementById("AddBudgetForm");
    addBudgetBtn.addEventListener("click", function () {
      addBudgetForm.classList.toggle("hidden");
      addBudgetBtn.textContent = addBudgetBtn.textContent === "Add Budget" ? "Close Form" : "Add Budget";
    });

    const budgetsList = document.getElementById("budgetsList");
    const editBudgetModal = document.getElementById("EditBudgetModal");
    const deleteBudgetModal = document.getElementById("DeleteBudgetModal");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    const closeEditModalBtn = document.getElementById("closeEditModal");
    const closeDeleteModalBtn = document.getElementById("closeDeleteModal");

    let budgetToDelete = null;

    function renderBudget(budget) {
      budgetsList.innerHTML += `
        <div class="bg-gray-800 p-5 rounded-xl shadow-md w-full h-40 flex flex-col justify-between">
          <div>
            <h3 class="text-lg font-semibold">${budget.category?.name ?? 'Unknown Category'}</h3>
            <p class="mt-2 text-gray-300">Amount: ₹${budget.amount}</p>
          </div>
          <div class="flex justify-end space-x-3">
            <button onclick="openEditModal(${budget.id})" class="bg-yellow-500 hover:bg-yellow-600 p-1 rounded-md">Edit</button>
            <button onclick="openDeleteModal(${budget.id})" class="bg-red-600 hover:bg-red-700 p-1 rounded-md">Delete</button>
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

    // Create Budget
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

      let res = await fetch('/api/budgets', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({ category_id, amount })
      });
      let data = await res.json();
      if (res.ok) {
        renderBudget(data);
        document.getElementById('budgetsForm').reset();
      } else {
        console.error("Create failed", data);
      }
    });

    // --- Edit Budget ---
    document.getElementById("editBudgetForm").addEventListener("submit", async function (event) {
      event.preventDefault();
      const budgetId = document.getElementById("editBudgetId").value;
      const category_id = document.getElementById("editCategoryId").value;
      const amount = document.getElementById("editAmount").value;

      let valid = true;
      if(!category_id) {
        document.getElementById("editCategoryError").classList.remove("hidden");
        valid = false;
      } else {
        document.getElementById("editCategoryError").classList.add("hidden");
      }
      if(!amount || isNaN(amount) || Number(amount) <= 0) {
        document.getElementById("editAmountError").classList.remove("hidden");
        valid = false;
      } else {
        document.getElementById("editAmountError").classList.add("hidden");
      }

      let res = await fetch(`/api/budgets/${budgetId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({ category_id, amount })
      });
      let data = await res.json();
      if (res.ok) {
        loadBudgets();
        editBudgetModal.classList.add("hidden");
      } else {
        console.error("Edit failed", data);
      }
    });

    async function openEditModal(id) {
      const res = await fetch(`/api/budgets/${id}`, { headers: { 'Authorization': 'Bearer ' + token } });
      if (res.ok) {
        let data = await res.json();
        document.getElementById("editBudgetId").value = data.id;
        document.getElementById("editCategoryId").value = data.category_id;
        document.getElementById("editAmount").value = data.amount;
        editBudgetModal.classList.remove("hidden");
      } else {
        alert("Failed to open edit modal");
      }
    }

    // Delete Budget
    function openDeleteModal(id) {
      budgetToDelete = id;
      deleteBudgetModal.classList.remove("hidden");
    }

    confirmDeleteBtn.addEventListener('click', async function () {
      if (!budgetToDelete) return;
      let res = await fetch(`/api/budgets/${budgetToDelete}`, {
        method: 'DELETE',
        headers: {
          'Authorization': 'Bearer ' + token
        }
      });
      if (res.ok) {
        budgetToDelete = null;
        deleteBudgetModal.classList.add("hidden");
        loadBudgets();
      } else {
        alert('Failed to delete budget');
      }
    });

    // Close modals
    closeEditModalBtn.addEventListener("click", () => editBudgetModal.classList.add("hidden"));
    closeDeleteModalBtn.addEventListener("click", () => deleteBudgetModal.classList.add("hidden"));

    // Load budgets initially
    loadBudgets();
  </script>
</body>
</html>
