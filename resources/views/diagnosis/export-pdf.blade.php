<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 22mm 16mm 20mm 16mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.55;
            margin: 0;
            padding: 0;
        }
        .doc-header {
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .brand {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.02em;
        }
        .brand span { color: #64748b; font-weight: normal; }
        .subtitle {
            color: #64748b;
            font-size: 10px;
            margin-top: 2px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 9px;
        }
        .meta-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 7px;
            vertical-align: top;
        }
        .meta-table .k {
            width: 24%;
            background: #f8fafc;
            color: #475569;
            font-weight: bold;
        }
        .section {
            margin: 0 0 14px 0;
            page-break-inside: avoid;
        }
        .section-break {
            page-break-before: always;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin: 0 0 8px 0;
        }
        .vehicle-table, .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .vehicle-table td, .info-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
        }
        .vehicle-table .k {
            width: 32%;
            background: #f8fafc;
            font-weight: bold;
            color: #334155;
        }
        .badge {
            display: inline-block;
            background: #0f172a;
            color: #fff;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: bold;
            margin-right: 4px;
            margin-bottom: 6px;
        }
        .badge-amber { background: #b45309; }
        .badge-soft { background: #475569; }
        .problem-title {
            font-size: 15px;
            font-weight: bold;
            margin: 4px 0 8px;
        }
        .box {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 9px 10px;
            margin: 0 0 8px 0;
            page-break-inside: avoid;
        }
        .box-cause {
            border-left: 3px solid #d97706;
            background: #fffbeb;
        }
        .box-action {
            border-left: 3px solid #059669;
            background: #ecfdf5;
        }
        .box-note {
            border-left: 3px solid #475569;
            background: #f1f5f9;
        }
        .muted { color: #64748b; font-size: 9.5px; }
        .illustration {
            text-align: center;
            margin: 8px 0 12px;
            page-break-inside: avoid;
        }
        .illustration img {
            max-width: 340px;
            max-height: 280px;
            border: 1px solid #e2e8f0;
        }
        .illustration .caption {
            margin-top: 4px;
            font-size: 9px;
            color: #64748b;
        }
        .cause-visual {
            margin: 8px 0 6px;
            text-align: center;
        }
        .cause-visual img {
            max-width: 420px;
            max-height: 280px;
            width: 100%;
            border: 1px solid #fecaca;
            border-radius: 8px;
        }
        .problem-zone {
            margin: 6px 0 8px;
            padding: 8px 10px;
            border: 1.5px solid #ef4444;
            background: #fef2f2;
            border-radius: 6px;
            page-break-inside: avoid;
        }
        .problem-zone .tag {
            display: inline-block;
            margin: 0 0 4px 0;
            padding: 2px 7px;
            border-radius: 999px;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .problem-zone .label {
            font-weight: 700;
            color: #7f1d1d;
            font-size: 11px;
            margin: 0 0 3px 0;
        }
        .problem-zone .meaning {
            color: #7f1d1d;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
        }
        .cause-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0 4px 8px 0;
        }
        ul { margin: 4px 0 0 16px; padding: 0; }
        li { margin: 0 0 3px 0; }
        .footer {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 8.5px;
            color: #64748b;
            text-align: center;
            page-break-inside: avoid;
        }
        .keep { page-break-inside: avoid; }
        h0 { display: none; }
    </style>
</head>
<body>
@php
    $diagnosedAt = $evidence['diagnosed_at'] ?? $generated_at ?? null;
    $unlockedAt = $evidence['unlocked_at'] ?? null;
    $engines = $evidence['diagnostic_engines'] ?? ($result['diagnostic_engines'] ?? []);
    $engineLabel = is_array($engines) && count($engines)
        ? implode(' + ', $engines)
        : (string) ($evidence['ai_provider'] ?? ($result['ai_provider'] ?? 'CarWise Multi-AI'));
    $glossary = $result['glossary'] ?? \App\Services\DiagnosisTerminology::glossary('sq');
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

<div class="doc-header keep">
    <div class="brand">CarWise<span>.ai</span></div>
    <div class="subtitle">Professional AI vehicle diagnosis · evidence report</div>
    <table class="meta-table">
        <tr>
            <td class="k">Report generated</td>
            <td>{{ $fmt($generated_at) }}</td>
            <td class="k">Diagnosis started</td>
            <td>{{ $fmt($diagnosedAt) }}</td>
        </tr>
        <tr>
            <td class="k">Unlocked / full result</td>
            <td>{{ $fmt($unlockedAt) }}</td>
            <td class="k">Report ID</td>
            <td>{{ $evidence['report_id'] ?? '—' }}</td>
        </tr>
        <tr>
            <td class="k">Session ID</td>
            <td>{{ $evidence['session_id'] ?? '—' }}</td>
            <td class="k">Diagnostic engines</td>
            <td>{{ $engineLabel }}</td>
        </tr>
    </table>
</div>

<div class="section keep">
    <div class="section-title">1. Vehicle</div>
    <table class="vehicle-table">
        <tr><td class="k">Make</td><td>{{ $vehicle['make'] }}</td></tr>
        <tr><td class="k">Model</td><td>{{ $vehicle['model'] }}</td></tr>
        <tr><td class="k">Year</td><td>{{ $vehicle['year'] }}</td></tr>
        @if(!empty($vehicle['mileage']))
            <tr><td class="k">Mileage</td><td>{{ number_format((int) $vehicle['mileage']) }} km</td></tr>
        @endif
        @if(!empty($vehicle['engine_type']))
            <tr><td class="k">Engine type</td><td>{{ $vehicle['engine_type'] }}</td></tr>
        @endif
        @if(!empty($vehicle['engine_size']))
            <tr><td class="k">Engine size</td><td>{{ $vehicle['engine_size'] }}</td></tr>
        @endif
    </table>
</div>

<div class="section keep">
    <div class="section-title">2. Reported symptoms</div>
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
        <p style="margin-top:8px;"><strong>Owner description:</strong> {{ $description }}</p>
    @endif
    @if(!empty($result['provided_obd_code']))
        <p><strong>Verified fault code:</strong> {{ $result['provided_obd_code'] }}</p>
    @endif
</div>

<div class="section keep">
    <div class="section-title">3. Diagnosis summary</div>
    @if(isset($result['confidence']) || isset($result['confidence_score']))
        <span class="badge">{{ $result['confidence'] ?? $result['confidence_score'] }}% confidence</span>
    @endif
    @if(!empty($result['severity']))
        <span class="badge badge-amber">Severity: {{ $result['severity'] }}</span>
    @endif
    @if(!empty($result['data_quality']))
        <span class="badge badge-soft">Data quality: {{ $result['data_quality'] }}</span>
    @endif
    @if(!empty($result['code_refined']))
        <span class="badge">Code verified</span>
    @endif

    @if(!empty($result['problem_title']))
        <div class="problem-title">{{ $result['problem_title'] }}</div>
    @endif
    @if(!empty($result['problem_description']))
        <p>{{ $result['problem_description'] }}</p>
    @endif

    @if(!empty($result['hypothesis_disclaimer']))
        <div class="box box-note">
            <strong>Professional note</strong>
            <div>{{ $result['hypothesis_disclaimer'] }}</div>
        </div>
    @endif
</div>

<div class="section">
    <div class="section-title">4. What to do (in order)</div>
    @forelse(($result['recommended_actions'] ?? []) as $i => $action)
        <div class="box box-action keep">
            <strong>{{ $i + 1 }}. {{ $action['action'] ?? $action['title'] ?? 'Action' }}</strong>
            <div>{{ $action['description'] ?? $action['explanation'] ?? '' }}</div>
            @if(!empty($action['urgency']))
                <div class="muted">Timing: {{ $action['urgency'] }}</div>
            @endif
        </div>
    @empty
        <p class="muted">No actions listed.</p>
    @endforelse
</div>

<div class="section section-break">
    <div class="section-title">5. Ranked likely causes · problem visuals</div>
    <p class="muted" style="margin-bottom:8px;">
        Same logic as the unlocked screen: photorealistic vehicle photo, red fault mark, and problem-zone explanation for each cause.
    </p>
    @forelse(($result['likely_causes'] ?? []) as $cause)
        <div class="box box-cause keep">
            <strong>{{ $cause['cause'] ?? $cause['title'] ?? 'Cause' }}</strong>
            @if(isset($cause['probability']))
                <span class="muted"> · {{ $cause['probability'] }}% probability</span>
            @endif

            @if(!empty($cause['image_src']))
                <div class="cause-visual">
                    <img src="{{ $cause['image_src'] }}" alt="{{ $cause['annotation']['label'] ?? ($cause['image_caption'] ?? ($cause['title'] ?? 'Cause visual')) }}">
                </div>
            @endif

            @if(!empty($cause['annotation']['meaning']) || !empty($cause['annotation']['label']))
                <div class="problem-zone">
                    <div class="tag">Problem zone</div>
                    @if(!empty($cause['annotation']['label']))
                        <div class="label">{{ $cause['annotation']['label'] }}</div>
                    @endif
                    @if(!empty($cause['annotation']['meaning']))
                        <p class="meaning">{{ $cause['annotation']['meaning'] }}</p>
                    @endif
                </div>
            @endif

            <div>{{ $cause['description'] ?? $cause['explanation'] ?? '' }}</div>
            @if(!empty($cause['typical_dtc_examples']))
                <div class="muted">
                    Typical codes:
                    {{ is_array($cause['typical_dtc_examples']) ? implode(', ', $cause['typical_dtc_examples']) : $cause['typical_dtc_examples'] }}
                </div>
            @endif
        </div>
    @empty
        <p class="muted">No causes listed.</p>
    @endforelse
</div>

@if(!empty($result['diagnostic_codes_to_check']))
<div class="section keep">
    <div class="section-title">Codes to check on OBD / VCDS</div>
    <ul>
        @foreach($result['diagnostic_codes_to_check'] as $code)
            <li>
                <strong>{{ is_array($code) ? ($code['code'] ?? '') : $code }}</strong>
                @if(is_array($code))
                    — {{ $code['meaning'] ?? '' }}
                    @if(!empty($code['related_cause']))
                        ({{ $code['related_cause'] }})
                    @endif
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endif

@if(!empty($result['estimated_costs']))
<div class="section keep">
    <div class="section-title">Guide costs (EUR)</div>
    <ul>
        @foreach($result['estimated_costs'] as $cost)
            <li>
                {{ $cost['service'] ?? 'Service' }}:
                €{{ $cost['min'] ?? '?' }} – €{{ $cost['max'] ?? '?' }}
            </li>
        @endforeach
    </ul>
    <p class="muted">Guide only. Workshop labour and parts can differ by region.</p>
</div>
@endif

@if(!empty($result['next_workshop_questions']))
<div class="section keep">
    <div class="section-title">Bring these answers to the workshop</div>
    <ul>
        @foreach($result['next_workshop_questions'] as $question)
            <li>{{ is_array($question) ? ($question['text'] ?? json_encode($question)) : $question }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(!empty($result['ai_insights']))
<div class="section keep">
    <div class="section-title">Also worth knowing</div>
    <ul>
        @foreach($result['ai_insights'] as $insight)
            <li>{{ is_array($insight) ? ($insight['text'] ?? json_encode($insight)) : $insight }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="section section-break keep">
    <div class="section-title">Glossary (for non-technical owners)</div>
    <table class="info-table">
        @foreach($glossary as $abbr => $meaning)
            <tr>
                <td class="k" style="width:18%">{{ $abbr }}</td>
                <td>{{ $meaning }}</td>
            </tr>
        @endforeach
    </table>
</div>

<div class="footer">
    <p>This document is an AI-assisted differential diagnosis evidence record from CarWise.ai. It is not a workshop invoice or legal certification.</p>
    <p>Read live fault codes before replacing parts. Always confirm repairs with a qualified technician.</p>
    <p>Engines used: {{ $engineLabel }} · © {{ date('Y') }} CarWise.ai</p>
</div>
</body>
</html>
