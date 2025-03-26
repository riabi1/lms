<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - EDAA Learning</title>
    <style>
        @page { 
            size: A4 portrait;
            margin: 20mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', serif;
            color: #333;
            background: #fff;
            text-align: center;
        }

        .container {
            width: 90%;
            margin: auto;
            padding: 20px;
            border: 15px solid #1e3a8a;
            border-radius: 10px;
            position: relative;
        }

        .header {
            margin-bottom: 40px;
        }

        .header img {
            width: 100px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 34px;
            color: #1e3a8a;
            margin: 0;
        }

        .body {
            margin: 30px 0;
        }

        .name, .course {
            font-size: 24px;
            font-weight: bold;
            margin: 20px auto;
            padding-bottom: 5px;
            border-bottom: 2px solid #1e3a8a;
            display: inline-block;
        }

        .text {
            font-size: 20px;
            margin: 10px 0;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #1e3a8a;
        }

        .footer div {
            font-size: 18px;
            margin: 10px 0;
        }

        .underline {
            border-bottom: 1px solid #777;
            width: 60%;
            margin: 5px auto;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <img src="{{ public_path('frontend/images/logo2.PNG') }}" alt="EDAA Learning Logo">
            <h1>Certificate of Completion</h1>
        </header>
        
        <section class="body">
            <p class="text">This is to certify that</p>
            <div class="name">{{ $user_name }}</div>
            <p class="text">has successfully completed the course</p>
            <div class="course">{{ $course_name }}</div>
        </section>

        <footer class="footer">
            <div>EDAA Learning</div>
            <div class="underline"></div>
            <div>EDAA Learning Administration</div>
            <div>Date Completed</div>
            <div class="underline">{{ $completion_date }}</div>
            <div>Certificate #: {{ $certificate_number }}</div>
        </footer>
    </div>
</body>
</html>
