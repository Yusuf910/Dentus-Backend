<!DOCTYPE html>
<html>
<head>
    <title>Invitation</title>
</head>
<body>
    <h1>Hello {{ $name }},</h1>
    <p>You have been invited to join Dentus. Please click the button below to accept the invitation.</p>
    <a href="{{ url('accept-invitation/' . $invitation_id) }}" style="display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none;">Accept Invitation</a>
    <p>If you did not expect this email, please ignore it.</p>
</body>
</html>
