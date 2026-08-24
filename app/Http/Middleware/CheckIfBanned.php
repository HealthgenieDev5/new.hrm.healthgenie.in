<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfBanned
{
    // List of banned user emails or IDs
    private array $bannedEmails = [
        // 'akshay323@yahoo.com',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && in_array(Auth::user()->email, $this->bannedEmails)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is under review. Please contact us for more information.']);
        }
        
        return $next($request);
    }
}