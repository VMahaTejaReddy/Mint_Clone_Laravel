<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF--8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accounts</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col items-center p-6">

  <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md h-[350px] flex flex-col justify-between">
    <h2 class="text-2xl font-semibold text-center mb-2">Add Account</h2>
    <form id="accountsForm" class="space-y-3 flex-1 flex flex-col justify-center" method="POST" action="{{ route('accounts.store') }}">
      @csrf
      <input type="text" name="name" placeholder="Account Name (e.g., Savings)"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
      <input type="number" name="balance" placeholder="Balance"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
      <select name="type"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
        <option value="">Select Type</option>
        <option value="Savings">Savings Account</option>
        <option value="Checking">Current Account</option>
        <option value="Credit">Credit Card</option>
      </select>

      <p class="text-sm text-gray-400 mt-6 text-center">
      <a href="/dashboard" class="text-indigo-400 hover:underline">← Back to Dashboard</a>
    </p>

    <button type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 py-3 rounded-lg text-white font-medium">Save Account</button>
    </form>
  </div>

  <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-4xl mt-10">
    <h2 class="text-2xl font-semibold text-center mb-6">Accounts List</h2>
    <div id="accountsList"
      class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[400px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
      
      @foreach ($accounts as $account)
        <div class="bg-gray-700 p-4 rounded-lg shadow-md w-full h-[150px] flex flex-col justify-between">
          <div>
            <h3 class="text-lg font-semibold truncate">{{ $account->name }}</h3>
            <p>Balance: <span class="font-medium">₹{{ $account->balance }}</span></p>
            <p>Type: {{ $account->type }}</p>
          </div>
          <div class="flex justify-end space-x-2">
            <a href="{{ route('accounts.edit', ['id' => $account->id]) }}" class="text-sm bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded">Edit</a>
            <form action="{{ route('accounts.destroy', ['id' => $account->id]) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded">Delete</button>
            </form>
          </div>
        </div>
      @endforeach
      
    </div>
  </div>

</body>
</html>