import { Truck } from "lucide-react";

<nav className="grid items-start px-2 text-sm font-medium lg:px-4">
  {/* ... other nav items */}
  <Link
    href={route('shippers.index')}
    className="flex items-center gap-3 rounded-lg px-3 py-2 text-muted-foreground transition-all hover:text-primary"
  >
    <Truck className="h-4 w-4" />
    Shippers
  </Link>
</nav> 