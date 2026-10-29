<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            line-height: 1.55;
            color: #0f172a;
            margin: 0;
            padding: 28px;
            font-size: 12px;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 22px;
        }
        .brand {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 0.04em;
            color: #0f172a;
        }
        .subtitle {
            color: #64748b;
            font-size: 11px;
            margin-top: 4px;
        }
        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            font-size: 10px;
        }
        .meta-grid td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .meta-grid .label {
            width: 28%;
            background: #f8fafc;
            color: #475569;
            font-weight: bold;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-bottom: 10px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }
        .vehicle-info { width: 100%; border-collapse: collapse; }
        .vehicle-info td {
            padding: 7px 8px;
            border: 1px solid #e2e8f0;
        }
        .vehicle-label {
            width: 30%;
            background: #f8fafc;
            font-weight: bold;
            color: #334155;
        }
        .badge {
            display: inline-block;
            background: #0f172a;
            color: #fff;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .problem-title {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 8px;
        }
        .box {
            border: 1px solid #e2e8f0;
            padding: 12px;
            margin: 8px 0;
            background: #f8fafc;
        }
        .cause-item { border-left: 3px solid #f59e0b; background: #fffbeb; }
        .solution-item { border-left: 3px solid #10b981; background: #ecfdf5; }
        .muted { color: #64748b; }
        .illustration {
            margin: 12px 0;
            text-align: center;
            page-break-inside: avoid;
        }
        .illustration img {
            max-width: 420px;
            max-height: 420px;
            border: 1px solid #e2e8f0;
        }
        .illustration .caption {
            margin-top: 6px;
            font-size: 10px;
            color: #64748b;
        }
        .footer {
            margin-top: 28px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #64748b;
            text-align: center;
        }
        ul { margin: 0; padding-left: 18px; }
        li { margin-bottom: 4px; }
    </style>
</head>
<body>
    @php
        $diagnosedAt = $evidence['diagnosed_at'] ?? $generated_at ?? null;
        $unlockedAt = $evidence['unlocked_at'] ?? null;
        $fmt = function ($value) {
            if (! $value) {
                return '—';
            }
            try {
                return \Carbon\Carbon::parse($value)->timezone(config('app.timezone'))->format('d.m.Y H:i:s T');
            } catch (\Throwable $e) {
                return (string) $value;
            }
        };
    @endphp

    <div class="header">
        <div class="brand">CarWise.ai</div>
        <div class="subtitle">AI vehicle diagnosis evidence report</div>

        <table class="meta-grid">
            <tr>
                <td class="label">Report generated</td>
                <td>{{ $fmt($generated_at) }}</td>
                <td class="label">Diagnosis started</td>
                <td>{{ $fmt($diagnosedAt) }}</td>
            </tr>
            <tr>
                <td class="label">Unlocked / full result</td>
                <td>{{ $fmt($unlockedAt) }}</td>
                <td class="label">Report ID</td>
                <td>{{ $evidence['report_id'] ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Session ID</td>
                <td>{{ $evidence['session_id'] ?? '—' }}</td>
                <td class="label">AI provider</td>
                <td>{{ strtoupper((string) ($evidence['ai_provider'] ?? ($result['ai_provider'] ?? 'openai'))) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Vehicle</div>
        <table class="vehicle-info">
            <tr>
                <td class="vehicle-label">Make</td>
                <td>{{ $vehicle['make'] }}</td>
            </tr>
            <tr>
                <td class="vehicle-label">Model</td>
                <td>{{ $vehicle['model'] }}</td>
            </tr>
            <tr>
                <td class="vehicle-label">Year</td>
                <td>{{ $vehicle['year'] }}</td>
            </tr>
            @if(!empty($vehicle['mileage']))
            <tr>
                <td class="vehicle-label">Mileage</td>
                <td>{{ number_format((int) $vehicle['mileage']) }} km</td>
            </tr>
            @endif
            @if(!empty($vehicle['engine_type']))
            <tr>
                <td class="vehicle-label">Engine type</td>
                <td>{{ $vehicle['engine_type'] }}</td>
            </tr>
            @endif
            @if(!empty($vehicle['engine_size']))
            <tr>
                <td class="vehicle-label">Engine size</td>
                <td>{{ $vehicle['engine_size'] }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">Reported symptoms</div>
        @if(!empty($symptoms))
            <ul>
                @foreach($symptoms as $symptom)
                    <li>{{ is_array($symptom) ? ($symptom['label'] ?? json_encode($symptom)) : $symptom }}</li>
                @endforeach
            </ul>
        @else
            <p class="muted">No checklist symptoms selected.</p>
        @endif
        @if(!empty($description))
            <p style="margin-top:10px;"><strong>Description:</strong> {{ $description }}</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Diagnosis result</div>
        @if(isset($result['confidence']) || isset($result['confidence_score']))
            <div class="badge">{{ $result['confidence'] ?? $result['confidence_score'] }}% match</div>
        @endif
        @if(!empty($result['severity']))
            <div class="badge" style="background:#b45309;">Severity: {{ $result['severity'] }}</div>
        @endif

        @if(!empty($result['problem_title']))
            <div class="problem-title">{{ $result['problem_title'] }}</div>
        @endif
        @if(!empty($result['problem_description']))
            <p>{{ $result['problem_description'] }}</p>
        @endif

        @if(!empty($illustrations))
            @foreach($illustrations as $image)
                <div class="illustration">
                    <img src="{{ $image['src'] }}" alt="{{ $image['caption'] }}">
                    <div class="caption">{{ $image['caption'] }} — AI technical illustration</div>
                </div>
            @endforeach
        @endif

        @if(!empty($result['likely_causes']))
            <p><strong>Why this is likely</strong></p>
            @foreach($result['likely_causes'] as $cause)
                <div class="box cause-item">
                    <strong>{{ $cause['cause'] ?? $cause['title'] ?? 'Cause' }}</strong>
                    <div>{{ $cause['description'] ?? $cause['explanation'] ?? '' }}</div>
                    @if(isset($cause['probability']))
                        <div class="muted">{{ $cause['probability'] }}% probability</div>
                    @endif
                </div>
            @endforeach
        @endif

        @if(!empty($result['recommended_actions']))
            <p><strong>What to do</strong></p>
            @foreach($result['recommended_actions'] as $action)
                <div class="box solution-item">
                    <strong>{{ $action['action'] ?? $action['title'] ?? 'Action' }}</strong>
                    <div>{{ $action['description'] ?? $action['explanation'] ?? '' }}</div>
                    @if(!empty($action['urgency']))
                        <div class="muted">{{ $action['urgency'] }}</div>
                    @endif
                </div>
            @endforeach
        @endif

        @if(!empty($result['estimated_costs']))
            <p><strong>Guide costs</strong></p>
            <ul>
                @foreach($result['estimated_costs'] as $cost)
                    <li>
                        {{ $cost['service'] ?? 'Service' }}:
                        €{{ $cost['min'] ?? '?' }} – €{{ $cost['max'] ?? '?' }}
                    </li>
                @endforeach
            </ul>
        @endif

        @if(!empty($result['ai_insights']))
            <p><strong>Also worth knowing</strong></p>
            <ul>
                @foreach($result['ai_insights'] as $insight)
                    <li>{{ is_array($insight) ? ($insight['text'] ?? json_encode($insight)) : $insight }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="footer">
        <p>This document is an AI-assisted diagnosis evidence record from CarWise.ai. It is not a workshop invoice or legal certification.</p>
        <p>Always confirm repairs with a qualified technician. © {{ date('Y') }} CarWise.ai</p>
    </div>
</body>
</html>
