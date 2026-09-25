<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Alert</title>
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
{{-- <body>
    <!-- Include SweetAlert JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        // Trigger the SweetAlert
        Swal.fire({
            icon: 'success',
            title: 'Thank you!',
            text: 'Your submission has been sent.',
            confirmButtonText: 'OK'
        }).then((result) => {
            // Redirect to specific page
            if (result.isConfirmed) {
                window.location.href = 'Frontweb.health-survey-form';
            }
        });
    </script>
</body> --}}
<body>
   <p>Thank you for submittig the form!</p>
   <a href="{{ route('health-survey-form') }}">OK</a>
</body>
</html>
