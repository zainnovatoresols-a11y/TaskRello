<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - TaskRello</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            line-height: 1.6;
            color: #e2dede;
            background-color: #0d0d0d;
        }

        .container {
            max-width: 600px;
            margin: 32px auto;
            background-color: #111111;
            border: 1px solid #2a2a2a;
            overflow: hidden;
        }

        .header {
            background: radial-gradient(ellipse at 50% 120%, #2e2e2e 0%, #161616 60%, #0d0d0d 100%);
            border-bottom: 1px solid #2a2a2a;
            padding: 36px 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 300;
            letter-spacing: 0.28em;
            color: #ffffff;
            text-transform: uppercase;
            margin: 0;
        }

        .header-line {
            width: 32px;
            height: 1px;
            background: #444;
            margin: 14px auto 0;
        }

        .content {
            padding: 44px 40px;
        }

        .greeting {
            font-size: 17px;
            font-weight: 300;
            color: #e5e5e5;
            letter-spacing: 0.08em;
            margin-bottom: 18px;
        }

        .message {
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 13.5px;
            color: #dbdada;
            margin-bottom: 28px;
            line-height: 1.9;
            letter-spacing: 0.02em;
        }

        .button-container {
            text-align: center;
            margin: 36px 0;
        }

        .button {
            display: inline-block;
            background: transparent;
            color: #e5e5e5;
            text-decoration: none;
            padding: 14px 40px;
            border: 1px solid #555;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-weight: 600;
            font-size: 11px;
            letter-spacing: 0.26em;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .button:hover {
            border-color: #999;
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.04);
        }

        .expiry {
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 11.5px;
            color: #555;
            text-align: center;
            margin: 20px 0;
            letter-spacing: 0.04em;
            font-style: italic;
        }

        .url-section {
            margin-top: 36px;
            padding-top: 32px;
            border-top: 1px solid #222;
        }

        .url-label {
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 11.5px;
            color: #8b8b8b;
            margin-bottom: 12px;
            letter-spacing: 0.03em;
            line-height: 1.7;
        }

        .url {
            font-size: 11px;
            color: #f5f3f3;
            word-break: break-all;
            background-color: #0d0d0d;
            padding: 14px 16px;
            border-left: 2px solid #333;
            font-family: 'Courier New', monospace;
            line-height: 1.7;
            letter-spacing: 0.02em;
        }

        .footer {
            background-color: #0d0d0d;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #1e1e1e;
        }

        .footer p {
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 11.5px;
            color: #d1cfcf;
            letter-spacing: 0.04em;
        }

        .footer-note {
            margin-top: 8px;
            font-size: 11px;
            line-height: 1.6;
        }

        .brand {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-weight: 300;
            font-size: 13px;
            letter-spacing: 0.22em;
            color: #d6d4d4;
            text-transform: uppercase;
            display: block;
            margin-top: 6px;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h1>TaskRello</h1>
            <div class="header-line"></div>
        </div>

        <div class="content">
            <p class="greeting">Hello!</p>

            <p class="message">
                You are receiving this email because we received a password reset request for your account.
            </p>

            <div class="button-container">
                <a href="{{ $url }}" class="button">Reset Password</a>
            </div>

            <p class="expiry">
                This password reset link will expire in 60 minutes.
            </p>

            <p class="message">
                If you did not request a password reset, no further action is required.
            </p>

            <div class="url-section">
                <p class="url-label">If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
                <p class="url">
                    <a href="{{ $url }}" style="color:#666; text-decoration:none;">
                        {{ $url }}
                    </a>
                </p>
            </div>
        </div>

        <div class="footer">
            <p>Regards,</p>
            <span class="brand">TaskRello</span>
            <div class="footer-note">
                <p>© 2025 TaskRello. All rights reserved.</p>
            </div>
        </div>

    </div>
</body>

</html>