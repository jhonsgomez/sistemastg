<div class="min-h-screen flex flex-col justify-start md:pt-[4rem] sm:pt-[2rem] xs:pt-[2rem] items-center bg-gray-100">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg" style='{{ request()->routeIs('login') ? "max-width: 30rem" : "max-width: 40rem" }}'>
        {{ $slot }}
    </div>
</div>
