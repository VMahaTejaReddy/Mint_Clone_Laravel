<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transactions</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex">
  <!-- Navbar -->
  <nav class="bg-gray-800 fixed left-0 top-0 h-full w-64 p-6 flex flex-col">
    <h1 class="text-2xl font-bold mb-8">Mint</h1>
    <div class="flex flex-col space-y-3 flex-grow">
      <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Dashboard</a>
      <a href="{{ route('accounts') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Accounts</a>
      <a href="{{ route('bills') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Bills</a>
      <a href="{{ route('budgets') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Budgets</a>
      <a href="{{ route('categories') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Categories</a>
      <a href="{{ route('transactions') }}" class="px-3 py-2 rounded-lg bg-gray-700">Transactions</a>
      <a href="{{ route('goals') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Goals</a>
      <a href="#" class="px-3 py-2 rounded-lg hover:bg-gray-700">Notifications</a>
      <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Profile</a>
    </div>
    <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <!-- Main Content -->
  <main class="flex-1 ml-0 sm:ml-64 p-6 flex flex-col items-center space-y-10">
    <button id="addTransactionBtn" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg font-semibold">Add Transaction</button>
    <!-- Transactions Form -->
    <div id="transactionsFormContainer" class="hidden bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-semibold text-center mb-6">Add Transaction</h2>
      <form id="transactionsForm" class="space-y-4">
        <div>
          <input name="description" type="text" placeholder="Description (e.g., Starbucks)"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
          <p class="text-red-400 text-sm mt-1 hidden" id="error-description">Description is required.</p>
        </div>
        <div>
          <input name="amount" type="number" placeholder="Amount"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
          <p class="text-red-400 text-sm mt-1 hidden" id="error-amount">Amount must be a valid number.</p>
        </div>
        <div>
          <input name="date" type="date"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
          <p class="text-red-400 text-sm mt-1 hidden" id="error-date">Date is required.</p>
        </div>
        <div>
          <select name="type"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
            <option value="">-- Select Type --</option>
            <option value="income">Income</option>
            <option value="expense">Expense</option>
          </select>
          <p class="text-red-400 text-sm mt-1 hidden" id="error-type">Type is required.</p>
        </div>
        <div>
          <select name="category_id" id="category_id_select"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
            <option value="">-- Select Category --</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
          </select>
          <p class="text-red-400 text-sm mt-1 hidden" id="error-category_id">Category is required.</p>
        </div>
        <div>
          <select name="account_id" id="account_id_select"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
            <option value="">-- Select Account --</option>
          </select>
          <p class="text-red-400 text-sm mt-1 hidden" id="error-account_id">Account is required.</p>
        </div>
        <button type="submit"
          class="w-full bg-orange-600 hover:bg-orange-700 py-3 rounded-lg text-white font-medium">Save Transaction</button>
        <p class="text-sm text-gray-400 mt-4 text-center">
          <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
        </p>
      </form>
    </div>

    <!-- Transactions List -->
    <div class="w-full max-w-5xl bg-gray-700/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
      <h2 class="text-2xl font-semibold text-center mb-6">Transactions List</h2>
      <!-- Income Transactions -->
      <div>
        <h3 class="text-xl font-semibold text-green-400 mb-3">Income</h3>
        <div id="incomeTransactions"
          class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[250px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
        </div>
      </div>
      <!-- Expense Transactions -->
      <div class="mt-8">
        <h3 class="text-xl font-semibold text-red-400 mb-3">Expenses</h3>
        <div id="expenseTransactions"
          class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[250px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div id="editTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
      <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Edit Transaction</h2>
        <form id="editTransactionForm" class="space-y-4">
          <input type="hidden" id="editTransactionId">
          <div>
            <input type="text" id="editDescription" name="description" placeholder="Description"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
            <p class="text-red-400 text-sm mt-1 hidden" id="edit-error-description">Description is required.</p>
          </div>
          <div>
            <input type="number" id="editAmount" name="amount" placeholder="Amount"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
            <p class="text-red-400 text-sm mt-1 hidden" id="edit-error-amount">Amount must be a valid number.</p>
          </div>
          <div>
            <input type="date" id="editDate" name="date"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
            <p class="text-red-400 text-sm mt-1 hidden" id="edit-error-date">Date is required.</p>
          </div>
          <div>
            <select id="editType" name="type"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
              <option value="">-- Select Type --</option>
              <option value="income">Income</option>
              <option value="expense">Expense</option>
            </select>
            <p class="text-red-400 text-sm mt-1 hidden" id="edit-error-type">Type is required.</p>
          </div>
          <div>
            <select id="editCategoryId" name="category_id"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
              <option value="">-- Select Category --</option>
              @foreach($categories as $category)
              <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
            </select>
            <p class="text-red-400 text-sm mt-1 hidden" id="edit-error-category_id">Category is required.</p>
          </div>
          <div>
            <select id="editAccountId" name="account_id"
              class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
              <option value="">-- Select Account --</option>
            </select>
            <p class="text-red-400 text-sm mt-1 hidden" id="edit-error-account_id">Account is required.</p>
          </div>
          <button type="submit"
            class="w-full bg-orange-600 hover:bg-orange-700 py-3 rounded-lg text-white font-medium">Update Transaction</button>
        </form>
        <button id="closeEditModal" onclick="closeEditModal()" class="mt-4 text-gray-400 hover:underline">Cancel</button>
      </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
      <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Delete Transaction</h2>
        <p class="mb-6">Are you sure you want to delete this transaction?</p>
        <div class="flex space-x-4">
          <button id="confirmDeleteTransaction" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Delete</button>
          <button id="cancelDeleteTransaction" onclick="closeDeleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Cancel</button>
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

  // Toggle Transactions Form
  const addTransactionBtn = document.getElementById('addTransactionBtn');
  const transactionsFormContainer = document.getElementById('transactionsFormContainer');
  addTransactionBtn.addEventListener('click', () => {
    transactionsFormContainer.classList.toggle('hidden');
    addTransactionBtn.textContent = transactionsFormContainer.classList.contains('hidden') ? "Add Transaction" : "Close Form";
    addTransactionBtn.style.backgroundColor = transactionsFormContainer.classList.contains('hidden') ? "green" : "red";
  });

  const incomeTransactions = document.getElementById("incomeTransactions");
  const expenseTransactions = document.getElementById("expenseTransactions");
  const editTransactionModal = document.getElementById("editTransactionModal");
  const deleteTransactionModal = document.getElementById("deleteTransactionModal");
  const confirmDeleteTransactionBtn = document.getElementById("confirmDeleteTransaction");
  let transactionToDelete = null;

  // Function to render a single transaction card
  function renderTransaction(transaction, isIncome = false) {
    const container = isIncome ? incomeTransactions : expenseTransactions;
    const amountColor = isIncome ? 'text-green-400' : 'text-red-400';
    container.innerHTML += `
      <div id="transaction-${transaction.id}" 
           class="p-4 rounded-lg shadow-md w-full h-[150px] flex flex-col justify-between
           ${isIncome ? 'bg-green-900 border border-green-500' : 'bg-red-700/30 border border-red-500'}">
        <h3 class="text-lg font-semibold truncate">${transaction.description}</h3>
        <p class="${amountColor}">Amount: ₹${transaction.amount}</p>
        <p>Date: ${transaction.date}</p>
        <p class="text-gray-400 text-sm">Category: ${transaction.category ? transaction.category.name : "N/A"}</p>
        <p class="text-gray-400 text-sm">Account: ${transaction.account ? transaction.account.name : "N/A"}</p>
        <div class="flex space-x-4 mt-2">
          <button class="bg-blue-500 text-white p-1 hover:bg-blue-600 rounded-md" 
                  onclick="openEditModal(${transaction.id})">Edit</button>
          <button class="bg-red-500 text-white p-1 hover:bg-red-600 rounded-md" 
                  onclick="openDeleteModal(${transaction.id})">Delete</button>
        </div>
      </div>
    `;
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
        incomeTransactions.innerHTML = "";
        expenseTransactions.innerHTML = "";
        transactions.forEach(transaction => {
          renderTransaction(transaction, transaction.type === 'income');
        });
      } else {
        console.error("Transactions fetch failed:", transactions);
        alert(`Failed to load transactions: ${transactions.message || 'Unknown error'}`);
      }
    } catch(error) {
      console.error("Error loading transactions:", error);
      alert("Error loading transactions");
    }
  }

  // Load Accounts and Categories
  async function loadAccountsAndCategories() {
    try {
      let [accRes, catRes] = await Promise.all([
        fetch('/api/accounts', { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token } }),
        fetch('/api/categories', { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token } })
      ]);
      if (accRes.status === 401 || catRes.status === 401) {
        localStorage.removeItem("jwt_token");
        window.location.href = "/login";
        return;
      }
      let accounts = await accRes.json();
      let categories = await catRes.json();
      if (accRes.ok) {
        const addSelect = document.getElementById('account_id_select');
        const editSelect = document.getElementById('editAccountId');
        addSelect.innerHTML = '<option value="">-- Select Account --</option>';
        editSelect.innerHTML = '<option value="">-- Select Account --</option>';
        accounts.forEach(account => {
          addSelect.innerHTML += `<option value="${account.id}">${account.name}</option>`;
          editSelect.innerHTML += `<option value="${account.id}">${account.name}</option>`;
        });
      } else {
        console.error("Accounts fetch failed:", accounts);
      }
      if (catRes.ok) {
        const addSelect = document.getElementById('category_id_select');
        const editSelect = document.getElementById('editCategoryId');
        addSelect.innerHTML = '<option value="">-- Select Category --</option>';
        editSelect.innerHTML = '<option value="">-- Select Category --</option>';
        categories.forEach(category => {
          addSelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
          editSelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
        });
      } else {
        console.error("Categories fetch failed:", categories);
      }
    } catch (error) {
      console.error("Error loading accounts or categories:", error);
      alert("Error loading accounts or categories");
    }
  }

  // Handle Transaction Form Submit (Add)
  document.getElementById('transactionsForm').addEventListener('submit', async function (event) {
    event.preventDefault();
    // Reset errors
    document.querySelectorAll('[id^="error-"]').forEach(el => {
      el.textContent = "Description is required.";
      el.classList.add("hidden");
    });
    let description = document.querySelector('input[name="description"]').value.trim();
    let amount = document.querySelector('input[name="amount"]').value.trim();
    let date = document.querySelector('input[name="date"]').value;
    let type = document.querySelector('select[name="type"]').value;
    let category_id = document.querySelector('select[name="category_id"]').value;
    let account_id = document.querySelector('select[name="account_id"]').value;
    let valid = true;
    if (!description) {
      document.getElementById("error-description").textContent = "Description is required.";
      document.getElementById("error-description").classList.remove("hidden");
      valid = false;
    }
    if (!amount || isNaN(amount) || amount <= 0) {
      document.getElementById("error-amount").textContent = "Amount must be a valid positive number.";
      document.getElementById("error-amount").classList.remove("hidden");
      valid = false;
    }
    if (!date) {
      document.getElementById("error-date").textContent = "Date is required.";
      document.getElementById("error-date").classList.remove("hidden");
      valid = false;
    }
    if (!type) {
      document.getElementById("error-type").textContent = "Type is required.";
      document.getElementById("error-type").classList.remove("hidden");
      valid = false;
    }
    if (!category_id) {
      document.getElementById("error-category_id").textContent = "Category is required.";
      document.getElementById("error-category_id").classList.remove("hidden");
      valid = false;
    }
    if (!account_id) {
      document.getElementById("error-account_id").textContent = "Account is required.";
      document.getElementById("error-account_id").classList.remove("hidden");
      valid = false;
    }
    if (!valid) return;
    let transactionData = { description, amount, date, type, category_id, account_id };
    try {
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
        transactionsFormContainer.classList.add('hidden');
        addTransactionBtn.textContent = "Add Transaction";
        addTransactionBtn.style.backgroundColor = "green";
        loadTransactions();
      } else {
        if (data.errors) {
          for (let field in data.errors) {
            let errorEl = document.getElementById(`error-${field}`);
            if (errorEl) {
              errorEl.textContent = data.errors[field][0];
              errorEl.classList.remove("hidden");
            }
          }
        } else {
          alert(`Failed to add transaction: ${data.message || 'Unknown error'}`);
        }
      }
    } catch (error) {
      console.error("Error adding transaction:", error);
      alert("Error adding transaction");
    }
  });

  // Edit Transaction
  document.getElementById('editTransactionForm').addEventListener('submit', async function (event) {
    event.preventDefault();
    // Reset errors
    document.querySelectorAll('[id^="edit-error-"]').forEach(el => {
      el.textContent = "";
      el.classList.add("hidden");
    });
    const transactionId = document.getElementById('editTransactionId').value;
    const description = document.getElementById('editDescription').value.trim();
    const amount = document.getElementById('editAmount').value.trim();
    const date = document.getElementById('editDate').value;
    const type = document.getElementById('editType').value;
    const category_id = document.getElementById('editCategoryId').value;
    const account_id = document.getElementById('editAccountId').value;
    let valid = true;
    if (!description) {
      document.getElementById("edit-error-description").textContent = "Description is required.";
      document.getElementById("edit-error-description").classList.remove("hidden");
      valid = false;
    }
    if (!amount || isNaN(amount) || amount <= 0) {
      document.getElementById("edit-error-amount").textContent = "Amount must be a valid positive number.";
      document.getElementById("edit-error-amount").classList.remove("hidden");
      valid = false;
    }
    if (!date) {
      document.getElementById("edit-error-date").textContent = "Date is required.";
      document.getElementById("edit-error-date").classList.remove("hidden");
      valid = false;
    }
    if (!type) {
      document.getElementById("edit-error-type").textContent = "Type is required.";
      document.getElementById("edit-error-type").classList.remove("hidden");
      valid = false;
    }
    if (!category_id) {
      document.getElementById("edit-error-category_id").textContent = "Category is required.";
      document.getElementById("edit-error-category_id").classList.remove("hidden");
      valid = false;
    }
    if (!account_id) {
      document.getElementById("edit-error-account_id").textContent = "Account is required.";
      document.getElementById("edit-error-account_id").classList.remove("hidden");
      valid = false;
    }
    if (!valid) return;
    let transactionData = { description, amount, date, type, category_id, account_id };
    try {
      let res = await fetch(`/api/transactions/${transactionId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify(transactionData)
      });
      let data = await res.json();
      if (res.ok) {
        closeEditModal();
        loadTransactions();
      } else {
        if (data.errors) {
          for (let field in data.errors) {
            let errorEl = document.getElementById(`edit-error-${field}`);
            if (errorEl) {
              errorEl.textContent = data.errors[field][0];
              errorEl.classList.remove("hidden");
            }
          }
        } else {
          alert(`Failed to update transaction: ${data.message || 'Unknown error'}`);
        }
      }
    } catch (error) {
      console.error("Error updating transaction:", error);
      alert("Error updating transaction");
    }
  });

  // Open Edit Modal
  async function openEditModal(transactionId) {
    try {
      const res = await fetch(`/api/transactions/${transactionId}`, {
        headers: { 'Authorization': 'Bearer ' + token }
      });
      const transaction = await res.json();
      if (res.ok) {
        document.getElementById('editTransactionId').value = transaction.id;
        document.getElementById('editDescription').value = transaction.description;
        document.getElementById('editAmount').value = transaction.amount;
        document.getElementById('editDate').value = transaction.date;
        document.getElementById('editType').value = transaction.type;
        document.getElementById('editCategoryId').value = transaction.category_id;
        document.getElementById('editAccountId').value = transaction.account_id;
        editTransactionModal.classList.remove("hidden");
      } else {
        alert(`Failed to load transaction: ${transaction.message || 'Unknown error'}`);
      }
    } catch (error) {
      console.error("Error loading transaction:", error);
      alert("Error loading transaction");
    }
  }

  // Open Delete Modal
  function openDeleteModal(transactionId) {
    transactionToDelete = transactionId;
    deleteTransactionModal.classList.remove("hidden");
  }

  // Close Edit Modal
  function closeEditModal() {
    editTransactionModal.classList.add("hidden");
  }

  // Close Delete Modal
  function closeDeleteModal() {
    transactionToDelete = null;
    deleteTransactionModal.classList.add("hidden");
  }

  // Delete Transaction
  confirmDeleteTransactionBtn.addEventListener('click', async function () {
    try {
      let res = await fetch(`/api/transactions/${transactionToDelete}`, {
        method: 'DELETE',
        headers: { 'Authorization': 'Bearer ' + token }
      });
      let data = await res.json();
      if (res.ok) {
        closeDeleteModal();
        loadTransactions();
      } else {
        alert(`Failed to delete transaction: ${data.message || 'Unknown error'}`);
        closeDeleteModal();
      }
    } catch (error) {
      console.error("Error deleting transaction:", error);
      alert("Error deleting transaction");
      closeDeleteModal();
    }
  });

  // Initialize page
  window.onload = () => {
    loadAccountsAndCategories();
    loadTransactions();
  };
</script>
</body>
</html>