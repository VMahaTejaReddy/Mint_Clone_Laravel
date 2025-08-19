<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 flex items-center justify-center h-screen">
  <div class="w-full max-w-md bg-gray-800 p-8 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

    <form id="loginForm" class="space-y-4" action="/api/login" method="POST">
        @csrf

      <input type="email" id="login_email" name="email" placeholder="Enter email"
        class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-100 focus:outline-none focus:ring focus:ring-indigo-500">

      <input type="password" id="login_password" name="password" placeholder="Enter Password"
        class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-100 focus:outline-none focus:ring focus:ring-indigo-500">

      <button type="submit"
        class="w-full bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg font-semibold">Login</button>

    </form>

    <p class="mt-4 text-sm text-center">
      Don’t have an account? <a href="/" class="text-indigo-400 hover:underline">Register</a>
    </p>
  </div>

  <script>
        localStorage.removeItem("jwt_token");

        document.getElementById('loginForm').addEventListener('submit', async function(event){
            event.preventDefault();

            let credentials = {
                email: document.getElementById('login_email').value,
                password: document.getElementById('login_password').value
            }

            let res = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(credentials)
            });

            let data = await res.json();

            if (res.ok) {
                alert("Login successful!");
                localStorage.setItem("jwt_token", data.token);
                window.location.href = "/dashboard";
            } else {
                alert('Login failed: '+JSON.stringify(data));
            }
        });
    </script>

</body>
</html>
