<x-app-layout>
    <div class="relative bg-sky-700 text-white h-64 flex items-center justify-center bg-cover bg-center"
        style="background-image: url('assets/images/page-banner.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-4xl font-bold mb-2">Register</h2>
            <ul class="flex justify-center space-x-2 text-sm">
                <li><a href="{{ route('home.index') }}" class="hover:text-primary">Home</a></li>
                <li>/</li>
                <li class="text-primary">Register</li>
            </ul>
        </div>
    </div>

    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="flex justify-center">
                <div class="w-full lg:w-1/2">
                    <div class="bg-gray-50 border rounded-lg p-8 shadow-sm">
                        <h4 class="text-2xl font-bold text-center mb-2">Create New Account</h4>
                        <p class="text-center text-sm mb-8 text-gray-500">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-primary hover:underline">Log in instead!</a>
                        </p>

                        <form method="POST" action="{{ route('register') }}" class="space-y-4">
                            @csrf
                            <div>
                                <input type="text" placeholder="Name" id="name" name="name"
                                    class="w-full border p-3 rounded focus:outline-none focus:border-primary transition"
                                    :value="old('name')" required autofocus autocomplete="name" />
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <input type="email" placeholder="Email Address *" id="email" name="email"
                                    class="w-full border p-3 rounded focus:outline-none focus:border-primary transition"
                                    :value="old('email')" required autofocus autocomplete="username" />
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <input type="text" placeholder="Mobile" id="mobile" name="mobile"
                                    class="w-full border p-3 rounded focus:outline-none focus:border-primary transition"
                                    value="{{ old('mobile') }}" required autocomplete="tel" />
                                @error('mobile')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <input type="password" placeholder="Password" id="password" name="password"
                                    class="w-full border p-3 rounded focus:outline-none focus:border-primary transition"
                                    required autocomplete="new-password" />
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <input type="password" placeholder="Confirm Password" id="password_confirmation"
                                    name="password_confirmation"
                                    class="w-full border p-3 rounded focus:outline-none focus:border-primary transition"
                                    required autocomplete="new-password" />
                            </div>

                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full bg-primary text-white font-medium py-3 rounded hover:bg-blue-600 transition shadow-lg">
                                    Register
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>