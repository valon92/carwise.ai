<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3B82F6;
            padding-bottom: 20px;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #3B82F6;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #6B7280;
            font-size: 16px;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 15px;
            padding: 10px;
            background: #F3F4F6;
            border-left: 4px solid #3B82F6;
        }
        
        .vehicle-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .vehicle-row {
            display: table-row;
        }
        
        .vehicle-label {
            display: table-cell;
            font-weight: bold;
            padding: 8px;
            width: 30%;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
        }
        
        .vehicle-value {
            display: table-cell;
            padding: 8px;
            border: 1px solid #E5E7EB;
        }
        
        .symptoms-list {
            list-style: none;
            padding: 0;
        }
        
        .symptoms-list li {
            padding: 5px 0;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .symptoms-list li:before {
            content: "• ";
            color: #3B82F6;
            font-weight: bold;
        }
        
        .diagnosis-result {
            background: #F0F9FF;
            border: 1px solid #0EA5E9;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }
        
        .confidence-badge {
            display: inline-block;
            background: #10B981;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .problem-title {
            font-size: 20px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 10px;
        }
        
        .causes-list {
            margin: 15px 0;
        }
        
        .cause-item {
            background: #FEF3C7;
            border: 1px solid #F59E0B;
            border-radius: 6px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .cause-title {
            font-weight: bold;
            color: #92400E;
            margin-bottom: 8px;
        }
        
        .cause-description {
            color: #78350F;
            margin-bottom: 8px;
        }
        
        .probability {
            font-size: 12px;
            color: #B45309;
            font-weight: bold;
        }
        
        .solutions-list {
            margin: 15px 0;
        }
        
        .solution-item {
            background: #ECFDF5;
            border: 1px solid #10B981;
            border-radius: 6px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .solution-title {
            font-weight: bold;
            color: #065F46;
            margin-bottom: 8px;
        }
        
        .solution-description {
            color: #047857;
        }
        
        .urgency {
            background: #FEE2E2;
            border: 1px solid #EF4444;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .urgency-title {
            font-weight: bold;
            color: #991B1B;
            margin-bottom: 8px;
        }
        
        .urgency-text {
            color: #B91C1C;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 12px;
        }
        
        .generated-info {
            background: #F3F4F6;
            padding: 10px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 12px;
            color: #6B7280;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CarWise.ai</div>
        <div class="subtitle">AI-Powered Vehicle Diagnosis Report</div>
    </div>

    <div class="section">
        <div class="section-title">Vehicle Information</div>
        <div class="vehicle-info">
            <div class="vehicle-row">
                <div class="vehicle-label">Make</div>
                <div class="vehicle-value">{{ $vehicle['make'] }}</div>
            </div>
            <div class="vehicle-row">
                <div class="vehicle-label">Model</div>
                <div class="vehicle-value">{{ $vehicle['model'] }}</div>
            </div>
            <div class="vehicle-row">
                <div class="vehicle-label">Year</div>
                <div class="vehicle-value">{{ $vehicle['year'] }}</div>
            </div>
            @if(isset($vehicle['mileage']) && $vehicle['mileage'])
            <div class="vehicle-row">
                <div class="vehicle-label">Mileage</div>
                <div class="vehicle-value">{{ number_format($vehicle['mileage']) }} km</div>
            </div>
            @endif
            @if(isset($vehicle['engine_type']) && $vehicle['engine_type'])
            <div class="vehicle-row">
                <div class="vehicle-label">Engine Type</div>
                <div class="vehicle-value">{{ $vehicle['engine_type'] }}</div>
            </div>
            @endif
            @if(isset($vehicle['engine_size']) && $vehicle['engine_size'])
            <div class="vehicle-row">
                <div class="vehicle-label">Engine Size</div>
                <div class="vehicle-value">{{ $vehicle['engine_size'] }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Reported Symptoms</div>
        @if(count($symptoms) > 0)
            <ul class="symptoms-list">
                @foreach($symptoms as $symptom)
                    <li>{{ $symptom }}</li>
                @endforeach
            </ul>
        @else
            <p>No specific symptoms reported.</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Problem Description</div>
        <p>{{ $description }}</p>
    </div>

    <div class="section">
        <div class="section-title">AI Diagnosis Results</div>
        <div class="diagnosis-result">
            @if(isset($result['confidence']))
                <div class="confidence-badge">{{ $result['confidence'] }}% Confidence</div>
            @endif
            
            @if(isset($result['problem_title']))
                <div class="problem-title">{{ $result['problem_title'] }}</div>
            @endif
            
            @if(isset($result['problem_description']))
                <p>{{ $result['problem_description'] }}</p>
            @endif
            
            @if(isset($result['likely_causes']) && count($result['likely_causes']) > 0)
                <div class="causes-list">
                    <h4>Likely Causes:</h4>
                    @foreach($result['likely_causes'] as $cause)
                        <div class="cause-item">
                            <div class="cause-title">{{ $cause['cause'] ?? 'Unknown Cause' }}</div>
                            <div class="cause-description">{{ $cause['description'] ?? 'No description available' }}</div>
                            @if(isset($cause['probability']))
                                <div class="probability">{{ $cause['probability'] }}% probability</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
            
            @if(isset($result['recommended_actions']) && count($result['recommended_actions']) > 0)
                <div class="solutions-list">
                    <h4>Recommended Actions:</h4>
                    @foreach($result['recommended_actions'] as $action)
                        <div class="solution-item">
                            <div class="solution-title">{{ $action['action'] ?? 'Recommended Action' }}</div>
                            <div class="solution-description">{{ $action['description'] ?? 'No description available' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            @if(isset($result['urgency']) && $result['urgency'] === 'high')
                <div class="urgency">
                    <div class="urgency-title">⚠️ Immediate Attention Required</div>
                    <div class="urgency-text">This issue requires immediate professional attention to prevent further damage or safety risks. Please contact a certified mechanic as soon as possible.</div>
                </div>
            @endif
        </div>
    </div>

    <div class="generated-info">
        <strong>Report Generated:</strong> {{ date('F j, Y \a\t g:i A', strtotime($generated_at)) }}<br>
        <strong>AI Provider:</strong> {{ $result['ai_provider'] ?? 'CarWise AI' }}<br>
        <strong>Report ID:</strong> {{ substr(md5($generated_at . $vehicle['make'] . $vehicle['model']), 0, 8) }}
    </div>

    <div class="footer">
        <p>This report was generated by CarWise.ai - Your AI-powered vehicle diagnosis assistant.</p>
        <p>For professional repair services, please consult a certified mechanic.</p>
        <p>© {{ date('Y') }} CarWise.ai. All rights reserved.</p>
    </div>
</body>
</html>

