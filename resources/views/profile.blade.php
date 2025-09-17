<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Profile</title>
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
      <a href="{{ route('transactions') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Transactions</a>
      <a href="{{ route('goals') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Goals</a>
      <a href="#" class="px-3 py-2 rounded-lg hover:bg-gray-700">Notifications</a>
      <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg bg-gray-700">Profile</a>
    </div>
    <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <!-- Main Content -->
  <main class="flex-1 ml-0 sm:ml-64 p-6 space-y-8">
    <h2 class="text-3xl font-bold text-white">Profile</h2>

    <!-- Profile Info Card -->
    <div id="profileCard" class="bg-gray-800 p-6 rounded-2xl shadow-lg flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
      <!-- Profile Icon placeholder -->
      <div id="profileIcon" class="w-24 h-24 bg-indigo-600 rounded-full flex items-center justify-center text-4xl font-bold text-white"></div>
      <div class="text-center sm:text-left">
        <h3 id="profileName" class="text-2xl font-semibold"></h3>
        <p id="profileEmail" class="text-gray-400"></p>
      </div>
      <div class="sm:ml-auto flex space-x-2">
        <button onclick="openEditModal()" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg font-semibold">Edit Profile</button>
        <button onclick="openDeleteModal()" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Delete Account</button>
      </div>
    </div>

    <!-- Accounts List -->
    <div>
      <h3 class="text-2xl font-bold text-white mt-8 mb-4">Your Accounts</h3>
      <div id="accountsList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Account cards will be dynamically inserted here -->
      </div>
    </div>
  </main>

  <!-- Edit Profile Modal -->
  <div id="editProfileModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6">Edit Profile</h2>
      <form id="editProfileForm" class="space-y-4">
        <div>
          <label for="editName" class="block text-sm font-medium text-gray-300">Name</label>
          <input type="text" id="editName" name="name" class="w-full p-3 mt-1 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
          <p id="editNameError" class="text-red-400 text-sm mt-1 hidden"></p>
        </div>
        <div>
          <label for="editEmail" class="block text-sm font-medium text-gray-300">Email</label>
          <input type="email" id="editEmail" name="email" class="w-full p-3 mt-1 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
          <p id="editEmailError" class="text-red-400 text-sm mt-1 hidden"></p>
        </div>
        <div class="flex justify-end space-x-4 pt-4">
          <button type="button" onclick="closeEditModal()" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg">Cancel</button>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Account Modal -->
  <div id="deleteAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md text-center">
      <h2 class="text-2xl font-bold mb-4">Delete Account</h2>
      <p class="text-gray-300 mb-6">Are you sure? This action is permanent and cannot be undone. All your data will be lost.</p>
      <div class="flex justify-center space-x-4">
        <button type="button" onclick="closeDeleteModal()" class="bg-gray-600 hover:bg-gray-700 px-6 py-2 rounded-lg">Cancel</button>
        <button type="button" id="confirmDeleteBtn" class="bg-red-600 hover:bg-red-700 px-6 py-2 rounded-lg">Delete Permanently</button>
      </div>
    </div>
  </div>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
      window.location.href = window.location.origin + "/login";
    }

    // --- DOM Elements ---
    const profileIcon = document.getElementById('profileIcon');
    const profileName = document.getElementById('profileName');
    const profileEmail = document.getElementById('profileEmail');
    const accountsList = document.getElementById('accountsList');
    const editProfileModal = document.getElementById('editProfileModal');
    const deleteAccountModal = document.getElementById('deleteAccountModal');
    const editProfileForm = document.getElementById('editProfileForm');

    let currentUser = null; // To store user data globally on this page

    // --- Fetch and Render Profile Data ---
    async function fetchProfileData() {
      try {
        const res = await fetch('/api/profile', {
          headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Could not fetch profile.');
        
        currentUser = await res.json();
        
        // Populate profile card
        profileName.textContent = currentUser.name;
        profileEmail.textContent = currentUser.email;
        profileIcon.textContent = currentUser.name.charAt(0).toUpperCase();

        // Populate accounts list
        accountsList.innerHTML = ''; // Clear previous
        if (currentUser.accounts && currentUser.accounts.length > 0) {
          currentUser.accounts.forEach(account => {
            const accountCard = `
              <div class="bg-gray-800 p-4 rounded-lg shadow-md">
                <h4 class="text-lg font-semibold">${account.name}</h4>
                <p class="text-gray-400">${account.type}</p>
                <p class="text-2xl font-bold mt-2">₹${parseFloat(account.balance).toFixed(2)}</p>
              </div>
            `;
            accountsList.innerHTML += accountCard;
          });
        } else {
          accountsList.innerHTML = '<p class="text-gray-400 col-span-full text-center">No accounts found.</p>';
        }
      } catch (error) {
        console.error("Profile fetch error:", error);
        // Redirect to login if unauthorized
        if (error.response && error.response.status === 401) {
            window.location.href = window.location.origin + "/login";
        }
      }
    }

    // --- Edit Modal ---
    function openEditModal() {
      if (!currentUser) return;
      document.getElementById('editName').value = currentUser.name;
      document.getElementById('editEmail').value = currentUser.email;
      editProfileModal.classList.remove('hidden');
    }

    function closeEditModal() {
      editProfileModal.classList.add('hidden');
    }

    editProfileForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const name = document.getElementById('editName').value;
      const email = document.getElementById('editEmail').value;

      try {
        const res = await fetch('/api/user', {
          method: 'PUT',
          headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ name, email })
        });
        
        if (!res.ok) {
            const errorData = await res.json();
            throw new Error(errorData.message || 'Failed to update profile.');
        }

        closeEditModal();
        fetchProfileData(); // Refresh data on page
      } catch (error) {
        alert(error.message);
      }
    });

    // --- Delete Modal ---
    function openDeleteModal() {
      deleteAccountModal.classList.remove('hidden');
    }

    function closeDeleteModal() {
      deleteAccountModal.classList.add('hidden');
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
      try {
        const res = await fetch('/api/user', {
          method: 'DELETE',
          headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        });

        if (!res.ok) throw new Error('Could not delete account.');

        localStorage.removeItem("jwt_token");
        alert('Account deleted successfully.');
        window.location.href = window.location.origin + "/login";

      } catch (error) {
        alert(error.message);
        closeDeleteModal();
      }
    });

    // --- Logout ---
    document.getElementById("logoutBtn").addEventListener("click", () => {
      localStorage.removeItem("jwt_token");
      window.location.href = window.location.origin + "/login";
    });

    // --- Initial Load ---
    window.onload = fetchProfileData;

  </script>
</body>
</html>
