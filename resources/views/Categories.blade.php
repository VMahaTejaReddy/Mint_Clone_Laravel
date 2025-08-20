<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Category</title>
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
      <a href="{{ route('categories') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg bg-gray-700 text-center lg:text-left">Categories</a>
      <a href="{{ route('transactions') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Transactions</a>
      <a href="{{ route('goals') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Goals</a>
      <a href="#" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Notifications</a>
      <a href="{{ route('profile') }}" class="block px-2 py-1 sm:px-4 sm:py-2 rounded-lg hover:bg-gray-700 text-center lg:text-left">Profile</a>
    </div>
    <button id="logoutBtn" class="mt-4 bg-red-600 hover:bg-red-700 w-full px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <!-- Category Form -->
  <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md min-h-[350px] flex flex-col">
    <h2 class="text-2xl font-bold mb-6 text-center">Add Category</h2>

    <form id="categoryForm" class="space-y-5" method="POST" action="{{ route('categories.store') }}">
  @csrf
  <!-- Category Name -->
  <div>
    <label for="name" class="block text-sm font-medium mb-1">Category Name</label>
    <input 
      type="text" 
      id="name" 
      name="name" 
      placeholder="Enter category name"
      class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 
             focus:ring-2 focus:ring-indigo-500 focus:outline-none"
      required
    >
    <!-- Inline Error -->
    <p id="nameError" class="text-red-500 text-sm mt-1 hidden"></p>
  </div>

  <!-- Submit Button -->
  <button 
    type="submit" 
    class="w-full py-2 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 
           transition text-white font-medium">
    Add Category
  </button>
</form>


    <!-- Back to Dashboard -->
    <p class="text-sm text-gray-400 mt-6 text-center">
      <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
    </p>
  </div>

  <!-- Categories List -->
  <div class="w-full max-w-5xl mt-12 bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-semibold text-center mb-6">Categories List</h2>
    
    <div id="categoriesList" 
         class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 
                max-h-96 overflow-y-auto p-2">
      <!-- Categories will be dynamically loaded here -->
    </div>
  </div>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
      window.location.href = "/login";
    }

    // Handle form submit
    // Handle form submit
document.getElementById('categoryForm').addEventListener('submit', async function(event){
  event.preventDefault();

  // Clear old error
  document.getElementById('nameError').textContent = "";
  document.getElementById('nameError').classList.add("hidden");

  let categoryData = {
    name: document.getElementById('name').value
  };

  let res = await fetch('/api/categories', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify(categoryData)
  });

  let data = await res.json();

  if (res.ok) {
    // Append category to list
    document.getElementById('categoriesList').innerHTML += `
      <div class="bg-gray-700 p-4 rounded-lg shadow-md text-center">
        <h3 class="text-lg font-semibold truncate">${data.name}</h3>
      </div>
    `;
    document.getElementById('categoryForm').reset();
  } else {
    // Show inline error if available
    if (data.errors && data.errors.name) {
      document.getElementById('nameError').textContent = data.errors.name[0];
      document.getElementById('nameError').classList.remove("hidden");
    } else if (data.message) {
      document.getElementById('nameError').textContent = data.message;
      document.getElementById('nameError').classList.remove("hidden");
    }
  }
});


    // Load existing categories on page load
    window.onload = async () => {
      let res = await fetch('/api/categories', {
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token
        }
      });

      let categories = await res.json();

      if (res.ok && Array.isArray(categories)) {
        categories.forEach(cat => {
          document.getElementById('categoriesList').innerHTML += `
            <div class="bg-gray-700 p-4 rounded-lg shadow-md text-center">
              <h3 class="text-lg font-semibold truncate">${cat.name}</h3>
            </div>
          `;
        });
      }
    };
  </script>
</body>
</html>
