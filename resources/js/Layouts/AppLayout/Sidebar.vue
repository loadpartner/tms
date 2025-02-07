<NavLink
    :href="route('shippers.index')"
    :active="route().current('shippers.*')"
    class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
>
    <TruckIcon class="w-5 h-5 mr-2" />
    {{ __('Shippers') }}
</NavLink> 