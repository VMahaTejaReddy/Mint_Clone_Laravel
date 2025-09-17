{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom scrollbar for better aesthetics */
        .scrollbar-thin::-webkit-scrollbar {
            width: 8px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #2d3748; /* gray-800 */
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: #4a5568; /* gray-600 */
            border-radius: 20px;
            border: 3px solid #2d3748; /* gray-800 */
        }
    </style>
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
        <button id="addTransactionBtn" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg font-semibold">+ Add Transaction</button>
        
        <!-- Transactions Form -->
        <div id="transactionsFormContainer" class="hidden bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-semibold text-center mb-6">Add Transaction</h2>
            <form id="transactionsForm" class="space-y-4">
                <div>
                    <input name="description" type="text" placeholder="Description (e.g., Starbucks)" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-description">Description is required.</p>
                </div>
                <div>
                    <input name="amount" type="number" step="0.01" placeholder="Amount" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-amount">Amount must be a valid number.</p>
                </div>
                <div>
                    <input name="date" type="date" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-date">Date is required.</p>
                </div>
                <div>
                    <select name="type" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                        <option value="">-- Select Type --</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-type">Type is required.</p>
                </div>
                <div>
                    <select name="category_id" id="category_id_select" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-category_id">Category is required.</p>
                </div>
                <div>
                    <select name="account_id" id="account_id_select" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                        <option value="">-- Select Account --</option>
                    </select>
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-account_id">Account is required.</p>
                </div>
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 py-3 rounded-lg text-white font-medium">Save Transaction</button>
            </form>
        </div>

        <!-- Transactions List Container -->
        <div class="w-full max-w-6xl bg-gray-700/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center mb-6">Transactions by Account</h2>
            <div id="transactionsByAccount" class="space-y-8">
                <!-- Transaction data will be dynamically injected here -->
            </div>
        </div>
    </main>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
        window.location.href = "/login";
    }

    document.getElementById("logoutBtn").addEventListener("click", async function () {
        localStorage.removeItem("jwt_token");
        window.location.href = "/login";
    });

    // --- TOGGLE FORM ---
  let addTransactionBtn = document.getElementById("addTransactionBtn");
  let transactionsFormContainer = document.getElementById("transactionsFormContainer");

  addTransactionBtn.addEventListener("click", () => {
    transactionsFormContainer.classList.toggle("hidden");
    if (transactionsFormContainer.classList.contains("hidden")) {
      addTransactionBtn.textContent = "+ Add Transaction";
    } else {
      addTransactionBtn.textContent = "Close Form";
    }
  });


    const transactionsByAccountContainer = document.getElementById("transactionsByAccount");

    // Function to return a transaction card HTML string
    function renderTransactionCard(transaction) {
        const isIncome = transaction.type === 'income';
        const amountColor = isIncome ? 'text-green-400' : 'text-red-400';
        const cardBg = isIncome ? 'bg-green-900/50 border border-green-700' : 'bg-red-900/50 border border-red-700';
        
        return `
            <div id="transaction-${transaction.id}" class="p-4 rounded-lg shadow-md w-full flex flex-col justify-between ${cardBg}">
                <div>
                    <h3 class="text-lg font-semibold truncate">${transaction.description}</h3>
                    <p class="${amountColor} font-bold text-xl">₹${parseFloat(transaction.amount).toFixed(2)}</p>
                </div>
                <div class="text-gray-400 text-sm mt-2">
                    <p>Date: ${transaction.date}</p>
                    <p>Category: ${transaction.category ? transaction.category.name : "N/A"}</p>
                </div>
            </div>
        `;
    }

    // Load and display transactions grouped by account
    async function loadTransactions() {
        try {
            const res = await fetch('/api/transactions', {
                headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
            });
            const accounts = await res.json();
            
            if (!res.ok) {
                throw new Error(accounts.message || 'Failed to load transactions');
            }

            transactionsByAccountContainer.innerHTML = ""; // Clear previous content

            if (accounts.length === 0) {
                transactionsByAccountContainer.innerHTML = '<p class="text-center text-gray-400">No accounts found. Please add an account first.</p>';
                return;
            }

            accounts.forEach(account => {
                const incomeForAccount = account.transactions.filter(t => t.type === 'income');
                const expensesForAccount = account.transactions.filter(t => t.type === 'expense');

                let incomeHTML = '';
                if (incomeForAccount.length > 0) {
                    incomeHTML = `
                        <div>
                            <h4 class="text-lg font-semibold text-green-400 mb-3">Income</h4>
                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">${incomeForAccount.map(renderTransactionCard).join('')}</div>
                        </div>
                    `;
                }

                let expenseHTML = '';
                if (expensesForAccount.length > 0) {
                    expenseHTML = `
                        <div>
                            <h4 class="text-lg font-semibold text-red-400 mb-3">Expenses</h4>
                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">${expensesForAccount.map(renderTransactionCard).join('')}</div>
                        </div>
                    `;
                }
                
                let noTransactionsMessage = (incomeForAccount.length === 0 && expensesForAccount.length === 0) ? '<p class="text-center text-gray-400">No transactions for this account yet.</p>' : '';
                
                const accountSectionHTML = `
                    <div class="p-4 bg-gray-800 rounded-lg">
                        <h3 class="text-2xl font-bold mb-4 border-b border-gray-600 pb-2 flex justify-between items-center">
                            <span>${account.name}</span>
                            <span class="text-xl font-semibold">${account.balance >= 0 ? '₹' : '-₹'}${Math.abs(account.balance).toFixed(2)}</span>
                        </h3>
                        <div class="space-y-6">
                            ${incomeHTML}
                            ${expenseHTML}
                            ${noTransactionsMessage}
                        </div>
                    </div>
                `;
                transactionsByAccountContainer.innerHTML += accountSectionHTML;
            });

        } catch(error) {
            console.error("Error loading transactions:", error);
            transactionsByAccountContainer.innerHTML = `<p class="text-center text-red-400">${error.message}</p>`;
        }
    }

    // Load Accounts for the dropdown
    async function loadAccounts() {
        try {
            const res = await fetch('/api/accounts', { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token } });
            if (res.status === 401) {
                localStorage.removeItem("jwt_token");
                window.location.href = "/login";
                return;
            }
            const accounts = await res.json();
            if (res.ok) {
                const addSelect = document.getElementById('account_id_select');
                addSelect.innerHTML = '<option value="">-- Select Account --</option>';
                accounts.forEach(account => {
                    addSelect.innerHTML += `<option value="${account.id}">${account.name}</option>`;
                });
            } else {
                console.error("Accounts fetch failed:", accounts);
            }
        } catch (error) {
            console.error("Error loading accounts:", error);
        }
    }

    // Handle Transaction Form Submission
    document.getElementById('transactionsForm').addEventListener('submit', async function (event) {
        event.preventDefault();
        document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add("hidden"));
        
        const formData = new FormData(this);
        const transactionData = Object.fromEntries(formData.entries());
        
        try {
            const res = await fetch('/api/transactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(transactionData)
            });
            
            const data = await res.json();
            if (res.ok) {
                this.reset();
                transactionsFormContainer.classList.add('hidden');
                addTransactionBtn.textContent = "Add Transaction";
                loadTransactions(); // Reload the updated, grouped list
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

    // Initialize page
    window.onload = () => {
        loadAccounts();
        loadTransactions();
    };
  </script>
</body>
</html> --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom scrollbar for better aesthetics */
        .scrollbar-thin::-webkit-scrollbar {
            width: 8px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #2d3748; /* gray-800 */
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: #4a5568; /* gray-600 */
            border-radius: 20px;
            border: 3px solid #2d3748; /* gray-800 */
        }
    </style>
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
        <button id="addTransactionBtn" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg font-semibold">+ Add Transaction</button>
        
        <!-- *** NEW: Custom Warning Message *** -->
        <div id="customWarning" class="hidden fixed top-5 right-5 bg-red-800 border-l-4 border-red-500 text-white p-4 rounded-lg shadow-lg" role="alert">
            <div class="flex">
                <div class="py-1"><svg class="fill-current h-6 w-6 text-red-400 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zM11.414 10l2.829-2.828-1.415-1.415L10 8.586 7.172 5.757 5.757 7.172 8.586 10l-2.829 2.828 1.415 1.415L10 11.414l2.828 2.829 1.415-1.415L11.414 10z"/></svg></div>
                <div>
                    <p class="font-bold">Transaction Failed</p>
                    <p class="text-sm" id="customWarningMessage">Insufficient funds for this transaction.</p>
                </div>
                <button onclick="document.getElementById('customWarning').classList.add('hidden')" class="ml-4 text-red-300 hover:text-white">&times;</button>
            </div>
        </div>

        <!-- Transactions Form -->
        <div id="transactionsFormContainer" class="hidden bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-semibold text-center mb-6">Add Transaction</h2>
            <form id="transactionsForm" class="space-y-4">
                <div>
                    <input name="description" type="text" placeholder="Description (e.g., Starbucks)" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-description">Description is required.</p>
                </div>
                <div>
                    <input name="amount" type="number" step="0.01" placeholder="Amount" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-amount">Amount must be a valid number.</p>
                </div>
                <div>
                    <input name="date" type="date" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-date">Date is required.</p>
                </div>
                <div>
                    <select name="type" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                        <option value="">-- Select Type --</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-type">Type is required.</p>
                </div>
                <div>
                    <select name="category_id" id="category_id_select" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-category_id">Category is required.</p>
                </div>
                <div>
                    <select name="account_id" id="account_id_select" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                        <option value="">-- Select Account --</option>
                    </select>
                    <p class="text-red-400 text-sm mt-1 hidden" id="error-account_id">Account is required.</p>
                </div>
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 py-3 rounded-lg text-white font-medium">Save Transaction</button>
            </form>
        </div>

        <!-- Transactions List Container -->
        <div class="w-full max-w-6xl bg-gray-700/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center mb-6">Transactions by Account</h2>
            <div id="transactionsByAccount" class="space-y-8">
                <!-- Transaction data will be dynamically injected here -->
            </div>
        </div>
    </main>

  <script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
        window.location.href = window.location.origin + "/login";
    }

    document.getElementById("logoutBtn").addEventListener("click", async function () {
        localStorage.removeItem("jwt_token");
        window.location.href = window.location.origin + "/login";
    });

    // --- TOGGLE FORM ---
    let addTransactionBtn = document.getElementById("addTransactionBtn");
    let transactionsFormContainer = document.getElementById("transactionsFormContainer");

    addTransactionBtn.addEventListener("click", () => {
        transactionsFormContainer.classList.toggle("hidden");
        if (transactionsFormContainer.classList.contains("hidden")) {
            addTransactionBtn.textContent = "+ Add Transaction";
        } else {
            addTransactionBtn.textContent = "Close Form";
        }
    });

    const transactionsByAccountContainer = document.getElementById("transactionsByAccount");

    // Function to return a transaction card HTML string
    function renderTransactionCard(transaction) {
        const isIncome = transaction.type === 'income';
        const amountColor = isIncome ? 'text-green-400' : 'text-red-400';
        const cardBg = isIncome ? 'bg-green-900/50 border border-green-700' : 'bg-red-900/50 border border-red-700';
        
        return `
            <div id="transaction-${transaction.id}" class="p-4 rounded-lg shadow-md w-full flex flex-col justify-between ${cardBg}">
                <div>
                    <h3 class="text-lg font-semibold truncate">${transaction.description}</h3>
                    <p class="${amountColor} font-bold text-xl">₹${parseFloat(transaction.amount).toFixed(2)}</p>
                </div>
                <div class="text-gray-400 text-sm mt-2">
                    <p>Date: ${transaction.date}</p>
                    <p>Category: ${transaction.category ? transaction.category.name : "N/A"}</p>
                </div>
            </div>
        `;
    }

    // Load and display transactions grouped by account
    async function loadTransactions() {
        try {
            const res = await fetch('/api/transactions', {
                headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
            });
            const accounts = await res.json();
            
            if (!res.ok) {
                throw new Error(accounts.message || 'Failed to load transactions');
            }

            transactionsByAccountContainer.innerHTML = ""; // Clear previous content

            if (accounts.length === 0) {
                transactionsByAccountContainer.innerHTML = '<p class="text-center text-gray-400">No accounts found. Please add an account first.</p>';
                return;
            }

            accounts.forEach(account => {
                const incomeForAccount = account.transactions.filter(t => t.type === 'income');
                const expensesForAccount = account.transactions.filter(t => t.type === 'expense');

                let incomeHTML = '';
                if (incomeForAccount.length > 0) {
                    incomeHTML = `
                        <div>
                            <h4 class="text-lg font-semibold text-green-400 mb-3">Income</h4>
                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">${incomeForAccount.map(renderTransactionCard).join('')}</div>
                        </div>
                    `;
                }

                let expenseHTML = '';
                if (expensesForAccount.length > 0) {
                    expenseHTML = `
                        <div>
                            <h4 class="text-lg font-semibold text-red-400 mb-3">Expenses</h4>
                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">${expensesForAccount.map(renderTransactionCard).join('')}</div>
                        </div>
                    `;
                }
                
                let noTransactionsMessage = (incomeForAccount.length === 0 && expensesForAccount.length === 0) ? '<p class="text-center text-gray-400">No transactions for this account yet.</p>' : '';
                
                const accountSectionHTML = `
                    <div class="p-4 bg-gray-800 rounded-lg">
                        <h3 class="text-2xl font-bold mb-4 border-b border-gray-600 pb-2 flex justify-between items-center">
                            <span>${account.name}</span>
                            <span class="text-xl font-semibold">${account.balance >= 0 ? '₹' : '-₹'}${Math.abs(account.balance).toFixed(2)}</span>
                        </h3>
                        <div class="space-y-6">
                            ${incomeHTML}
                            ${expenseHTML}
                            ${noTransactionsMessage}
                        </div>
                    </div>
                `;
                transactionsByAccountContainer.innerHTML += accountSectionHTML;
            });

        } catch(error) {
            console.error("Error loading transactions:", error);
            transactionsByAccountContainer.innerHTML = `<p class="text-center text-red-400">${error.message}</p>`;
        }
    }

    // Load Accounts for the dropdown
    async function loadAccounts() {
        try {
            const res = await fetch('/api/accounts', { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token } });
            if (res.status === 401) {
                localStorage.removeItem("jwt_token");
                window.location.href = window.location.origin + "/login";
                return;
            }
            const accounts = await res.json();
            if (res.ok) {
                const addSelect = document.getElementById('account_id_select');
                addSelect.innerHTML = '<option value="">-- Select Account --</option>';
                accounts.forEach(account => {
                    addSelect.innerHTML += `<option value="${account.id}">${account.name}</option>`;
                });
            } else {
                console.error("Accounts fetch failed:", accounts);
            }
        } catch (error) {
            console.error("Error loading accounts:", error);
        }
    }

    // Handle Transaction Form Submission
    document.getElementById('transactionsForm').addEventListener('submit', async function (event) {
        event.preventDefault();
        document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add("hidden"));
        
        const formData = new FormData(this);
        const transactionData = Object.fromEntries(formData.entries());
        
        try {
            const res = await fetch('/api/transactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(transactionData)
            });
            
            const data = await res.json();
            if (res.ok) {
                this.reset();
                transactionsFormContainer.classList.add('hidden');
                addTransactionBtn.textContent = "Add Transaction";
                loadTransactions(); // Reload the updated, grouped list
            } else {
                // Check for our custom insufficient funds error
                if (res.status === 422 && data.message && data.message.includes('Insufficient funds')) {
                    document.getElementById('customWarningMessage').textContent = data.message;
                    document.getElementById('customWarning').classList.remove('hidden');
                } else if (data.errors) {
                    // Handle standard validation errors
                    for (let field in data.errors) {
                        let errorEl = document.getElementById(`error-${field}`);
                        if (errorEl) {
                            errorEl.textContent = data.errors[field][0];
                            errorEl.classList.remove("hidden");
                        }
                    }
                } else {
                    // Generic fallback error
                    document.getElementById('customWarningMessage').textContent = data.message || 'An unknown error occurred.';
                    document.getElementById('customWarning').classList.remove('hidden');
                }
            }
        } catch (error) {
            console.error("Error adding transaction:", error);
            document.getElementById('customWarningMessage').textContent = "Error adding transaction.";
            document.getElementById('customWarning').classList.remove('hidden');
        }
    });

    // Initialize page
    window.onload = () => {
        loadAccounts();
        loadTransactions();
    };
  </script>
</body>
</html>

