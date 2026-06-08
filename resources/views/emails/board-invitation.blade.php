<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Board Invitation</title>
    <style>
        input:-webkit-autofill {
            transition: background-color 9999s ease-in-out 0s;
        }

        @keyframes none {}

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background-color: #0d0d0d;
            color: #ffffff;
            line-height: 1.6;
            margin: 0;
            padding: 40px 16px;
        }

        .btn:hover {
            border-color: #999 !important;
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
    </style>
</head>

<body>

    {{-- Outer wrapper --}}
    <div style="max-width:560px; margin:0 auto; background:#111111; border:1px solid #2a2a2a; overflow:hidden;">

        {{-- ── Header ── --}}
        <div style="background: radial-gradient(ellipse at 50% 120%, #2e2e2e 0%, #161616 60%, #0d0d0d 100%);
                border-bottom: 1px solid #2a2a2a;
                padding: 40px 40px 36px;
                text-align: center;">

            {{-- Icon circle --}}
            <div style="width:52px; height:52px; border:1px solid #3a3a3a; border-radius:50%;
                    margin:0 auto 20px; display:flex; align-items:center; justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#888"
                    stroke-width="1.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0
                         012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0
                         002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />
                </svg>
            </div>

            <h1 style="font-size:20px; font-weight:300; letter-spacing:0.22em;
                   color:#ffffff; text-transform:uppercase; margin:0 0 8px;">
                Board Invitation
            </h1>
            <p style="font-family: ui-sans-serif, system-ui, sans-serif;
                  font-size:12.5px; color:#666; letter-spacing:0.04em;">
                {{ $inviter->name }} wants to collaborate with you
            </p>
            <div style="width:32px; height:1px; background:#333; margin:16px auto 0;"></div>
        </div>

        {{-- ── Body ── --}}
        <div style="padding: 44px 40px 32px;">

            <p style="font-family: ui-sans-serif, system-ui, sans-serif;
                  font-size:13.5px; color:#888; line-height:1.9;
                  letter-spacing:0.02em; margin-bottom:28px;">
                Hi there —<br><br>
                <strong style="color:#bbb; font-weight:600;">{{ $inviter->name }}</strong>
                has invited you to join the board
                <strong style="color:#bbb; font-weight:600;">"{{ $board->name }}"</strong>
                on <strong style="color:#bbb; font-weight:600;">{{ config('app.name') }}</strong>.
                <br><br>
                To accept this invitation you need to create a free account.
                It only takes a minute.
            </p>

            {{-- Board box --}}
            <div style="display:flex; align-items:center; gap:16px;
                    background:#0d0d0d; border:1px solid #242424;
                    border-left:3px solid #333; padding:18px 20px; margin-bottom:28px;">
                <div style="width:44px; height:44px; min-width:44px; min-height:44px; flex-shrink:0; opacity:0.85;
            background-color:{{ $board->background_color ?? '#2a2a2a' }};
            display:inline-block; vertical-align:middle;"></div>
                <div>
                    <div style="font-family: ui-sans-serif, system-ui, sans-serif;
                            font-size:14px; font-weight:600; color:#e5e5e5; letter-spacing:0.03em;">
                        {{ $board->name }}
                    </div>
                    <div style="font-family: ui-sans-serif, system-ui, sans-serif;
                            font-size:11.5px; color:#555; margin-top:3px; letter-spacing:0.03em;">
                        Invited by {{ $inviter->name }}
                    </div>
                </div>
            </div>

            {{-- Steps --}}
            <div style="background:#0d0d0d; border:1px solid #222; padding:20px 22px; margin-bottom:32px;">
                <div style="font-family: ui-sans-serif, system-ui, sans-serif;
                        font-size:10px; font-weight:700; color:#555;
                        letter-spacing:0.18em; text-transform:uppercase; margin-bottom:16px;">
                    How to join in 3 steps
                </div>

                @foreach([
                ['n' => '1', 'text' => 'Click the button below to create your free account', 'strong' => null],
                ['n' => '2', 'text' => 'Register using ', 'strong' => $invitation->email],
                ['n' => '3', 'text' => 'You will be automatically added to ', 'strong' => '"'.$board->name.'"'],
                ] as $step)
                <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom:12px;
                        font-family: ui-sans-serif, system-ui, sans-serif;
                        font-size:12.5px; color:#777; line-height:1.6; letter-spacing:0.02em;
                        {{ $loop->last ? 'margin-bottom:0;' : '' }}">
                    <div style="width:20px; height:20px; min-width:20px; border:1px solid #444;
                            color:#888; font-size:10px; font-weight:600; display:flex;
                            align-items:center; justify-content:center; flex-shrink:0; margin-top:1px;">
                        {{ $step['n'] }}
                    </div>
                    <div>
                        {{ $step['text'] }}
                        @if($step['strong'])
                        <strong style="color:#aaa;">{{ $step['strong'] }}</strong>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Button --}}
            <div style="text-align:center; margin: 4px 0 24px;">
                <a href="{{ route('register', ['invitation' => $invitation->token]) }}"
                    class="btn"
                    style="display:inline-block; background:transparent; color:#e5e5e5;
                      text-decoration:none; padding:14px 40px; border:1px solid #555;
                      font-family: ui-sans-serif, system-ui, sans-serif;
                      font-weight:600; font-size:11px; letter-spacing:0.26em;
                      text-transform:uppercase;">
                    Create Account &amp; Join Board
                </a>
            </div>

            {{-- Expiry --}}
            <p style="text-align:center; font-family: ui-sans-serif, system-ui, sans-serif;
                  font-size:11px; color:#484848; margin-top:18px;
                  letter-spacing:0.03em; font-style:italic;">
                This invitation expires
                {{ $invitation->expires_at->diffForHumans() }}
                ({{ $invitation->expires_at->format('M j, Y') }})
            </p>

        </div>

        {{-- ── Footer ── --}}
        <div style="border-top:1px solid #1e1e1e; padding:22px 40px 28px;
                text-align:center; background-color:#0d0d0d;">
            <p style="font-family: ui-sans-serif, system-ui, sans-serif;
                  font-size:11.5px; color:#444; line-height:1.8; letter-spacing:0.03em;">
                If you did not expect this invitation you can safely ignore this email.<br>
                Sent by {{ $inviter->name }} · {{ $inviter->email }}
            </p>
            <span style="font-family: 'Georgia', 'Times New Roman', serif;
                     font-weight:300; font-size:13px; letter-spacing:0.22em;
                     color:#777; text-transform:uppercase; display:block; margin-top:10px;">
                {{ config('app.name') }}
            </span>
        </div>

    </div>

</body>

</html>