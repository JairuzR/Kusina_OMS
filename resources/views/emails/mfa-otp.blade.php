<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; background: #f3f4f6; margin: 0; padding: 40px 20px; }
        .card { background: white; max-width: 480px; margin: 0 auto; border-radius: 12px; padding: 40px; }
        .logo { font-size: 24px; font-weight: bold; margin-bottom: 24px; }
        .logo span:first-child { color: #f97316; }
        .code { font-size: 40px; font-weight: bold; letter-spacing: 12px; color: #111827;
                background: #f9fafb; border: 2px dashed #e5e7eb; border-radius: 8px;
                padding: 16px 24px; text-align: center; margin: 24px 0; }
        .footer { color: #9ca3af; font-size: 13px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <span>Kusina</span><span style="color:#111827"> OMS</span>
        </div>
        <p>Hi <strong>{{ $userName }}</strong>,</p>
        <p>Your login verification code is:</p>
        <div class="code">{{ $code }}</div>
        <p>This code expires in <strong>5 minutes</strong>. Do not share it with anyone.</p>
        <p>If you did not attempt to log in, please contact your administrator immediately.</p>
        <div class="footer">— KusinaOMS Security</div>
    </div>
</body>
</html>