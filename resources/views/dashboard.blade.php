@extends('layouts.app')

@section('title', 'Rule Engine - login')

@section('layout.sidebar')
    @parent

@endsection

@section('content')
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-error">
                {{ session()->get('error') }}
            </div>
        @endif
        <h4>Fund transfer (get email address from <a href="{{route('users')}}" target="_blank" class="underline">here</a>)
    <form class="space-y-6" action="{{route('tranfer')}}" method="POST">
        @csrf
        <div>
        <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
        <div class="mt-2">
            <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
        </div>
        <label for="amt" class="block text-sm/6 font-medium text-gray-900">Amount</label>
        <div class="mt-2">
            <input id="amt" name="amt" type="text" autocomplete="amt" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
        </div>
        </div>
        <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Transfer</button>
        </div>
    </form>
    </div>
</div>

@endsection
