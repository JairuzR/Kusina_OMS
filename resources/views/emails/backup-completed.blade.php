<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: {{ $success ? '#16a34a' : '#dc2626' }}; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: 0.85; }
        .body { padding: 32px; }
        .row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .row span:first-child { color: #6b7280; }
        .row span:last-child { font-weight: 600; color: #111827; }
        .error-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 12px 16px; margin-top: 16px; font-size: 13px; color: #991b1b; }
        .footer { padding: 16px 32px; background: #f9fafb; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>{{ $success ? 'Backup Completed' : 'Backup Failed' }}</h1>
        <p>KusinaOMS — Restaurant Operations Management System</p>
    </div>
    <div class="body">
        <p style="font-size:14px;color:#374151;margin-top:0;">
            {{ $success
                ? 'Your database backup was completed successfully. Details below.'
                : 'The scheduled backup encountered an error and could not complete.' }}
        </p>

        <div class="row"><span>Filename</span><span>{{ $backup->filename }}</span></div>
        <div class="row"><span>Type</span><span style="text-transform:capitalize;">{{ $backup->type }}</span></div>
        <div class="row"><span>Size</span><span>{{ $backup->formatted_size }}</span></div>
        <div class="row"><span>Status</span><span>{{ ucfirst($backup->status) }}</span></div>
        <div class="row"><span>Timestamp</span><span>{{ $backup->created_at->format('F j, Y g:i A') }}</span></div>

        @if(!$success && $errorMessage)
        <div class="error-box">
            <strong>Error:</strong> {{ $errorMessage }}
        </div>
        @endif
    </div>
    <div class="footer">
        This is an automated message from KusinaOMS. Please do not reply to this email.
    </div>
</div>
</body>
</html>