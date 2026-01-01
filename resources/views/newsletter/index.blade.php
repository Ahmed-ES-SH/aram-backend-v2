<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->subject }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9fafb;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            margin: 0;
            color: #1a202c;
            font-size: 2.5rem;
        }

        .header p {
            color: #718096;
            font-size: 1.1rem;
            margin-top: 10px;
        }

        .section {
            margin-bottom: 40px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 40px;
        }

        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-image {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.8rem;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .section-desc {
            font-size: 1.1rem;
            color: #4a5568;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $newsletter->subject }}</h1>
            @if($newsletter->content)
            <p>{{ $newsletter->content }}</p>
            @endif
        </div>

        <!-- Section 1 -->
        @if($newsletter->section_1_image || $newsletter->section_1_title)
        <div class="section">
            @if($newsletter->section_1_image)
            <img src="{{ asset($newsletter->section_1_image) }}" alt="Section 1 Image" class="section-image">
            @else
            @if($newsletter->section_1_title)
            <h2 class="section-title">{{ $newsletter->section_1_title }}</h2>
            @endif
            @if($newsletter->section_1_description)
            <div class="section-desc">
                {!! nl2br(e($newsletter->section_1_description)) !!}
            </div>
            @endif
            @endif
        </div>
        @endif

        <!-- Section 2 -->
        @if($newsletter->section_2_image || $newsletter->section_2_title)
        <div class="section">
            @if($newsletter->section_2_image)
            <img src="{{ asset($newsletter->section_2_image) }}" alt="Section 2 Image" class="section-image">
            @else
            @if($newsletter->section_2_title)
            <h2 class="section-title">{{ $newsletter->section_2_title }}</h2>
            @endif
            @if($newsletter->section_2_description)
            <div class="section-desc">
                {!! nl2br(e($newsletter->section_2_description)) !!}
            </div>
            @endif
            @endif
        </div>
        @endif

        <!-- Section 3 -->
        @if($newsletter->section_3_image || $newsletter->section_3_title)
        <div class="section">
            @if($newsletter->section_3_image)
            <img src="{{ asset($newsletter->section_3_image) }}" alt="Section 3 Image" class="section-image">
            @else
            @if($newsletter->section_3_title)
            <h2 class="section-title">{{ $newsletter->section_3_title }}</h2>
            @endif
            @if($newsletter->section_3_description)
            <div class="section-desc">
                {!! nl2br(e($newsletter->section_3_description)) !!}
            </div>
            @endif
            @endif
        </div>
        @endif
    </div>
</body>

</html>