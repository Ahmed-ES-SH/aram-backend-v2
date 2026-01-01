<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #333;
        }

        .section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .section:last-child {
            border-bottom: none;
        }

        .section img {
            max-width: 100%;
            border-radius: 4px;
            display: block;
            margin: 0 auto;
        }

        .section-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .section-desc {
            color: #555;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $newsletter->subject }}</h1>
            @if($newsletter->content)
            <p>{{ $newsletter->content }}</p>
            @endif
        </div>

        @foreach([1, 2, 3] as $i)
        @php
        $img = $newsletter->{'section_'.$i.'_image'};
        $title = $newsletter->{'section_'.$i.'_title'};
        $desc = $newsletter->{'section_'.$i.'_description'};
        @endphp

        @if($img || $title)
        <div class="section">
            @if($img)
            <img src="{{ $img }}" alt="Image">
            @else
            @if($title)
            <h2 class="section-title">{{ $title }}</h2>
            @endif
            @if($desc)
            <div class="section-desc">
                {!! nl2br(e($desc)) !!}
            </div>
            @endif
            @endif
        </div>
        @endif
        @endforeach
    </div>
</body>

</html>