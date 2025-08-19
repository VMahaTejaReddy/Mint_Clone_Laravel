{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budgets</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex justify-center items-center p-6">

        <!-- Budgets Form -->
        <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg">
            <h2 class="text-2xl font-semibold text-center mb-6">Create Budget</h2>
            <form id="budgetsForm" class="space-y-4" method="POST" action="{{ route('budgets.store') }}">
                @csrf
                <select class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-purple-500 outline-none">
                    <option value="">-- Select Category --</option>
                    @if(isset($categories) && !$categories->isEmpty())
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    @endif
                </select>
                <input type="number" name="amount" placeholder="Budget Amount" class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-purple-500 outline-none">
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 py-3 rounded-lg text-white font-medium">Save Budget</button>
            </form>
        </div>

        <div class="w-full max-w-4xl space-y-8 mt-8">
            <h2 class="text-2xl font-semibold text-center mb-6">Budgets List</h2>
            <div id="budgetsList" class="space-y-4">
                <!-- Budgets will be dynamically loaded here -->
        </div>
</body>
<script>
        const token = localStorage.getItem("jwt_token");
        if (!token) {
            window.location.href = "/login";
        }

    // Add your custom JavaScript here
        document.getElementById('budgetsForm').addEventListener('submit', async function(event){
            event.preventDefault();

            let budgetData = {
                category: document.querySelector('select[name="category"]').value,
                amount: document.querySelector('input[name="amount"]').value
            }

            let res = await fetch('/api/budgets', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem("jwt_token")
                },
                body: JSON.stringify(budgetData)
            });

            let data = await res.json();

            if (res.ok) {
                alert("Budget created successfully!");
                document.getElementById('budgetsList').innerHTML += `
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold">${data.category}</h3>
                        <p>Amount: ${data.amount}</p>
                    </div>
                `;
            } else {
                alert('Budget creation failed: '+JSON.stringify(data));
            }
        });
</script>
</html> --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budgets</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col items-center p-6">

    <!-- Budgets Form -->
    <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-semibold text-center mb-6">Create Budget</h2>

        <form id="budgetsForm" class="space-y-4" method="POST" action="{{ route('budgets.store') }}">
            @csrf

            <!-- Category Dropdown -->
            <select 
                name="category_id"
                id="category_id"
                class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-purple-500 outline-none"
                required
            >
                <option value="">-- Select Category --</option>
                @if(isset($categories) && !$categories->isEmpty())
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                @endif
            </select>

            <!-- Budget Amount -->
            <input 
                type="number" 
                name="amount" 
                placeholder="Budget Amount" 
                class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-purple-500 outline-none"
                required
            >

            <!-- Submit -->
            <button 
                type="submit" 
                class="w-full bg-purple-600 hover:bg-purple-700 py-3 rounded-lg text-white font-medium">
                Save Budget
            </button>
        </form>
    </div>

    <!-- Budgets List -->
    <div class="w-full max-w-4xl space-y-8 mt-8">
        <h2 class="text-2xl font-semibold text-center mb-6">Budgets List</h2>
        <div id="budgetsList" class="space-y-4">
            <!-- Budgets will be dynamically loaded here -->
        </div>
    </div>

<script>
        const token = localStorage.getItem("jwt_token");
        if (!token) {
            window.location.href = "/login";
        }

        const budgetsList = document.getElementById("budgetsList");

        // ✅ Render single budget card
        function renderBudget(budget) {
            budgetsList.innerHTML += `
                <div class="bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold">${budget.category?.name ?? 'Unknown Category'}</h3>
                    <p>Amount: ${budget.amount}</p>
                </div>
            `;
        }

        // ✅ Load all budgets on page load
        async function loadBudgets() {
            try {
                let res = await fetch('/api/budgets', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    }
                });

                let data = await res.json();

                if (res.ok) {
                    budgetsList.innerHTML = ""; // clear old list
                    data.forEach(budget => renderBudget(budget));
                } else {
                    alert("Failed to load budgets: " + JSON.stringify(data));
                }
            } catch (error) {
                console.error("Error loading budgets", error);
            }
        }

        // ✅ Handle form submission (Add new budget)
        document.getElementById('budgetsForm').addEventListener('submit', async function (event) {
            event.preventDefault();

            let budgetData = {
                category_id: document.querySelector('select[name="category_id"]').value,
                amount: document.querySelector('input[name="amount"]').value
            };

            let res = await fetch('/api/budgets', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(budgetData)
            });

            let data = await res.json();

            if (res.ok) {
                alert("Budget created successfully!");
                renderBudget(data);
                document.getElementById('budgetsForm').reset();
            } else {
                alert('Budget creation failed: ' + JSON.stringify(data));
            }
        });

        // Call loadBudgets() when page loads
        window.onload = loadBudgets;
    </script>

</body>
</html>
