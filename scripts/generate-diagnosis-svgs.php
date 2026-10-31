<?php

$dir = dirname(__DIR__).'/public/images/diagnosis';
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$wrap = static function (string $title, string $subtitle, string $body): string {
    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 480" role="img" aria-label="{$title}">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#0f172a"/>
      <stop offset="55%" stop-color="#1e293b"/>
      <stop offset="100%" stop-color="#0ea5e9" stop-opacity="0.35"/>
    </linearGradient>
    <linearGradient id="panel" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#f8fafc"/>
      <stop offset="100%" stop-color="#e2e8f0"/>
    </linearGradient>
  </defs>
  <rect width="800" height="480" fill="url(#bg)"/>
  <rect x="28" y="28" width="744" height="424" rx="28" fill="url(#panel)" opacity="0.96"/>
  <text x="56" y="78" font-family="ui-sans-serif,system-ui,sans-serif" font-size="28" font-weight="700" fill="#0f172a">{$title}</text>
  <text x="56" y="108" font-family="ui-sans-serif,system-ui,sans-serif" font-size="15" fill="#64748b">{$subtitle}</text>
  {$body}
  <text x="56" y="430" font-family="ui-sans-serif,system-ui,sans-serif" font-size="12" fill="#94a3b8">CarWise.ai · educational diagram · not a substitute for live OBD scan</text>
</svg>
SVG;
};

