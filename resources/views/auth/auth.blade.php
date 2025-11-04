<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login & Register</title>
<style>
body{font-family:sans-serif;background:#f0f0f0;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}
.auth-container{background:#fff;padding:30px;border-radius:15px;width:400px;box-shadow:0 4px 15px rgba(0,0,0,0.2);}
.tabs{display:flex;justify-content:space-around;margin-bottom:20px;}
.tab{cursor:pointer;padding-bottom:5px;border-bottom:2px solid transparent;font-weight:600;}
.tab.active{color:#7c3aed;border-color:#7c3aed;}
form{display:none;}form.active{display:block;}
input,select,button{width:100%;padding:10px;margin-bottom:12px;border-radius:8px;}
button{background:#7c3aed;color:#fff;border:none;cursor:pointer;}
.error{color:#e11d48;font-size:13px;margin-bottom:10px;}
.success{color:green;font-size:13px;margin-bottom:10px;}
</style>
</head>
<body>
<div class="auth-container">
<div class="tabs">
<div class="tab active" onclick="showForm('login')">Login</div>
<div class="tab" onclick="showForm('register')">Register</div>
</div>

@if(session('success'))
<p class="success">{{ session('success') }}</p>
@endif
@if($errors->any())
<p class="error">{{ $errors->first() }}</p>
@endif

<form id="login" class="active" method="POST" action="{{ route('auth.login') }}">
@csrf
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>

<form id="register" method="POST" action="{{ route('auth.register') }}">
@csrf
<input type="text" name="full_name" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<input type="password" name="password_confirmation" placeholder="Confirm Password" required>
<select name="role" required>
<option value="">-- Select Role --</option>
<option value="client">Client</option>
<option value="artist">Artist</option>
</select>
<button type="submit">Register</button>
</form>
</div>

<script>
function showForm(type){
document.querySelectorAll('form').forEach(f=>f.classList.remove('active'));
document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
document.getElementById(type).classList.add('active');
document.querySelector(`.tab[onclick="showForm('${type}')"]`).classList.add('active');
}
</script>
</body>
</html>
