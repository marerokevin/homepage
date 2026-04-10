<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .card { background: #fff; border-radius: 16px; max-width: 480px; margin: 0 auto; padding: 36px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .logo { font-size: 13px; font-weight: 700; color: #888; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 24px; }
        h2 { font-size: 20px; color: #111; margin: 0 0 8px; }
        p { font-size: 14px; color: #555; line-height: 1.6; margin: 0 0 24px; }
        .code { font-size: 42px; font-weight: 800; letter-spacing: 0.2em; color: #111; text-align: center; background: #f3f4f6; border-radius: 12px; padding: 20px; margin: 24px 0; }
        .label { font-size: 12px; color: #aaa; text-align: center; margin-top: -16px; margin-bottom: 24px; }
        .warning { font-size: 12px; color: #d97706; background: #fef3c7; border-radius: 8px; padding: 10px 14px; }
        .footer { margin-top: 32px; font-size: 11px; color: #bbb; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">Crestec Philippines, Inc.</div>
        <h2>Cancellation Verification Code</h2>
        <p>You requested to cancel the following booking:</p>
        <p><strong>{{ $label }}</strong></p>

        <div class="code">{{ $code }}</div>
        <div class="label">Enter this code to confirm cancellation</div>

        <div class="warning">
            ⚠ This code expires in <strong>10 minutes</strong>. Do not share it with anyone.
        </div>

        <div class="footer">
            If you did not request this cancellation, you can safely ignore this email.<br>
            Crestec Philippines, Inc. — Vehicle & Room Allocation System
        </div>
    </div>
</body>
</html>
