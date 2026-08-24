<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Ad Has Been Created</title>
</head>
<body>
    <h1>Your ad is live</h1>

    <p>Hello, {{ $ad->user?->name ?? 'there' }}!</p>

    <p>Your ad "{{ $ad->title }}" has been created successfully.</p>

    <p><strong>Category:</strong> {{ $ad->category?->name ?? 'N/A' }}</p>
    <p><strong>City:</strong> {{ $ad->city }}</p>
    <p><strong>Price:</strong> Rs. {{ number_format($ad->price, 2) }}</p>
    <p><strong>Status:</strong> {{ ucfirst($ad->status) }}</p>

    <p>You can log in to manage the ad or update it anytime from your dashboard.</p>
</body>
</html>
