<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex justify-center items-center p-6">

        <!-- Transactions Form -->
        <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center mb-6">Add Transaction</h2>
            <form id="transactionsForm" class="space-y-4" method="POST" action="{{ route('transactions.store') }}">
                @csrf
                <input name="description" type="text" placeholder="Description (e.g., Starbucks)" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                <input name="amount" type="number" placeholder="Amount" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                <input name="date" type="date" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                <select name="category_id" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-orange-500 outline-none">
                    <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                </select>
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 py-3 rounded-lg text-white font-medium">Save Transaction</button>
            </form>
        </div>
        <div class="w-full max-w-4xl space-y-8 mt-8">
            <h2 class="text-2xl font-semibold text-center mb-6">Transactions List</h2>
            <div id="transactionsList" class="space-y-4">
                <!-- Transactions will be dynamically loaded here -->
        </div>

</body>
<script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
        window.location.href = "/login";
    }

    // Handle Transaction Form Submit
    document.getElementById('transactionsForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        let transactionData = {
            account_id: document.querySelector('select[name="account_id"]').value,
            category_id: document.querySelector('select[name="category_id"]').value,
            description: document.querySelector('input[name="description"]').value,
            amount: document.querySelector('input[name="amount"]').value,
            date: document.querySelector('input[name="date"]').value
        };

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
            alert("Transaction added successfully!");

            // Append new transaction dynamically
            document.getElementById('transactionsList').innerHTML += `
                <div class="bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold">${data.description}</h3>
                    <p>Amount: ₹${data.amount}</p>
                    <p>Date: ${data.date}</p>
                    <p>Category: ${data.category}</p>
                </div>
            `;

            // Reset form
            document.getElementById('transactionsForm').reset();
        } else {
            alert('Transaction failed: ' + JSON.stringify(data));
        }
    });

    // Load Transactions List on Page Load
    async function loadTransactions() {
        let res = await fetch('/api/transactions', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        let transactions = await res.json();

        if (res.ok) {
            let list = document.getElementById('transactionsList');
            list.innerHTML = "";

            transactions.forEach(tx => {
                list.innerHTML += `
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold">${tx.description}</h3>
                        <p>Amount: ₹${tx.amount}</p>
                        <p>Date: ${tx.date}</p>
                        <p>Category: ${tx.category}</p>
                    </div>
                `;
            });
        }
    }

    // Call loadTransactions when page loads
    window.onload = loadTransactions;
</script>


</html>