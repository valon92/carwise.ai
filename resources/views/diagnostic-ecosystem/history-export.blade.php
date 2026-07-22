<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vehicle Twin History</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin-top: 18px; }
        .meta { color: #555; margin-bottom: 16px; }
        .event { border-bottom: 1px solid #ddd; padding: 8px 0; }
        .title { font-weight: bold; }
        .small { color: #666; }
    </style>
</head>
<body>
    <h1>CarWise Vehicle Twin History</h1>
    <div class="meta">
        VIN: {{ $vehicle['vin'] ?? 'n/a' }}<br>
        Vehicle: {{ trim(($vehicle['year'] ?? '').' '.($vehicle['brand'] ?? '').' '.($vehicle['model'] ?? '')) ?: ($vehicle['nickname'] ?? 'Vehicle') }}<br>
        Mileage: {{ $vehicle['current_mileage'] ?? 'n/a' }} km<br>
        Exported: {{ $exportedAt }}
    </div>

    <h2>Timeline</h2>
    @forelse ($events as $event)
        <div class="event">
            <div class="title">{{ $event['title'] ?? 'Event' }}</div>
            <div class="small">
                {{ $event['event_type'] ?? '' }}
                @if (!empty($event['event_date'])) · {{ $event['event_date'] }} @endif
                @if (!empty($event['mileage'])) · {{ $event['mileage'] }} km @endif
            </div>
            @if (!empty($event['description']))
                <div>{{ $event['description'] }}</div>
            @endif
        </div>
    @empty
        <p>No history events yet.</p>
    @endforelse
</body>
</html>
