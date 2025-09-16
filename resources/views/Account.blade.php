<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accounts</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/266842c081.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex">

  <nav class="bg-gray-800 fixed left-0 top-0 h-full w-64 p-6 flex flex-col">
    <h1 class="text-2xl font-bold mb-8">Mint</h1>
    <div class="flex flex-col space-y-3 flex-grow">
      <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Dashboard</a>
      <a href="{{ route('accounts') }}" class="px-3 py-2 rounded-lg bg-gray-700">Accounts</a>
      <a href="{{ route('bills') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Bills</a>
      <a href="{{ route('budgets') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Budgets</a>
      <a href="{{ route('categories') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Categories</a>
      <a href="{{ route('transactions.index') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Transactions</a>
      <a href="{{ route('goals') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Goals</a>
      <a href="#" class="px-3 py-2 rounded-lg hover:bg-gray-700">Notifications</a>
      <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Profile</a>
    </div>
    <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <div class="flex-1 ml-0 sm:ml-64 p-6 flex flex-col items-center space-y-10">

    <button id="addAccountBtn" class=" bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg font-semibold">+ Add Account</button>

    {{-- Add Account Form --}}
    <div id="addAccountForm" class="hidden bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-lg mx-auto">
      <h2 class="text-2xl font-semibold text-center mb-4">Add Account</h2>
      <form id="accountsForm" class="space-y-4">
        <div>
          <input type="text" name="name" placeholder="Account Name (e.g., Savings)"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
          <p class="text-red-400 text-sm mt-1 hidden" id="nameError">Account name is required.</p>
        </div>
        <div>
          <input type="number" name="balance" placeholder="Balance"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
          <p class="text-red-400 text-sm mt-1 hidden" id="balanceError">Balance must be a valid number.</p>
        </div>
        <div>
          <select name="type"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Select Type</option>
            <option value="Savings Account">Savings Account</option>
            <option value="Current Account">Current Account</option>
            <option value="Credit Card">Credit Card</option>
          </select>
          <p class="text-red-400 text-sm mt-1 hidden" id="typeError">Please select an account type.</p>
        </div>
        <button type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 py-3 rounded-lg text-white font-medium">Save Account</button>
        <p class="text-sm text-gray-400 mt-6 text-center">
          <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
        </p>
      </form>
    </div>

    {{-- Accounts List --}}
    <div class="bg-gray-700/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-5xl mx-auto">
      <h2 class="text-2xl font-semibold text-center mb-6">Accounts List</h2>
      <div id="accountsList" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-h-[500px] overflow-y-auto pr-2">
      </div>
    </div>
  </div>

  <!-- Edit Account Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-gray-800 p-6 rounded-lg w-96">
      <h2 class="text-xl mb-4">Edit Account</h2>
      <form id="editAccountForm" class="space-y-4">
        @csrf
        <input type="hidden" id="editAccountId">

        <div>
          <label class="block">Account Name</label>
          <input type="text" id="editName" class="w-full p-2 rounded bg-gray-700 text-white">
          <p id="editNameError" class="text-red-500 text-sm hidden">Account name is required.</p>
        </div>

        <div>
          <label class="block">Balance</label>
          <input type="number" id="editBalance" class="w-full p-2 rounded bg-gray-700 text-white">
          <p id="editBalanceError" class="text-red-500 text-sm hidden">Balance must be a valid number.</p>
        </div>

        <div>
          <label class="block">Type</label>
          <select id="editType" class="w-full p-2 rounded bg-gray-700 text-white">
            <option value="">Select type</option>
            <option value="Savings Account">Savings Account</option>
            <option value="Current Account">Current Account</option>
            <option value="Credit Card">Credit Card</option>
          </select>
          <p id="editTypeError" class="text-red-500 text-sm hidden">Please select an account type.</p>
        </div>

        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeEditModal()" 
            class="bg-gray-500 px-4 py-2 rounded">Cancel</button>
          <button type="submit" 
            class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded">Update</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Delete Modal --}}
  <div id="deleteModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-2xl shadow-lg w-full max-w-md text-center">
      <h2 class="text-2xl font-semibold mb-4">Are you sure?</h2>
      <p class="text-gray-400 mb-6">Are you sure you want to delete this account?</p>
      <div class="flex justify-center space-x-4">
        <button type="button" onclick="closeDeleteModal()" class="px-6 py-2 rounded-lg bg-gray-600 hover:bg-gray-700">Cancel</button>
        <button type="button" id="confirmDeleteBtn" class="px-6 py-2 rounded-lg bg-red-600 hover:bg-red-700">Delete</button>
      </div>
    </div>
  </div>

  <script>
  const token = localStorage.getItem("jwt_token");
  if (!token) window.location.href = "/login";

  const accountsList = document.getElementById("accountsList");
  const editModal = document.getElementById('editModal');
  const deleteModal = document.getElementById('deleteModal');
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
  let accountIdToDelete = null;

  // Toggle Add Account Form
  let addAccountBtn = document.getElementById('addAccountBtn');
  let addAccountForm = document.getElementById('addAccountForm');
  addAccountBtn.addEventListener("click", function() {
      addAccountForm.classList.toggle("hidden");
      if (addAccountBtn.textContent === "+ Add Account") {
        addAccountBtn.textContent = "❌ Close Form";
      } else {
        addAccountBtn.textContent = "+ Add Account";
      }
    });

  // --- RENDER & MODAL FUNCTIONS ---
  function renderAccount(account) {
    const accountCard = document.createElement('div');
    accountCard.id = `account-${account.id}`;
    accountCard.className = "bg-gray-800 p-4 rounded-lg shadow-md w-full h-[150px] flex flex-col justify-between";
    accountCard.innerHTML = `
        <div>
          <h3 class="text-lg font-semibold truncate">${account.name}</h3>
          <p>Balance: <span class="font-medium">₹${account.balance}</span></p>
          <p>Type: ${account.type}</p>
        </div>
        <div class="flex justify-end space-x-2">
          <button onclick="openEditModal(${account.id})" 
            class="text-sm bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded">Edit</button>
          <button onclick="openDeleteModal(${account.id})" 
            class="text-sm bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded">Delete</button>
        </div>
    `;
    accountsList.appendChild(accountCard);
  }

  // Load Accounts
  async function loadAccounts() {
    const res = await fetch("/api/accounts", { method: 'GET', headers: { Authorization: `Bearer ${token}` } });
    const data = await res.json();
    if (res.ok) {
      accountsList.innerHTML = "";
      data.forEach(account => renderAccount(account));
    }
  }

  //Add Account
  document.getElementById('accountsForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const formData = new FormData(this);
    const name = formData.get('name').trim();
    const balance = formData.get('balance').trim();
    const type = formData.get('type'); // ✅ FIXED: Removed `.value`

    const errors = {
      name: document.getElementById('nameError'),
      balance: document.getElementById('balanceError'),
      type: document.getElementById('typeError')
    };

    let valid = true;
    if(!name){errors.name.classList.remove("hidden");valid = false;} 
    else{errors.name.classList.add("hidden");}
    if(!balance || isNaN(balance) || parseFloat(balance) <= 0){errors.balance.classList.remove("hidden");valid = false;} 
    else{errors.balance.classList.add("hidden");}
    if(!type){errors.type.classList.remove("hidden");valid = false;} 
    else{errors.type.classList.add("hidden");}
    if(!valid) return;

    const res = await fetch('/api/accounts', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
      body: JSON.stringify({ name, balance, type })
    });

    const data = await res.json();
    if(res.ok){
      renderAccount(data);
      this.reset();
    }
  });

  // EDIT ACCOUNT
  document.getElementById('editAccountForm').addEventListener('submit', async function (event) {
    event.preventDefault();
    const id = document.getElementById('editAccountId').value;
    const name = document.getElementById('editName').value;
    const balance = document.getElementById('editBalance').value;
    const type = document.getElementById('editType').value;

    let valid = true;
    if(!name){document.getElementById("editNameError").classList.remove("hidden");valid = false;} 
    else{document.getElementById("editNameError").classList.add("hidden");}
    if(!balance || isNaN(balance) || parseFloat(balance) <= 0){document.getElementById("editBalanceError").classList.remove("hidden");valid = false;} 
    else{document.getElementById("editBalanceError").classList.add("hidden");}
    if(!type){document.getElementById("editTypeError").classList.remove("hidden");valid = false;} 
    else{document.getElementById("editTypeError").classList.add("hidden");}
    if(!valid) return;

    const res = await fetch(`/api/accounts/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
      body: JSON.stringify({ name, balance, type })
    });

    if (res.ok) {
      closeEditModal();
      loadAccounts();
    } else {
      alert('Failed to update account.');
    }
  });

  async function openEditModal(id) {
    const res = await fetch(`/api/accounts/${id}`, { headers: { 'Authorization': `Bearer ${token}` } });
    if (res.ok) {
      const account = await res.json();
      document.getElementById('editAccountId').value = account.id;
      document.getElementById('editName').value = account.name;
      document.getElementById('editBalance').value = account.balance;
      document.getElementById('editType').value = account.type;
      editModal.classList.remove('hidden');
    }
  }

  function openDeleteModal(id) {
    accountIdToDelete = id;
    deleteModal.classList.remove('hidden');
  }

  function closeEditModal() {
    editModal.classList.add('hidden');
  }

  function closeDeleteModal() {
    accountIdToDelete = null;
    deleteModal.classList.add('hidden');
  }

  confirmDeleteBtn.addEventListener('click', async () => {
    if (!accountIdToDelete) return;

    const res = await fetch(`/api/accounts/${accountIdToDelete}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    });

    if (res.ok) {
      document.getElementById(`account-${accountIdToDelete}`).remove();
      closeDeleteModal();
    } else {
      alert('Failed to delete account.');
      closeDeleteModal();
    }
  });

  document.getElementById("logoutBtn").addEventListener("click", () => {
    localStorage.removeItem("jwt_token");
    window.location.href = "/login";
  });

  window.onload = loadAccounts;
</script>

</body>
</html>
