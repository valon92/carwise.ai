<?php

namespace App\Services;

/**
 * Maps diagnostic cause titles to visual keys used for photorealistic vehicle photos.
 */
class DiagnosisVisualCatalog
{
    /**
     * Ordered keyword → visual key (first match wins).
     */
    private const KEYWORD_MAP = [
        'glow plug' => 'glow-plug',
        'glowplug' => 'glow-plug',
        'cold start' => 'glow-plug',
        'dpf' => 'dpf',
        'particulate' => 'dpf',
        'partikula' => 'dpf',
        'grimcave' => 'dpf',
        'egr' => 'egr',
        'recirculation' => 'egr',
        'riciklim' => 'egr',
        'recirkulim' => 'egr',
        'maf' => 'maf',
        'mass air' => 'maf',
        // Prefer specific sensor matches before generic "turbo" / TDI text.
        'exhaust pressure' => 'exhaust-pressure',
        'presionit të shkarkimeve' => 'exhaust-pressure',
        'presionit te shkarkimeve' => 'exhaust-pressure',
        'differential pressure' => 'exhaust-pressure',
        'sensor i presionit' => 'exhaust-pressure',
        'turbocharger' => 'turbo',
        'wastegate' => 'turbo',
        'boost pressure' => 'turbo',
        'boost leak' => 'turbo',
        'injector' => 'injector',
        'injectors' => 'injector',
        'fuel system' => 'injector',
        'fuel rail' => 'injector',
        'oxygen sensor' => 'oxygen-sensor',
        'o2 sensor' => 'oxygen-sensor',
        'lambda' => 'oxygen-sensor',
        'catalytic' => 'catalytic',
        'catalyst' => 'catalytic',
        'thermostat' => 'cooling',
        'coolant' => 'cooling',
        'overheat' => 'cooling',
        'battery' => 'battery',
        'alternator' => 'battery',
        'charging' => 'battery',
        'spark plug' => 'spark',
        'ignition coil' => 'spark',
        'obd' => 'obd-scan',
        'vcds' => 'obd-scan',
        'check engine' => 'check-engine',
        'drita e verdh' => 'check-engine',
        'warning light' => 'check-engine',
        'cel' => 'check-engine',
    ];

    public static function keys(): array
    {
        return [
            'check-engine',
            'egr',
            'dpf',
            'maf',
            'turbo',
            'injector',
            'glow-plug',
            'exhaust-pressure',
            'oxygen-sensor',
            'catalytic',
            'cooling',
            'battery',
            'spark',
            'obd-scan',
            'engine-overview',
        ];
    }

    public static function resolveKey(?string $title, ?string $description = null): string
    {
        // Title first — descriptions often mention TDI/Turbocharged and pollute matching.
        foreach ([trim((string) $title), trim((string) $description)] as $chunk) {
            $haystack = mb_strtolower($chunk);
            if ($haystack === '') {
                continue;
            }
            foreach (self::KEYWORD_MAP as $needle => $key) {
                if (str_contains($haystack, $needle)) {
                    return $key;
                }
            }
        }

        return 'engine-overview';
    }

    public static function labelFor(string $key): string
    {
        return match ($key) {
            'check-engine' => 'Check engine light / instrument cluster',
            'egr' => 'EGR valve & recirculation path',
            'dpf' => 'Diesel particulate filter (DPF)',
            'maf' => 'Mass air flow (MAF) sensor',
            'turbo' => 'Turbocharger & boost circuit',
            'injector' => 'Fuel injectors & rail',
            'glow-plug' => 'Glow plugs / cold-start system',
            'exhaust-pressure' => 'Exhaust pressure sensor',
            'oxygen-sensor' => 'Oxygen / lambda sensor',
            'catalytic' => 'Catalytic converter',
            'cooling' => 'Cooling system & thermostat',
            'battery' => 'Battery & charging system',
            'spark' => 'Ignition coils & spark plugs',
            'obd-scan' => 'OBD-II / VCDS scan port',
            default => 'Engine systems overview',
        };
    }

    /**
     * Concrete photorealistic subject for image generation (vehicle-agnostic phrasing).
     */
    public static function photoSubject(string $key): string
    {
        return match ($key) {
            'check-engine' => 'illuminated yellow check-engine warning light on the dashboard instrument cluster',
            'egr' => 'close-up of the EGR valve and exhaust recirculation pipework on the engine',
            'dpf' => 'diesel particulate filter and exhaust section under the car / in the exhaust line',
            'maf' => 'mass air flow sensor in the intake air hose near the airbox',
            'turbo' => 'turbocharger assembly on the exhaust side of the engine bay',
            'injector' => 'fuel rail and diesel/petrol injectors on the engine',
            'glow-plug' => 'glow plug connectors and cylinder head area for diesel cold start',
            'exhaust-pressure' => 'exhaust differential pressure sensor and small vacuum/pressure hoses near the DPF',
            'oxygen-sensor' => 'oxygen / lambda sensor screwed into the exhaust pipe',
            'catalytic' => 'catalytic converter section of the exhaust system',
            'cooling' => 'thermostat housing and coolant hoses in the engine bay',
            'battery' => '12V car battery and alternator charging cables in the engine bay',
            'spark' => 'ignition coil packs and spark plug wells on a petrol engine',
            'obd-scan' => 'OBD-II diagnostic port under the dashboard with a scan tool plugged in',
            default => 'engine bay overview during a workshop inspection',
        };
    }

