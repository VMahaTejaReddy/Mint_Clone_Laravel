<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goals</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex justify-center items-center p-6">

        <!-- Goals Form -->
        <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center mb-6">Set Goal</h2>
            <form id="goalsForm" class="space-y-4" method="POST" action="{{ route('goals.store') }}">
                @csrf
                <input id="name" type="text" name="name" placeholder="Goal Name (e.g., Buy a Car)" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-pink-500 outline-none">
                <input id="target_amount" type="number" name="target_amount" placeholder="Target Amount" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-pink-500 outline-none">
                <input id="current_amount" type="number" name="current_amount" placeholder="Current Amount" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-pink-500 outline-none">
                {{-- <input type="date" name="due_date" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-pink-500 outline-none"> --}}
                <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 py-3 rounded-lg text-white font-medium">Save Goal</button>
            </form>
        </div>
        <div class="w-full max-w-4xl space-y-8 mt-8">
            <h2 class="text-2xl font-semibold text-center mb-6">Goals List</h2>
            <div id="goalsList" class="space-y-4">
                <!-- Goals will be dynamically loaded here -->
            </div>

</body>
{{-- <script>
        const token = localStorage.getItem("jwt_token");
        if (!token) {
            window.location.href = "/login";
        }

    // Add your custom JavaScript here
    document.getElementById('goalsForm').addEventListener('submit', async function(event){
            event.preventDefault();

            let goalData = {
                name: document.getElementById('name').value,
                target_amount: document.getElementById('target_amount').value,
                current_amount: document.getElementById('current_amount').value
            }

            let res = await fetch('/api/goals', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem("jwt_token")
                },
                body: JSON.stringify(goalData)
            });

            let data = await res.json();

            if (res.ok) {
                alert("Account created successfully!");
                document.getElementById('goalsList').innerHTML += `
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold">${data.name}</h3>
                        <p>Target Amount: ${data.target_amount}</p>
                        <p>Current Amount: ${data.current_amount}</p>
                    </div>
                `;
            } else {
                alert('Account creation failed: '+JSON.stringify(data));
            }
        });
</script> --}}
<script>
    const token = localStorage.getItem("jwt_token");
    if (!token) {
        window.location.href = "/login";
    }

    // ✅ Load all goals on page load
    async function loadGoals() {
        let res = await fetch('/api/goals', {
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        let data = await res.json();

        if (res.ok) {
            let goalsList = document.getElementById('goalsList');
            goalsList.innerHTML = ""; // clear old list
            data.forEach(goal => {
                goalsList.innerHTML += `
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold">${goal.name}</h3>
                        <p>Target Amount: ${goal.target_amount}</p>
                        <p>Current Amount: ${goal.current_amount}</p>
                    </div>
                `;
            });
        } else {
            alert('Failed to load goals: ' + JSON.stringify(data));
        }
    }

    // ✅ Handle form submission
    document.getElementById('goalsForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        let goalData = {
            name: document.querySelector('input[name="name"]').value,
            target_amount: document.querySelector('input[name="target_amount"]').value,
            current_amount: document.querySelector('input[name="current_amount"]').value
        };

        let res = await fetch('/api/goals', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(goalData)
        });

        let data = await res.json();

        if (res.ok) {
            alert("Goal created successfully!");
            document.getElementById('goalsForm').reset();
            loadGoals(); // reload goals list
        } else {
            alert('Goal creation failed: ' + JSON.stringify(data));
        }
    });

    // ✅ Call loadGoals when page loads
    window.onload = loadGoals;
</script>



</html>