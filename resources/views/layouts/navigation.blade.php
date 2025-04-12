<nav class="bg-gray-800 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        @if (Auth::check())
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ Route::is('admin.dashboard') ? 'bg-gray-900 text-white' : '' }}">Admin Dashboard</a>
                                <a href="{{ route('admin.agents') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ Route::is('admin.agents') ? 'bg-gray-900 text-white' : '' }}">Agents</a>
                                <a href="{{ route('admin.meter_readings') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ Route::is('admin.meter_readings') ? 'bg-gray-900 text-white' : '' }}">Meter Readings</a>
                            @elseif (Auth::user()->isAgent())
                                <a href="{{ route('agent.dashboard') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ Route::is('agent.dashboard') ? 'bg-gray-900 text-white' : '' }}">Agent Dashboard</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ Route::is('dashboard') ? 'bg-gray-900 text-white' : '' }}">Dashboard</a>
                            @endif
                            <a href="{{ route('messages.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ Route::is('messages.*') ? 'bg-gray-900 text-white' : '' }}">
                                Messages
                                @php
                                    $unreadCount = auth()->user()->unreadMessages()->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="ml-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-600 rounded-full">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @if (Auth::check())
                <div class="ml-4 flex items-center md:ml-6">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Log Out</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</nav>