    /**
     * Red problem-zone annotation for fullscreen viewer (hotspot % of image).
     */
    public static function annotationFor(string $key, ?string $language = 'en', ?string $causeTitle = null): array
    {
        $sq = str_starts_with(strtolower((string) $language), 'sq');

        $map = [
            'check-engine' => [
                'label' => $sq ? 'Drita e motorit (CEL)' : 'Check engine light',
                'meaning' => $sq
                    ? 'Zona e kuqe tregon dritën e verdhë në panel — ECU ka ruajtur një gabim. Lexo kodin OBD/VCDS para se të ndërrosh pjesë.'
                    : 'Red zone marks the dashboard warning — the ECU stored a fault. Read OBD/VCDS before replacing parts.',
                'hotspot' => ['x' => 28, 'y' => 30, 'w' => 22, 'h' => 28],
            ],
            'egr' => [
                'label' => $sq ? 'Valvula EGR' : 'EGR valve',
                'meaning' => $sq
                    ? 'Zona e kuqe është valvula EGR / tubacioni i riciklimit. Karboni këtu bllokon valvulin dhe ndez dritën e motorit, sidomos në TDI me km të larta.'
                    : 'Red zone is the EGR valve / recirculation path. Carbon here sticks the valve and triggers the CEL on high-mileage diesels.',
                'hotspot' => ['x' => 38, 'y' => 28, 'w' => 30, 'h' => 34],
            ],
            'dpf' => [
                'label' => $sq ? 'Filtri DPF' : 'DPF filter',
                'meaning' => $sq
                    ? 'Zona e kuqe është DPF në linjën e shkarkimit. Bllokimi nga qyteti rrit presionin dhe ndez dritën e motorit.'
                    : 'Red zone is the DPF in the exhaust line. City driving soot raises pressure and lights the CEL.',
                'hotspot' => ['x' => 34, 'y' => 32, 'w' => 34, 'h' => 30],
            ],
            'maf' => [
                'label' => $sq ? 'Sensori MAF' : 'MAF sensor',
                'meaning' => $sq
                    ? 'Zona e kuqe është sensori MAF në tubin e ajrit. Ndotja jep lexime të gabuara të ajrit dhe përzierje të gabuar karburanti.'
                    : 'Red zone is the MAF in the intake hose. Dirt skews airflow readings and fuel mixture.',
                'hotspot' => ['x' => 40, 'y' => 30, 'w' => 24, 'h' => 36],
            ],
            'turbo' => [
                'label' => $sq ? 'Turbokompresori' : 'Turbocharger',
                'meaning' => $sq
                    ? 'Zona e kuqe është turbo / tubat e boost. Rrjedhjet ose wastegate e dëmtuar ulin shtytjen dhe ruajnë kode P023x / P0299.'
                    : 'Red zone is the turbo / boost plumbing. Leaks or wastegate faults cut boost and store P023x / P0299.',
                'hotspot' => ['x' => 36, 'y' => 30, 'w' => 32, 'h' => 34],
            ],
            'injector' => [
                'label' => $sq ? 'Injektorët / rail' : 'Injectors / fuel rail',
                'meaning' => $sq
                    ? 'Zona e kuqe është rail-i dhe injektorët. Balanca e keqe ose rrjedhja shkakton mungesë djegieje dhe dritë motori.'
                    : 'Red zone is the rail and injectors. Imbalance or leakage causes misfire and CEL.',
                'hotspot' => ['x' => 30, 'y' => 28, 'w' => 40, 'h' => 30],
            ],
            'glow-plug' => [
                'label' => $sq ? 'Glow plugs' : 'Glow plugs',
                'meaning' => $sq
                    ? 'Zona e kuqe janë glow plugs / lidhësit. Dështimi vështirëson nisjen në të ftohtë dhe jep kode P067x.'
                    : 'Red zone is the glow plugs / connectors. Failure hurts cold starts and stores P067x.',
                'hotspot' => ['x' => 34, 'y' => 26, 'w' => 36, 'h' => 34],
            ],
            'exhaust-pressure' => [
                'label' => $sq ? 'Sensori i presionit të shkarkimit' : 'Exhaust pressure sensor',
                'meaning' => $sq
                    ? 'Zona e kuqe është sensori i presionit (para/pas DPF). Sensor i dëmtuar jep alarme false DPF dhe ndez dritën.'
                    : 'Red zone is the differential pressure sensor near the DPF. A bad sensor fakes DPF faults and lights the CEL.',
                'hotspot' => ['x' => 40, 'y' => 26, 'w' => 26, 'h' => 32],
            ],
            'oxygen-sensor' => [
                'label' => $sq ? 'Sensori lambda / O2' : 'Oxygen / lambda sensor',
                'meaning' => $sq
                    ? 'Zona e kuqe është sensori O2 në shkarkim. Lexime të ngadalta shtrembërojnë përzierjen dhe ndezin CEL.'
                    : 'Red zone is the O2 sensor in the exhaust. Slow readings skew mixture and light the CEL.',
                'hotspot' => ['x' => 42, 'y' => 30, 'w' => 22, 'h' => 34],
            ],
            'catalytic' => [
                'label' => $sq ? 'Katalizatori' : 'Catalytic converter',
                'meaning' => $sq
                    ? 'Zona e kuqe është katalizatori. Eficienca e ulët shpesh vjen nga gabime upstream — konfirmo kodet para zëvendësimit.'
                    : 'Red zone is the catalytic converter. Efficiency codes often need upstream faults confirmed first.',
                'hotspot' => ['x' => 34, 'y' => 32, 'w' => 32, 'h' => 28],
            ],
            'cooling' => [
                'label' => $sq ? 'Sistemi i ftohjes' : 'Cooling system',
                'meaning' => $sq
                    ? 'Zona e kuqe është termostati / tubat e ftohësit. Bllokimi ose mungesa e antifrigut shkakton mbinxehje.'
                    : 'Red zone is the thermostat / coolant hoses. Stuck flow or low coolant causes overheating.',
                'hotspot' => ['x' => 30, 'y' => 28, 'w' => 34, 'h' => 34],
            ],
            'battery' => [
                'label' => $sq ? 'Bateria / karikimi' : 'Battery / charging',
                'meaning' => $sq
                    ? 'Zona e kuqe është bateria 12V / alternatori. Tensioni i ulët mund të shkaktojë alarme false në ECU.'
                    : 'Red zone is the 12V battery / alternator. Low voltage can trigger false ECU warnings.',
                'hotspot' => ['x' => 28, 'y' => 30, 'w' => 30, 'h' => 32],
            ],
            'spark' => [
                'label' => $sq ? 'Bobinat / spark plugs' : 'Ignition coils / plugs',
                'meaning' => $sq
                    ? 'Zona e kuqe janë bobinat e ndezjes. Dështimi jep misfire dhe dritë motori në motorë benzinë.'
                    : 'Red zone is the ignition coils. Failure causes misfire and CEL on petrol engines.',
                'hotspot' => ['x' => 32, 'y' => 26, 'w' => 36, 'h' => 34],
            ],
            'obd-scan' => [
                'label' => $sq ? 'Porta OBD / VCDS' : 'OBD / VCDS port',
                'meaning' => $sq
                    ? 'Zona e kuqe është porta diagnostike. Leximi i kodit të gjallë e kthen hipotezën në plan të saktë riparimi.'
                    : 'Red zone is the diagnostic port. A live code turns a hypothesis into a precise repair plan.',
                'hotspot' => ['x' => 36, 'y' => 34, 'w' => 30, 'h' => 28],
            ],
            'engine-overview' => [
                'label' => $sq ? 'Zona e motorit' : 'Engine bay focus',
                'meaning' => $sq
                    ? 'Zona e kuqe tregon sistemin që duhet inspekuar së pari. Konfirmo me skaner para zëvendësimit të pjesëve.'
                    : 'Red zone marks the system to inspect first. Confirm with a scanner before replacing parts.',
                'hotspot' => ['x' => 34, 'y' => 28, 'w' => 32, 'h' => 34],
            ],
        ];

        $row = $map[$key] ?? $map['engine-overview'];
        if (is_string($causeTitle) && trim($causeTitle) !== '') {
            $clean = trim(preg_replace('/\s*\([^)]*\)\s*/u', ' ', $causeTitle) ?? $causeTitle);
            $clean = preg_replace('/\s+/u', ' ', $clean) ?? $clean;
            if ($clean !== '') {
                $row['label'] = mb_substr($clean, 0, 80);
            }
        }

        return $row;
    }

    public static function publicPath(string $key): string
    {
        $safe = in_array($key, self::keys(), true) ? $key : 'engine-overview';

        return '/images/diagnosis/'.$safe.'.svg';
    }

    public static function absoluteUrl(string $key): string
    {
        return url(self::publicPath($key));
    }
}
