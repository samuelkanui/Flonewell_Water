    <!-- Company Header -->
<div class="text-center mb-10 relative">
    <div class="inline-block">
        <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-800 to-blue-600 tracking-tight animate-slide-in-down">
            Fynergy Water
        </h1>
        <div class="h-1 w-24 bg-gradient-to-r from-blue-500 to-blue-700 mx-auto mt-2 rounded-full animate-scale-in"></div>
    </div>
    <p class="text-blue-600 mt-3 text-lg font-medium animate-fade-in delay-200">Water Management System</p>
</div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6 relative">
        @csrf

        <!-- Welcome Message -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800 animate-fadeIn delay-100">Welcome Back!</h2>
            <p class="text-gray-600 mt-2">Please sign in to your account</p>
        </div>

        <!-- Email Address -->
        <div class="space-y-2 animate-slideInRight delay-200">
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <div class="relative rounded-lg shadow-md group">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-500 transition-colors group-focus-within:text-blue-600">
                    <i class="fas fa-envelope text-lg"></i>
                </span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="block w-full pl-10 pr-3 py-3.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 text-sm transition-all duration-200 bg-white/80 backdrop-blur-sm hover:border-blue-400"
                    placeholder="Enter your email address">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="space-y-2 animate-slideInLeft delay-300">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="relative rounded-lg shadow-md group">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-500 transition-colors group-focus-within:text-blue-600">
                    <i class="fas fa-lock text-lg"></i>
                </span>
                <input type="password" id="password" name="password" required
                    class="block w-full pl-10 pr-3 py-3.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 text-sm transition-all duration-200 bg-white/80 backdrop-blur-sm hover:border-blue-400"
                    placeholder="Enter your password">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mt-6 animate-slideInUp delay-400">
            <div class="flex items-center">
                <input type="checkbox" id="remember_me" name="remember"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors duration-200 cursor-pointer">
                <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer hover:text-blue-600 transition-colors duration-200">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors duration-200 hover:underline">
                    Forgot your password?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div class="mt-8 animate-slideInUp delay-500">
            <button type="submit"
                class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-blue-500/50 gap-2">
                Login
                
            </button>
        </div>
    </form>

    
</div>
<style>
/* General Styles */
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(to bottom, #e0f7ff, #f0f8ff);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

/* Container */
form {
    max-width: 500px;
   
    padding: 100px 100px;
    border-radius: 30px;
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
    margin-top: 10px;
    margin-left: 0;

}

form:hover {
    transform: translateY(-5px);
}

/* Company Header */
.text-center h1 {
    font-size: 3.5rem;
    font-weight: bold;
    background: linear-gradient(to right, #0033cc, #0055ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 1px;
    margin-right: 40px;
}

.text-center p {
    color: #0033cc;
    font-size: 1.1rem;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.animate-slide-in-down {
    animation: slideInDown 0.8s ease-out forwards;
}

.animate-scale-in {
    animation: scaleIn 0.6s ease-out forwards;
}

.animate-fade-in {
    animation: fadeIn 1s ease-out forwards;
}

/* Input Fields */
input {
    width: 100%;
    padding: 12px;
    padding-left: 40px;
    border: 2px solid #ccc;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    font-size: 1rem;
}

input:focus {
    border-color: #0055ff;
    box-shadow: 0px 0px 8px rgba(0, 85, 255, 0.4);
    outline: none;
}

/* Icons Inside Inputs */
.group {
    position: relative;
}

.group i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #0055ff;
    font-size: 1.2rem;
    transition: color 0.3s ease-in-out;
}

.group:focus-within i {
    color: #ff3b30;
}

/* Remember Me & Forgot Password */
label {
    font-size: 0.95rem;
    color: #333;
}

input[type="checkbox"] {
    accent-color: #0055ff;
    width: 16px;
    height: 16px;
}

/* Forgot Password */
a {
    color: #0055ff;
    text-decoration: none;
    transition: color 0.3s ease-in-out;
}

a:hover {
    color: #ff3b30;
    text-decoration: underline;
}

/* Button */
button {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px 50px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: white;
    background: linear-gradient(to right, #2563eb, #1e40af);
    transition: all 0.2s ease-in-out;
    box-shadow: 0px 4px 6px rgba(0, 0, 255, 0.3);
}

button:hover {
    background: linear-gradient(to right,rgb(216, 113, 29), #1e3a8a);
    transform: scale(1.02);
    box-shadow: 0px 6px 10px rgba(0, 247, 255, 0.4);
}

button:active {
    transform: scale(0.98);
}

button:focus {
    outline: none;
    box-shadow: 0px 0px 8px rgba(0, 0, 255, 0.5);
}

/* Footer */

</style>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
