<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Account</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col items-center p-6">

  <div class="bg-gray-800/90 backdrop-blur-lg p-6 rounded-2xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-semibold text-center mb-6">Edit Account</h2>
    
    <form id="editAccountForm" class="space-y-4" method="POST" action="{{ route('accounts.update', ['id' => $account->id]) }}">
      @csrf
      @method('PUT') <input type="text" name="name" placeholder="Account Name (e.g., Savings)" value="{{ $account->name }}"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
        
      <input type="number" name="balance" placeholder="Balance" value="{{ $account->balance }}"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
        
      <select name="type"
        class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
        <option value="">Select Type</option>
        <option value="Savings" {{ $account->type == 'Savings' ? 'selected' : '' }}>Savings Account</option>
        <option value="Checking" {{ $account->type == 'Checking' ? 'selected' : '' }}>Current Account</option>
        <option value="Credit" {{ $account->type == 'Credit' ? 'selected' : '' }}>Credit Card</option>
      </select>

    <p class="text-sm text-gray-400 mt-6 text-center">
      <a href="{{ route('accounts') }}" class="text-indigo-400 hover:underline">← Back to Accounts</a>
    </p>

    <button type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 py-3 rounded-lg text-white font-medium">Update Account</button>
    </form>
  </div>

</body>
</html>