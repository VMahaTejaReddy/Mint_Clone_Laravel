<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bills</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex justify-center items-center p-6">

        <!-- Bills Form -->
        <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center mb-6">Add Bill</h2>
            <form id="billsForm" class="space-y-4" method="POST" action="{{ route('bills.store') }}">
                @csrf
                <input type="text" name="name" placeholder="Bill Name (e.g., Electricity)" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
                <input type="number" name="amount" placeholder="Amount" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
                <input type="date" name="due_date" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 py-3 rounded-lg text-white font-medium">Save Bill</button>
            </form>
        </div>
        <div class="w-full max-w-4xl space-y-8 mt-8">
            <h2 class="text-2xl font-semibold text-center mb-6">Bills List</h2>
            <div id="billsList" class="space-y-4">
                <!-- Bills will be dynamically loaded here -->
        </div>

</body>
{{-- <script>
        const token = localStorage.getItem("jwt_token");
        if (!token) {
            window.location.href = "/login";
        }

    // Add your custom JavaScript here
    document.getElementById('billsForm').addEventListener('submit', async function(event){
            event.preventDefault();

            let billData = {
                name: document.querySelector('input[name="name"]').value,
                amount: document.querySelector('input[name="amount"]').value,
                due_date: document.querySelector('input[name="due_date"]').value
            }

            let res = await fetch('/api/bills', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem("jwt_token")
                },
                body: JSON.stringify(billData)
            });

            let data = await res.json();

            if (res.ok) {
                alert("Bill created successfully!");
                document.getElementById('billsList').innerHTML += `
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold">${data.name}</h3>
                        <p>Amount: ${data.amount}</p>
                        <p>Due Date: ${data.due_date}</p>
                    </div>
                `;
            } else {
                alert('Bill creation failed: '+JSON.stringify(data));
            }
        });
</script> --}}
<script>
        const token = localStorage.getItem("jwt_token");
        if (!token) {
            window.location.href = "/login";
        }

        const billsList = document.getElementById("billsList");

        // Function to render a single bill card
        function renderBill(bill) {
            billsList.innerHTML += `
                <div class="bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold">${bill.name}</h3>
                    <p>Amount: ${bill.amount}</p>
                    <p>Due Date: ${bill.due_date}</p>
                </div>
            `;
        }

        // ✅ Load all bills on page load
        async function loadBills() {
            try {
                let res = await fetch('/api/bills', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    }
                });

                let data = await res.json();

                if (res.ok) {
                    billsList.innerHTML = ""; // clear before adding
                    data.forEach(bill => renderBill(bill));
                } else {
                    alert("Failed to load bills: " + JSON.stringify(data));
                }
            } catch (error) {
                console.error("Error loading bills", error);
            }
        }

        // ✅ Handle form submission (Add new bill)
        document.getElementById('billsForm').addEventListener('submit', async function (event) {
            event.preventDefault();

            let billData = {
                name: document.querySelector('input[name="name"]').value,
                amount: document.querySelector('input[name="amount"]').value,
                due_date: document.querySelector('input[name="due_date"]').value
            };

            let res = await fetch('/api/bills', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(billData)
            });

            let data = await res.json();

            if (res.ok) {
                alert("Bill created successfully!");
                renderBill(data);
                document.getElementById('billsForm').reset();
            } else {
                alert('Bill creation failed: ' + JSON.stringify(data));
            }
        });

        // Call loadBills() when page loads
        window.onload = loadBills;
    </script>


</html>