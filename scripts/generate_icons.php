<?php
/**
 * PWA Icon Generator
 * Generates all required PNG icons for the web app manifest and iOS PWA support.
 *
 * Run once:  php scripts/generate_icons.php
 * Docker:    Called automatically during image build (see Dockerfile)
 */
declare(strict_types=1);

if (!extension_loaded('gd')) {
    fwrite(STDERR, "Error: GD extension is required.\n");
    exit(1);
}

$iconsDir = __DIR__ . '/../icons';
if (!is_dir($iconsDir)) {
    mkdir($iconsDir, 0755, true);
}

$icons = [
    ['size' => 192,  'file' => 'icon-192.png',           'maskable' => false],
    ['size' => 512,  'file' => 'icon-512.png',           'maskable' => false],
    ['size' => 512,  'file' => 'icon-maskable.png',      'maskable' => true],
    ['size' => 180,  'file' => 'apple-touch-icon.png',   'maskable' => false],
    ['size' => 152,  'file' => 'apple-touch-icon-152.png','maskable' => false],
    ['size' => 120,  'file' => 'apple-touch-icon-120.png','maskable' => false],
];

foreach ($icons as ['size' => $size, 'file' => $file, 'maskable' => $maskable]) {
    generateIcon($size, "$iconsDir/$file", $maskable);
    echo "✓ icons/$file  ({$size}×{$size}" . ($maskable ? ', maskable' : '') . ")\n";
}

echo "\nDone. " . count($icons) . " icons generated in icons/\n";

// ---------------------------------------------------------------------------

/**
 * Draws a cat face icon on a purple background and saves it as PNG.
 *
 * All drawing coordinates are defined on a 512×512 reference canvas and
 * scaled proportionally to the target size.
 *
 * @param int    $size      Target icon size in pixels (square)
 * @param string $path      Absolute path to save the PNG file
 * @param bool   $maskable  If true, shrinks the drawing to 70% to satisfy
 *                          the PWA maskable icon safe-area requirement
 */
function generateIcon(int $size, string $path, bool $maskable): void
{
    $img = imagecreatetruecolor($size, $size);

    $purple = imagecolorallocate($img, 108, 92, 231);   // #6c5ce7
    $white  = imagecolorallocate($img, 255, 255, 255);

    // Background
    imagefill($img, 0, 0, $purple);

    // For maskable icons the safe area is the central 80%.
    // We use 70% scale to give a comfortable breathing room.
    $scale = $maskable ? ($size / 512) * 0.70 : $size / 512;
    $ox    = $maskable ? (int)(($size - ($size * 0.70)) / 2) : 0;
    $oy    = $maskable ? (int)(($size - ($size * 0.70)) / 2) : 0;

    // Helpers: scale a coordinate from the 512-ref system to target size
    $cx = fn(float $x): int => (int)round($x * $scale + $ox);
    $cy = fn(float $y): int => (int)round($y * $scale + $oy);
    $cr = fn(float $r): int => (int)round($r * $scale);

    // ── Head (large circle, white) ──────────────────────────────────────────
    imagefilledellipse($img, $cx(256), $cy(320), $cr(340), $cr(340), $white);

    // ── Left ear (triangle, white) ─────────────────────────────────────────
    imagefilledpolygon($img, [
        $cx(110), $cy(250),
        $cx(170), $cy(90),
        $cx(235), $cy(235),
    ], $white);

    // ── Right ear (triangle, white) ────────────────────────────────────────
    imagefilledpolygon($img, [
        $cx(277), $cy(235),
        $cx(342), $cy(90),
        $cx(402), $cy(250),
    ], $white);

    // ── Inner left ear (purple — adds depth) ──────────────────────────────
    imagefilledpolygon($img, [
        $cx(135), $cy(242),
        $cx(170), $cy(118),
        $cx(218), $cy(237),
    ], $purple);

    // ── Inner right ear (purple) ───────────────────────────────────────────
    imagefilledpolygon($img, [
        $cx(294), $cy(237),
        $cx(342), $cy(118),
        $cx(377), $cy(242),
    ], $purple);

    // ── Left eye (filled circle, purple) ──────────────────────────────────
    imagefilledellipse($img, $cx(205), $cy(295), $cr(52), $cr(52), $purple);

    // ── Right eye (filled circle, purple) ─────────────────────────────────
    imagefilledellipse($img, $cx(307), $cy(295), $cr(52), $cr(52), $purple);

    // ── Nose (small triangle, purple) ─────────────────────────────────────
    imagefilledpolygon($img, [
        $cx(243), $cy(330),
        $cx(269), $cy(330),
        $cx(256), $cy(348),
    ], $purple);

    imagepng($img, $path);
    imagedestroy($img);
}
