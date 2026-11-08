<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                        <img src="{{ asset('logo.png') }}" alt="Logo Bạn hỏi tôi trả lời" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-sm sm:text-base font-bold text-blue-600 tracking-tight group-hover:text-blue-700 transition-colors whitespace-nowrap">
                            {{ config('app.name', 'Bạn hỏi tôi trả lời') }}
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden items-center gap-2 sm:-my-px sm:ms-4 sm:flex whitespace-nowrap">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        Trang chủ
                    </x-nav-link>
                    <x-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.*')">
                        Hỏi đáp
                    </x-nav-link>
                    <x-nav-link :href="route('stats')" :active="request()->routeIs('stats')">
                        Thống kê
                    </x-nav-link>
                    <x-nav-link :href="route('books.index')" :active="request()->routeIs('books.*')">
                        Sách cũ
                    </x-nav-link>
                    @auth
                        @if(auth()->user()->isModerator())
                            <x-nav-link :href="route('moderation')" :active="request()->routeIs('moderation')">
                                Kiểm duyệt
                            </x-nav-link>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                Tài khoản
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown / Login links + Nút Đặt câu hỏi cho người đã đăng nhập -->
            <div class="hidden sm:flex sm:items-center sm:ms-4 sm:gap-2.5 whitespace-nowrap">
                @auth
                    <!-- Nút Đặt câu hỏi màu xanh nổi bật cho người đã đăng nhập -->
                    @unless(request()->routeIs('home', 'admin.users.*'))
                        <a href="{{ route('questions.create') }}" class="inline-flex shrink-0 items-center whitespace-nowrap px-2.5 py-1.5 border border-transparent text-xs font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Đặt câu hỏi
                        </a>
                    @endunless

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center whitespace-nowrap px-2.5 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                                <x-user-avatar :user="Auth::user()" size="w-6 h-6" text-size="text-xs" class="mr-2" />
                                <div class="font-medium whitespace-nowrap">{{ Auth::user()->name }}</div>
                                <span class="ms-1 text-xs font-semibold whitespace-nowrap px-1 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ Auth::user()->reputation }} đ
                                </span>
                                <div class="ms-1.5">
                                    <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('users.show', Auth::user())">
                                Trang cá nhân
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">
                                Cài đặt tài khoản
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    Đăng xuất
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">
                        Đăng nhập
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors">
                            Đăng ký
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-b border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                Trang chủ
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.*')">
                Hỏi đáp
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('stats')" :active="request()->routeIs('stats')">
                Thống kê
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('books.index')" :active="request()->routeIs('books.*')">
                Sách cũ
            </x-responsive-nav-link>
            @auth
                @if(auth()->user()->isModerator())
                    <x-responsive-nav-link :href="route('moderation')" :active="request()->routeIs('moderation')">
                        Kiểm duyệt
                    </x-responsive-nav-link>
                @endif
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        Quản lý tài khoản
                    </x-responsive-nav-link>
                @endif
                <div class="px-4 pt-2">
                    <a href="{{ route('questions.create') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                        + Đặt câu hỏi
                    </a>
                </div>
            @endauth
        </div>

        <div class="pt-4 pb-3 border-t border-gray-200">
            @auth
                <div class="px-4 flex items-center gap-3">
                    <x-user-avatar :user="Auth::user()" size="w-9 h-9" text-size="text-sm" />
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-xs text-gray-500">{{ Auth::user()->email }} · {{ Auth::user()->reputation }} điểm</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('users.show', Auth::user())">
                        Trang cá nhân
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Cài đặt tài khoản
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            Đăng xuất
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')">Đăng nhập</x-responsive-nav-link>
                    @if (Route::has('register'))
                        <x-responsive-nav-link :href="route('register')">Đăng ký</x-responsive-nav-link>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>
