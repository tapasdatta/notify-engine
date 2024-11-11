@extends('layouts.app')

@section('title', 'Rule Engine - login')

@section('layout.sidebar')
    @parent

@endsection

@section('content')
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <h2>Rules</h2>
        <ul role="list" class="divide-y divide-dark/5">
            @if($rules)
                @foreach ($rules as $rule)
                <li class="relative flex items-center space-x-4 py-4">
                <div class="min-w-0 flex-auto">
                    <div class="flex items-center gap-x-3">
                    <div class="flex-none rounded-full bg-green-400/10 p-1 text-green-400">
                        <div class="h-2 w-2 rounded-full bg-current"></div>
                    </div>
                    <h2 class="min-w-0 text-sm/6 font-semibold text-dark">
                        <div href="#" class="flex gap-x-2">
                        <span class="truncate">{{ $rule['type'] }}</span>
                        <span class="text-gray-400">/</span>
                        <span class="whitespace-nowrap">{{ $rule['action']['type'] }}</span>
                        </div>
                    </h2>
                    </div>
                    <div class="mt-3 flex items-center gap-x-2.5 text-xs/5 text-gray-400">
                    <p class="whitespace-nowrap">{{ $rule['name'] }}</p>
                    </div>
                </div>
                <div class="flex-none z-auto rounded-full bg-green-400/10 px-2 py-1 text-xs font-medium text-gray-400 ring-1 ring-inset ring-gray-400/20">
                    <form action="{{route('rule.destroy')}}" method="POST">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="type" value="{{ $rule['type'] }}">
                        <button type="submit">Delete</button>
                    </form>
                </div>
                </li>
                @endforeach
            @else
                <p>No rules setup!</p>
            @endif
        </ul>
        @if($errors->any())
          {!! implode('', $errors->all('<li class="alert alert-danger">:message</li>')) !!}
        @endif
        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif
        <form class="space-y-6" action="{{route('rule.store')}}" method="POST">
            @csrf
            <div>
                <label for="name" class="block text-sm/6 font-medium text-gray-900">Rule Name</label>
                <div class="mt-2">
                    <input id="name" name="name" type="name" autocomplete="name" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                </div>
                <div class="mt-2">
                  <label for="type" class="block text-sm/6 font-medium text-gray-900">Rule Type</label>
                  <select id="type" name="type" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm/6">
                    <option value="inactivity">Login Inactivity</option>
                    <option value="transaction_limit">Transaction Limit</option>
                    <option value="transaction_threshold">Transaction Threshold</option>
                  </select>
                </div>
                <label for="conditions[days_inactive]" class="block text-sm/6 font-medium text-gray-900">Days of inactive</label>
                <div class="mt-2">
                    <input id="conditions[days_inactive]" name="conditions[days_inactive]" type="conditions[days_inactive]" autocomplete="inactivity" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                </div>
                <div class="mt-2">
                  <label for="action[type]" class="block text-sm/6 font-medium text-gray-900">Channel</label>
                  <select id="action[type]" name="action[type]" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm/6">
                    <option value="email" selected>Email</option>
                    <option value="sms">SMS</option>
                  </select>
                </div>
                <div class="mt-2">
                  <label for="action[priority]" class="block text-sm/6 font-medium text-gray-900">Priority</label>
                  <select id="action[priority]" name="action[priority]" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm/6">
                    <option value="high" selected>High</option>
                    <option value="low">Low</option>
                  </select>
                </div>
            </div>
            <div  class="mt-2">
                <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Create</button>
            </div>
        </form>
    </div>


@endsection
