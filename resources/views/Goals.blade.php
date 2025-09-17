<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Goals</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex">

  <nav class="bg-gray-800 fixed left-0 top-0 h-full w-64 p-6 flex flex-col">
    <h1 class="text-2xl font-bold mb-8">Mint</h1>
    <div class="flex flex-col space-y-3 flex-grow">
      <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Dashboard</a>
      <a href="{{ route('accounts') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Accounts</a>
      <a href="{{ route('bills') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Bills</a>
      <a href="{{ route('budgets') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Budgets</a>
      <a href="{{ route('categories') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Categories</a>
      <a href="{{ route('transactions') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Transactions</a>
      <a href="{{ route('goals') }}" class="px-3 py-2 rounded-lg bg-gray-700">Goals</a>
      <a href="{{ route('notifications') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Notifications</a>
      <a href="{{ route('profile') }}" class="px-3 py-2 rounded-lg hover:bg-gray-700">Profile</a>
    </div>
    <button id="logoutBtn" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">Logout</button>
  </nav>

  <main class="flex-1 ml-0 sm:ml-64 p-6 flex flex-col items-center space-y-10">

    <button id="addGoalBtn" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg font-semibold">Add Goal</button>

    <div id="addGoalForm" class="hidden bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-xl">
      <h2 class="text-2xl font-semibold text-center mb-6">Add Goal</h2>
      <form id="goalsForm" class="space-y-4">
        <div>
          <input type="text" name="name" placeholder="Goal Name"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-500 text-sm mt-1 hidden" id="nameError">Goal name is required.</p>
        </div>
        <div>
          <input type="number" name="target_amount" placeholder="Target Amount"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-500 text-sm mt-1 hidden" id="targetAmountError">Target amount must be a valid number.</p>
        </div>
        <div>
          <input type="number" name="current_amount" placeholder="Current Amount"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-500 text-sm mt-1 hidden" id="currentAmountError">Current amount must be a valid number.</p>
        </div>
        <div>
          <input type="date" name="due_date"
            class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
          <p class="text-red-500 text-sm mt-1 hidden" id="dueDateError">A valid due date is required.</p>
        </div>
        <button type="submit"
          class="w-full bg-green-600 hover:bg-green-700 py-3 rounded-lg text-white font-medium">Save Goal</button>
        <p class="text-sm text-gray-400 text-center">
          <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
        </p>
      </form>
    </div>

    <div class="bg-gray-700/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-5xl">
      <h2 class="text-2xl font-semibold text-center mb-6">Goals List</h2>
      <div id="goalsList" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      </div>
    </div>
  </main>

  <div id="editModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-2xl shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-semibold text-center mb-6">Edit Goal</h2>
      <form id="editGoalForm" class="space-y-4">
        <input type="hidden" id="editGoalId">
        <div>
          <label class="block text-sm font-medium text-gray-300">Goal Name</label>
          <input type="text" id="editName" class="w-full p-2 rounded bg-gray-700 text-white">
          <p id="editNameError" class="text-red-500 text-sm mt-1 hidden">Goal name is required.</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-300">Target Amount</label>
          <input type="number" id="editTargetAmount" class="w-full p-2 rounded bg-gray-700 text-white">
          <p id="editTargetAmountError" class="text-red-500 text-sm mt-1 hidden">Target amount must be a valid number.</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-300">Current Amount</label>
          <input type="number" id="editCurrentAmount" class="w-full p-2 rounded bg-gray-700 text-white">
          <p id="editCurrentAmountError" class="text-red-500 text-sm mt-1 hidden">Current amount must be a valid number.</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-300">Due Date</label>
          <input type="date" id="editDueDate" class="w-full p-2 rounded bg-gray-700 text-white">
          <p id="editDueDateError" class="text-red-500 text-sm mt-1 hidden">A valid due date is required.</p>
        </div>
        <div class="flex justify-end space-x-2 pt-4">
          <button type="button" onclick="closeEditModal()" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded">Cancel</button>
          <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Update</button>
        </div>
      </form>
    </div>
  </div>

  <div id="deleteModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-2xl shadow-lg w-full max-w-md text-center">
      <h2 class="text-2xl font-semibold mb-4">Are you sure?</h2>
      <p class="text-gray-400 mb-6">Do you really want to delete this goal?</p>
      <div class="flex justify-center space-x-4">
        <button type="button" onclick="closeDeleteModal()" class="px-6 py-2 rounded-lg bg-gray-600 hover:bg-gray-700">Cancel</button>
        <button type="button" id="confirmDeleteBtn" class="px-6 py-2 rounded-lg bg-red-600 hover:bg-red-700">Delete</button>
      </div>
    </div>
  </div>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) window.location.href = "/login";

    const goalsList = document.getElementById("goalsList");
    const editModal = document.getElementById("editModal");
    const deleteModal = document.getElementById("deleteModal");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    let goalIdToDelete = null;

    // --- TOGGLE FORM ---
    let addGoalBtn = document.getElementById("addGoalBtn");
    let addGoalForm = document.getElementById("addGoalForm");
    addGoalBtn.addEventListener("click", function() {
      addGoalForm.classList.toggle("hidden");
      if (addGoalBtn.textContent === "Add Goal") {
        addGoalBtn.textContent = "Close Form";
      } else {
        addGoalBtn.textContent="Add Goal";
      }
    });

    // --- RENDER & MODAL FUNCTIONS ---
    function renderGoal(goal) {
        const card = document.createElement("div");
        card.id = `goal-${goal.id}`;
        card.className = "bg-gray-800 p-5 rounded-xl shadow hover:shadow-lg transition flex flex-col justify-between";
        card.innerHTML = `
            <div>
                <h3 class="text-lg font-semibold mb-2">${goal.name}</h3>
                <p>🎯 Target: <span class="font-medium">₹${goal.target_amount}</span></p>
                <p>💰 Current: <span class="font-medium">₹${goal.current_amount}</span></p>
                <p>📅 Due: <span class="font-medium">${goal.due_date}</span></p>
            </div>
            <div class="flex justify-end space-x-2 mt-3">
                <button onclick="openEditModal(${goal.id})" class="text-sm bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded">Edit</button>
                <button onclick="openDeleteModal(${goal.id})" class="text-sm bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded">Delete</button>
            </div>
        `;
        goalsList.appendChild(card);
    }

    // Load goals from the API
    async function loadGoals() {
        try {
            const res = await fetch('/api/goals', { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token } });
            const data = await res.json();
            if (res.ok) {
                goalsList.innerHTML = "";
                data.forEach(goal => renderGoal(goal));
            }
        } catch (err) { console.error('Error loading goals', err); }
    }

    // Handle form submission for adding a new goal
    document.getElementById("goalsForm").addEventListener("submit", async function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        const name = formData.get('name');
        const targetAmount = formData.get('target_amount');
        const currentAmount = formData.get('current_amount');
        const dueDate = formData.get('due_date');

        const errors = {
            name: document.getElementById("nameError"),
            targetAmount: document.getElementById("targetAmountError"),
            currentAmount: document.getElementById("currentAmountError"),
            dueDate: document.getElementById("dueDateError")
        };

        // Handling Validations

        let valid = true;
        if(!name) { errors.name.classList.remove("hidden"); valid = false; } 
        else { errors.name.classList.add("hidden"); }
        if(!targetAmount) { errors.targetAmount.classList.remove("hidden"); valid = false; } 
        else { errors.targetAmount.classList.add("hidden"); }
        if(!currentAmount) { errors.currentAmount.classList.remove("hidden"); valid = false; } 
        else { errors.currentAmount.classList.add("hidden"); }
        if(!dueDate) { errors.dueDate.classList.remove("hidden"); valid = false; } 
        else { errors.dueDate.classList.add("hidden"); }

        if (!valid) return;

        const goalData = { name, target_amount: targetAmount, current_amount: currentAmount, due_date: dueDate };
        const res = await fetch('/api/goals', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
            body: JSON.stringify(goalData)
        });

        if (res.ok) {
            loadGoals();
            this.reset();
        } else {
            alert('Failed to create goal.');
        }
    });

    // Handle form submission for editing a goal
    document.getElementById("editGoalForm").addEventListener("submit", async function (event) {
        event.preventDefault();
        const goalId = document.getElementById("editGoalId").value;
        const name = document.getElementById("editName").value;
        const targetAmount = document.getElementById("editTargetAmount").value;
        const currentAmount = document.getElementById("editCurrentAmount").value;
        const dueDate = document.getElementById("editDueDate").value;

        const errors = {
            name: document.getElementById("editNameError"),
            targetAmount: document.getElementById("editTargetAmountError"),
            currentAmount: document.getElementById("editCurrentAmountError"),
            dueDate: document.getElementById("editDueDateError")
        };

        // Validate form fields
        let valid = true;
        if(!name){errors.name.classList.remove("hidden"); valid = false;} 
        else {errors.name.classList.add("hidden");}
        if(!targetAmount){errors.targetAmount.classList.remove("hidden"); valid = false;}
        else {errors.targetAmount.classList.add("hidden");}
        if(!currentAmount){errors.currentAmount.classList.remove("hidden"); valid = false;}
        else {errors.currentAmount.classList.add("hidden");}
        if(!dueDate){errors.dueDate.classList.remove("hidden"); valid = false;}
        else {errors.dueDate.classList.add("hidden");}

        if (!valid) return;

        const goalData = { name, target_amount: targetAmount, current_amount: currentAmount, due_date: dueDate };
        const res = await fetch(`/api/goals/${goalId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
            body: JSON.stringify(goalData)
        });

        if (res.ok) {
            closeEditModal();
            loadGoals();
        } else {
            alert('Failed to update goal.');
        }
    });

    // Open edit modal
    async function openEditModal(id) {
        const res = await fetch(`/api/goals/${id}`, { headers: { 'Authorization': `Bearer ${token}` } });
        if (res.ok) {
            const goal = await res.json();
            document.getElementById('editGoalId').value = goal.id;
            document.getElementById('editName').value = goal.name;
            document.getElementById('editTargetAmount').value = goal.target_amount;
            document.getElementById('editCurrentAmount').value = goal.current_amount;
            document.getElementById('editDueDate').value = goal.due_date;
            editModal.classList.remove("hidden");
        }
    }

     // Close Edit Modal
    function closeEditModal() {
        editModal.classList.add("hidden");
    }

    // Open Delete Modal
    function openDeleteModal(id) {
        goalIdToDelete = id;
        deleteModal.classList.remove("hidden");
    }

    // Close Delete Modal
    function closeDeleteModal() {
        goalIdToDelete = null;
        deleteModal.classList.add("hidden");
    }

    // Confirm Delete
    confirmDeleteBtn.addEventListener('click', async () => {
        if (!goalIdToDelete) return;
        const res = await fetch(`/api/goals/${goalIdToDelete}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.ok) {
            document.getElementById(`goal-${goalIdToDelete}`).remove();
            closeDeleteModal();
        } else {
            alert('Failed to delete goal.');
            closeDeleteModal();
        }
    });

    document.getElementById("logoutBtn").addEventListener("click", () => {
        localStorage.removeItem("jwt_token");
        window.location.href = "/login";
    });

    window.onload = loadGoals;
  </script>
</body>
</html>