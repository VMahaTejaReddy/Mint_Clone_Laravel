<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex justify-center items-center p-6">

    <div class="w-full max-w-4xl space-y-8">

        <!-- Accounts Form -->
        <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center mb-6">Add Account</h2>

            <form id="accountForm" class="space-y-4" method="POST" action=" {{ route('accounts.store') }}">
                @csrf

                <input type="text" name="name" id="name" placeholder="Account Name (e.g., HDFC Bank)" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
                <select name="type" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option>Savings Account</option>
                    <option>Current Account</option>
                    <option>Credit Card</option>
                    <option>Wallet</option>
                </select>
                <input type="number" name="balance" id="balance" placeholder="Balance" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 py-3 rounded-lg text-white font-medium">Save Account</button>
            </form>
        </div>
    </div>
    <div>
        <h2 class="text-2xl font-semibold text-center mb-6">Accounts List</h2>
        <div id="accountsList" class="space-y-4">
            <!-- Accounts will be dynamically loaded here -->

        </div>
    </div>

        

</body>

</html><script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
        window.location.href = "/login";
    }

    const accountsList = document.getElementById('accountsList');

    // Function to render account in DOM
    function renderAccount(account) {
        accountsList.innerHTML += `
            <div class="bg-gray-700 p-4 rounded-lg">
                <h3 class="text-lg font-semibold">${account.name}</h3>
                <p>Type: ${account.type}</p>
                <p>Balance: ${account.balance}</p>
            </div>
        `;
    }

    // ✅ Load all accounts when page loads
    async function loadAccounts() {
        try {
            let res = await fetch('/api/accounts', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });

            let data = await res.json();

            if (res.ok) {
                accountsList.innerHTML = ""; // clear before loading
                data.forEach(account => renderAccount(account));
            } else {
                alert("Failed to load accounts: " + JSON.stringify(data));
            }
        } catch (error) {
            console.error("Error loading accounts", error);
        }
    }

    // ✅ Handle form submit (add account)
    document.getElementById('accountForm').addEventListener('submit', async function(event){
        event.preventDefault();

        let accountData = {
            name: document.getElementById('name').value,
            type: document.querySelector('select[name="type"]').value,
            balance: document.getElementById('balance').value
        }

        let res = await fetch('/api/accounts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(accountData)
        });

        let data = await res.json();

        if (res.ok) {
            alert("Account created successfully!");
            renderAccount(data);
            document.getElementById('accountForm').reset(); // clear form
        } else {
            alert('Account creation failed: '+JSON.stringify(data));
        }
    });

    // Call loadAccounts() on page load
    window.onload = loadAccounts;
</script>
