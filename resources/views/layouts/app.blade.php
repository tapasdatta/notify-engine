<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="">
        <header class="bg-white">
          <nav class="mx-auto flex max-w-7xl items-center justify-between gap-x-6 p-6 lg:px-8" aria-label="Global">
            <div class="hidden lg:flex lg:gap-x-12">
                @auth
                    <a href="#" class="text-sm/6 font-semibold text-gray-900">Product</a>
                    <a href="#" class="text-sm/6 font-semibold text-gray-900">Features</a>
                    <a href="#" class="text-sm/6 font-semibold text-gray-900">Marketplace</a>
                    <a href="#" class="text-sm/6 font-semibold text-gray-900">Company</a>
                @endauth
            </div>
            <div class="flex flex-1 items-center justify-end gap-x-6">

               @auth <a href="{{route('logout')}}">Logout</a> @else <a href="{{route('login')}}">Log in</a> @endauth
            </div>
          </nav>
        </header>
        <div class="container">
            @yield('content')
        </div>
    </body>
</html>
