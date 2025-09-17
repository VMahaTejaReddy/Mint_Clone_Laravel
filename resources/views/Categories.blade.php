<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categories</title>
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
      <a href="{{ route('categories') }}" class="px-3 py-2 rounded-lg bg-gray-700">Categories</a>
      <a href="{{ route('transactions') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Transactions</a>
      <a href="{{ route('goals') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Goals</a>
      <a href="{{ route('notifications') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Notifications</a>
      <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Profile</a>
    </div>
    <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <!-- Main Content -->
  <main class="flex-1 ml-0 sm:ml-64 p-6 flex flex-col items-center space-y-10">

    <button id="addCategoryBtn" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg font-semibold">Add Category</button>

    <!-- Add Category Form -->
    <div id="AddCategoryForm" class="hidden bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center">Add Category</h2>
      <form id="categoryForm" class="space-y-5">
        <div>
          <label for="name" class="block text-sm font-medium mb-1">Category Name</label>
          <input type="text" id="name" name="name" placeholder="Enter category name"
            class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
          <p id="nameError" class="text-red-500 text-sm mt-1 hidden"></p>
        </div>
        <button type="submit" class="w-full py-2 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 transition text-white font-medium">Add Category</button>
      </form>
    </div>

    <!-- Categories List -->
    <div class="w-full max-w-5xl bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
      <h2 class="text-2xl font-semibold text-center mb-6">Categories List</h2>
      <div id="categoriesList" class="flex flex-col space-y-4 max-h-96 overflow-y-auto p-2"></div>
    </div>

    <!-- Edit Modal -->
    <div id="EditCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Edit Category</h2>
        <form id="editCategoryForm" class="space-y-4">
          <input type="hidden" id="editCategoryId">
          <div>
            <label for="editName" class="block text-sm font-medium mb-1">Category Name</label>
            <input type="text" id="editName" name="name"
              class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            <p id="editNameError" class="text-red-500 text-sm mt-1 hidden"></p>
          </div>
          <button type="submit" class="w-full py-2 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 transition text-white font-medium">Save Changes</button>
        </form>
        <button onclick="closeEditModal()" class="mt-4 text-gray-400 hover:underline">Cancel</button>
      </div>
    </div>

    <!-- Delete Modal -->
    <div id="DeleteCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Delete Category</h2>
        <p class="mb-4">Are you sure you want to delete this category?</p>
        <div class="flex justify-end">
          <button id="confirmDeleteBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Delete</button>
          <button onclick="closeDeleteModal()" class="ml-2 text-gray-400 hover:underline">Cancel</button>
        </div>
      </div>
    </div>

  </main>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) window.location.href = "/login";

    document.getElementById("logoutBtn").addEventListener("click", () => {
      localStorage.removeItem("jwt_token");
      window.location.href = "/login";
    });

    // Toggle Add Form
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    const addCategoryForm = document.getElementById('AddCategoryForm');
    addCategoryBtn.addEventListener('click', () => {
      addCategoryForm.classList.toggle('hidden');
      addCategoryBtn.textContent = addCategoryForm.classList.contains('hidden') ? "Add Category" : "Close Form";
    });

    // Add Category
    document.getElementById('categoryForm').addEventListener('submit', async e => {
      e.preventDefault();
      document.getElementById('nameError').classList.add("hidden");
      const categoryData = { name: document.getElementById('name').value };

      let valid = true;
      if(!categoryData.name.trim()){
        document.getElementById('nameError').textContent = "Category name is required.";
        document.getElementById('nameError').classList.remove("hidden");
        valid = false;
      }

      let res = await fetch('/api/categories', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
        body: JSON.stringify(categoryData)
      });
      let data = await res.json();

      if (res.ok) {
        appendCategory(data);
        e.target.reset();
      } else showInlineError('nameError', data);
    });

    // Append category card
function appendCategory(cat) {
  document.getElementById('categoriesList').innerHTML += `
    <div class="bg-gray-700 p-4 rounded-lg shadow-md flex items-center justify-between" id="cat-${cat.id}">
      <h3 class="text-lg font-semibold truncate">${cat.name}</h3>
      <div class="flex space-x-4">
        <button onclick="openEditModal(${cat.id}, '${cat.name}')" class="text-indigo-400 hover:text-indigo-600 font-medium">Edit</button>
        <button onclick="openDeleteModal(${cat.id})" class="text-red-400 hover:text-red-600 font-medium">Delete</button>
      </div>
    </div>`;
}


    // Load categories
    window.onload = async () => {
      let res = await fetch('/api/categories', { headers: { 'Authorization': 'Bearer ' + token } });
      let categories = await res.json();
      if (res.ok) categories.forEach(appendCategory);
    };

    // ----- Edit -----
    function openEditModal(id, name) {
      document.getElementById('editCategoryId').value = id;
      document.getElementById('editName').value = name;
      document.getElementById('EditCategoryModal').classList.remove('hidden');
    }
    function closeEditModal() { document.getElementById('EditCategoryModal').classList.add('hidden'); }

    document.getElementById('editCategoryForm').addEventListener('submit', async e => {
      e.preventDefault();
      const id = document.getElementById('editCategoryId').value;
      const name = document.getElementById('editName').value;
      document.getElementById('editNameError').classList.add("hidden");

      let valid = true;
      if(!name.trim()){
        document.getElementById('editNameError').textContent = "Category name is required.";
        document.getElementById('editNameError').classList.remove("hidden");
        valid = false;
      }

      let res = await fetch(`/api/categories/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
        body: JSON.stringify({ name })
      });
      let data = await res.json();

      if (res.ok) {
        document.querySelector(`#cat-${id} h3`).textContent = data.name;
        closeEditModal();
      } else showInlineError('editNameError', data);
    });

    // ----- Delete -----
    let deleteId = null;
    function openDeleteModal(id) {
      deleteId = id;
      document.getElementById('DeleteCategoryModal').classList.remove('hidden');
    }
    function closeDeleteModal() { document.getElementById('DeleteCategoryModal').classList.add('hidden'); }

    document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
      if (!deleteId) return;
      let res = await fetch(`/api/categories/${deleteId}`, {
        method: 'DELETE',
        headers: { 'Authorization': 'Bearer ' + token }
      });
      if (res.ok) {
        document.getElementById(`cat-${deleteId}`).remove();
        closeDeleteModal();
      }
    });

    // Helper for inline errors
    function showInlineError(elId, data) {
      if (data.errors && data.errors.name) {
        document.getElementById(elId).textContent = data.errors.name[0];
        document.getElementById(elId).classList.remove("hidden");
      } else if (data.message) {
        document.getElementById(elId).textContent = data.message;
        document.getElementById(elId).classList.remove("hidden");
      }
    }
  </script>
</body>
</html>