$diagrams = [
    'check-engine' => [
        'Check engine light',
        'Instrument cluster warning — read codes before replacing parts',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="220" y="150" width="360" height="180" rx="24" fill="#fff" stroke="#cbd5e1"/><circle cx="300" cy="230" r="34" fill="#fef08a" stroke="#ca8a04"/><path d="M286 218c8-10 20-10 28 0" stroke="#854d0e" stroke-width="3"/><circle cx="300" cy="236" r="3" fill="#854d0e" stroke="none"/><rect x="360" y="200" width="160" height="14" rx="7" fill="#e2e8f0" stroke="none"/><rect x="360" y="230" width="120" height="10" rx="5" fill="#cbd5e1" stroke="none"/><rect x="360" y="255" width="140" height="10" rx="5" fill="#cbd5e1" stroke="none"/></g><text x="250" y="360" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Yellow CEL = fault stored in ECU · scan OBD/VCDS next</text>',
    ],
    'egr' => [
        'EGR valve',
        'Exhaust gas recirculation path into the intake',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M140 300c40-90 120-140 220-140h180" stroke="#64748b" stroke-width="18"/><path d="M140 300c40-90 120-140 220-140h180" stroke="#94a3b8" stroke-width="8"/><rect x="330" y="130" width="90" height="70" rx="12" fill="#38bdf8" stroke="#0369a1"/><path d="M375 200v50" stroke="#0369a1" stroke-width="6"/><path d="M200 300h280" stroke="#f97316" stroke-width="10"/><circle cx="200" cy="300" r="18" fill="#fb923c" stroke="#c2410c"/><circle cx="480" cy="300" r="18" fill="#fb923c" stroke="#c2410c"/><path d="M375 250c-40 20-80 35-140 45" stroke="#ea580c" stroke-dasharray="8 6"/></g><text x="318" y="172" font-family="ui-sans-serif,system-ui,sans-serif" font-size="13" font-weight="700" fill="#0c4a6e">EGR</text><text x="150" y="360" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Carbon buildup can stick the valve open/closed → CEL</text>',
    ],
    'dpf' => [
        'Diesel particulate filter',
        'Trap for soot in the exhaust stream',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><path d="M120 240h120" stroke="#64748b" stroke-width="22"/><rect x="240" y="170" width="280" height="140" rx="28" fill="#fff" stroke="#475569"/><path d="M270 200v80M310 200v80M350 200v80M390 200v80M430 200v80M470 200v80" stroke="#94a3b8" stroke-width="4"/><circle cx="300" cy="240" r="6" fill="#78716c" stroke="none"/><circle cx="360" cy="220" r="5" fill="#78716c" stroke="none"/><circle cx="420" cy="255" r="7" fill="#78716c" stroke="none"/><circle cx="450" cy="230" r="4" fill="#78716c" stroke="none"/><path d="M520 240h160" stroke="#64748b" stroke-width="22"/></g><text x="300" y="360" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">City driving can clog DPF → pressure rise → CEL</text>',
    ],
    'maf' => [
        'MAF sensor',
        'Measures air entering the engine',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><path d="M120 240h560" stroke="#94a3b8" stroke-width="40"/><path d="M120 240h560" stroke="#e2e8f0" stroke-width="26"/><rect x="340" y="160" width="120" height="160" rx="16" fill="#38bdf8" stroke="#0369a1"/><path d="M370 210h60M370 240h60M370 270h60" stroke="#0c4a6e" stroke-width="4"/><path d="M200 180c40 20 40 60 0 80" stroke="#0ea5e9" stroke-width="4"/><path d="M600 180c-40 20-40 60 0 80" stroke="#0ea5e9" stroke-width="4"/></g><text x="365" y="250" font-family="ui-sans-serif,system-ui,sans-serif" font-size="16" font-weight="700" fill="#0c4a6e">MAF</text><text x="220" y="370" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Dirty MAF → wrong fuel mix → rough idle / CEL</text>',
    ],
    'turbo' => [
        'Turbocharger',
        'Exhaust spins compressor to boost intake air',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="300" cy="240" r="70" fill="#fff" stroke="#64748b"/><circle cx="500" cy="240" r="70" fill="#fff" stroke="#f97316"/><path d="M300 240l50-30M300 240l50 10M300 240l20 50M300 240l-45 25M300 240l-40-35" stroke="#0ea5e9"/><path d="M500 240l50-30M500 240l50 10M500 240l20 50M500 240l-45 25M500 240l-40-35" stroke="#ea580c"/><path d="M370 240h60" stroke="#334155" stroke-width="10"/><path d="M180 240h50" stroke="#94a3b8" stroke-width="14"/><path d="M570 240h50" stroke="#fb923c" stroke-width="14"/></g><text x="260" y="360" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Boost leaks / wastegate faults often store P023x / P0299</text>',
    ],
    'injector' => [
        'Fuel injectors',
        'High-pressure fuel metering into cylinders',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><rect x="180" y="170" width="440" height="40" rx="12" fill="#334155" stroke="#0f172a"/><g fill="#38bdf8" stroke="#0369a1"><rect x="220" y="210" width="40" height="90" rx="8"/><rect x="320" y="210" width="40" height="90" rx="8"/><rect x="420" y="210" width="40" height="90" rx="8"/><rect x="520" y="210" width="40" height="90" rx="8"/></g><path d="M240 300v30M340 300v30M440 300v30M540 300v30" stroke="#0ea5e9" stroke-width="4"/></g><text x="210" y="370" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Misfire / imbalance codes often point to injector or rail issues</text>',
    ],
    'glow-plug' => [
        'Glow plugs',
        'Heat the combustion chamber for diesel cold starts',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><rect x="250" y="150" width="60" height="40" rx="8" fill="#94a3b8"/><path d="M280 190v120" stroke="#f97316" stroke-width="10"/><circle cx="280" cy="330" r="16" fill="#fdba74" stroke="#c2410c"/><rect x="370" y="150" width="60" height="40" rx="8" fill="#94a3b8"/><path d="M400 190v120" stroke="#f97316" stroke-width="10"/><circle cx="400" cy="330" r="16" fill="#fdba74" stroke="#c2410c"/><rect x="490" y="150" width="60" height="40" rx="8" fill="#94a3b8"/><path d="M520 190v120" stroke="#f97316" stroke-width="10"/><circle cx="520" cy="330" r="16" fill="#fdba74" stroke="#c2410c"/></g><text x="230" y="390" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Failed glow circuit → hard cold start + P067x codes</text>',
    ],
    'exhaust-pressure' => [
        'Exhaust pressure sensor',
        'Monitors pressure before/after DPF',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><path d="M140 260h520" stroke="#64748b" stroke-width="28"/><rect x="340" y="150" width="120" height="70" rx="14" fill="#38bdf8" stroke="#0369a1"/><path d="M380 220v40M420 220v40" stroke="#0369a1" stroke-width="5"/><circle cx="300" cy="260" r="10" fill="#f97316" stroke="#c2410c"/><circle cx="500" cy="260" r="10" fill="#f97316" stroke="#c2410c"/><path d="M300 250v-40h80M500 250v-40h-80" stroke="#ea580c" stroke-dasharray="6 5"/></g><text x="220" y="360" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Failed sensor → false DPF pressure readings → CEL</text>',
    ],
    'oxygen-sensor' => [
        'Oxygen / lambda sensor',
        'Reads oxygen in exhaust for fuel trim',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><path d="M160 280c80-40 160-40 240-10s160 40 240 10" stroke="#64748b" stroke-width="22"/><rect x="360" y="160" width="80" height="100" rx="12" fill="#a3e635" stroke="#4d7c0f"/><path d="M400 260v20" stroke="#4d7c0f" stroke-width="8"/><circle cx="400" cy="290" r="12" fill="#84cc16" stroke="#3f6212"/></g><text x="230" y="370" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Slow or biased O2 sensor skews mixture and lights CEL</text>',
    ],
    'catalytic' => [
        'Catalytic converter',
        'Converts harmful exhaust gases',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><path d="M120 240h140" stroke="#64748b" stroke-width="24"/><ellipse cx="400" cy="240" rx="140" ry="70" fill="#fff" stroke="#475569"/><path d="M300 210h200M300 240h200M300 270h200" stroke="#94a3b8"/><path d="M540 240h140" stroke="#64748b" stroke-width="24"/></g><text x="250" y="360" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Efficiency codes (P0420 family) need confirmed upstream faults first</text>',
    ],
    'cooling' => [
        'Cooling system',
        'Thermostat, radiator and coolant flow',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><rect x="180" y="160" width="160" height="160" rx="16" fill="#bae6fd" stroke="#0284c7"/><rect x="420" y="190" width="180" height="100" rx="16" fill="#e0f2fe" stroke="#0369a1"/><path d="M340 220h80M340 260h80" stroke="#0ea5e9" stroke-width="10"/><circle cx="260" cy="240" r="28" fill="#38bdf8" stroke="#0369a1"/></g><text x="230" y="370" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Stuck thermostat / low coolant → overheating warnings</text>',
    ],
    'battery' => [
        'Battery &amp; charging',
        '12V battery, alternator and cables',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><rect x="220" y="180" width="160" height="120" rx="14" fill="#fff" stroke="#334155"/><rect x="250" y="160" width="40" height="20" rx="4" fill="#22c55e" stroke="#15803d"/><rect x="310" y="160" width="40" height="20" rx="4" fill="#ef4444" stroke="#b91c1c"/><circle cx="520" cy="240" r="60" fill="#f8fafc" stroke="#64748b"/><path d="M490 240h60M520 210v60" stroke="#0ea5e9" stroke-width="6"/><path d="M380 240h80" stroke="#94a3b8" stroke-width="8"/></g><text x="220" y="370" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Low voltage can trigger multiple false ECU warnings</text>',
    ],
    'spark' => [
        'Ignition system',
        'Coils and spark plugs for petrol engines',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><rect x="250" y="150" width="70" height="90" rx="10" fill="#334155"/><path d="M285 240v70" stroke="#facc15" stroke-width="6"/><rect x="380" y="150" width="70" height="90" rx="10" fill="#334155"/><path d="M415 240v70" stroke="#facc15" stroke-width="6"/><rect x="510" y="150" width="70" height="90" rx="10" fill="#334155"/><path d="M545 240v70" stroke="#facc15" stroke-width="6"/></g><text x="240" y="370" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">Misfire codes often track to coil pack or plug wear</text>',
    ],
    'obd-scan' => [
        'OBD-II / VCDS scan',
        'Read live DTCs before guessing parts',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"><rect x="200" y="160" width="400" height="180" rx="20" fill="#0f172a" stroke="#334155"/><rect x="230" y="190" width="340" height="90" rx="10" fill="#022c22" stroke="#10b981"/><text x="260" y="245" font-family="ui-monospace,monospace" font-size="28" fill="#34d399">P0401  EGR</text><rect x="230" y="300" width="80" height="18" rx="4" fill="#334155" stroke="none"/><rect x="330" y="300" width="80" height="18" rx="4" fill="#334155" stroke="none"/><rect x="430" y="300" width="80" height="18" rx="4" fill="#334155" stroke="none"/></g><text x="210" y="390" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">A live code turns hypothesis into a targeted repair plan</text>',
    ],
    'engine-overview' => [
        'Engine systems',
        'Where intake, fuel, exhaust and electronics meet',
        '<g stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="260" y="160" width="280" height="180" rx="24" fill="#fff" stroke="#475569"/><circle cx="330" cy="230" r="28" fill="#bae6fd" stroke="#0284c7"/><circle cx="400" cy="250" r="34" fill="#fed7aa" stroke="#ea580c"/><circle cx="470" cy="230" r="28" fill="#d9f99d" stroke="#65a30d"/><path d="M200 220h60M540 220h60M400 340v40" stroke="#94a3b8" stroke-width="10"/></g><text x="210" y="410" font-family="ui-sans-serif,system-ui,sans-serif" font-size="14" fill="#334155">CarWise ranks likely systems — confirm with OBD before replacing parts</text>',
    ],
];

foreach ($diagrams as $key => [$title, $subtitle, $body]) {
    file_put_contents("{$dir}/{$key}.svg", $wrap($title, $subtitle, $body));
    echo "wrote {$key}.svg\n";
}
