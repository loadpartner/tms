<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Response;
use Lorisleiva\Actions\ActionRequest;

class SubmitFeedback {
    use AsAction {
        __invoke as protected invokeFromLaravelActions;
    }

    public function handle(string $email, string $feedback): Response
    {
        Mail::raw($feedback, function($message) use ($email) {
            $message->to(config('mail.feedback_email'))
                   ->subject('New Feedback from ' . $email)
                   ->replyTo($email);
        });

        return new Response(status: 200, content: 'Feedback submitted successfully.');
    }

    public function asController(ActionRequest $request)
    {
        $rateLimitIdentifier = 'ip' . $request->ip();
        $rateLimiterKey = 'submit-feedback.' . $rateLimitIdentifier;

        if (RateLimiter::tooManyAttempts($rateLimiterKey, 5)) {
            return new Response(status: 429, content: 'Too many attempts. Please try again later.');
        }

        RateLimiter::increment($rateLimiterKey);

        $request->validate();

        $email = $request->input('email');
        $feedback = $request->input('feedback');

        return $this->handle($email, $feedback);
    }

    public function htmlResponse()
    {
        return redirect()->back();
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'feedback' => 'required|string|min:10',
        ];
    }
}