<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationAssigned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $invite = $request->user()
                ->organizationInvites()
                ->withoutGlobalScopes()
                ->where('accepted_at', null)
                ->first();
            if ($invite) {
                return redirect()->route(
                    'organizations.invites.show',
                    [
                        'organization' => $invite->organization_id,
                        'invite' => $invite->code
                    ]
                );
            }

            if (! $request->user()->current_organization_id) {
                $firstOrg = $request->user()->organizations->first();
                if ($firstOrg) {
                    $request->user()->update(['current_organization_id' => $firstOrg->id]);
                } else {
                    return redirect()->route('organizations.create');
                }
            }
        }
        return $next($request);
    }
}
