<?php

namespace App\Services;

class PortfolioAnalyzer
{
    // Format gambar yang diterima
    private const IMAGE_FORMATS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'gif',
        'bmp',
        'heic',
        'heif',
        'tiff',
        'tif',
        'avif',
    ];

    // Kata kunci nama file yang mengindikasikan proses/WIP
    private const WIP_KEYWORDS = [
        'wip',
        'sketch',
        'draft',
        'process',
        'progress',
        'layer',
        'layers',
        'lineart',
        'line',
        'rough',
        'thumbnail',
        'study',
        'ref',
        'reference',
        'base',
        'flat',
        'shade',
        'shading',
        'highlight',
        'color',
        'coloring',
        'detail',
        'cleanup',
        'ink',
        'inking',
        'step',
        'stage',
        'before',
        'after',
        // Bahasa Indonesia
        'sketsa',
        'proses',
        'lapisan',
        'warna',
        'dasar',
        'bayangan',
        'garis',
    ];

    /**
     * Analisis array file upload dan return skor + breakdown + catatan.
     *
     * @param  array  $files  Array dari $request->file('portfolio_files')
     * @return array  ['score' => int, 'notes' => string, 'breakdown' => array]
     */
    public function analyze(array $files): array
    {
        $fileData = $this->extractFileData($files);

        $breakdown = [
            'file_count' => $this->scoreFileCount($fileData),
            'resolution' => $this->scoreResolution($fileData),
            'wip'        => $this->scoreWip($fileData),
            'file_size'  => $this->scoreFileSize($fileData),
            'social'     => [
                'score' => 0,
                'note'  => 'Dinilai manual oleh admin berdasarkan pemeriksaan akun sosial media.',
            ],
        ];

        $total = collect($breakdown)->sum('score');
        $notes = $this->buildNotes($breakdown, $fileData);

        return [
            'score'     => min(100, $total),
            'notes'     => $notes,
            'breakdown' => $breakdown,
        ];
    }

    // ────────────────────────────────────────
    // EXTRACT DATA DARI FILE
    // ────────────────────────────────────────
    private function extractFileData(array $files): array
    {
        $data = [];
        foreach ($files as $file) {
            $name    = strtolower($file->getClientOriginalName());
            $ext     = strtolower($file->getClientOriginalExtension());
            $base    = strtolower(pathinfo($name, PATHINFO_FILENAME));
            $isImage = in_array($ext, self::IMAGE_FORMATS);

            // Coba baca dimensi gambar
            $width = $height = null;
            if ($isImage) {
                try {
                    [$width, $height] = getimagesize($file->getPathname()) ?: [null, null];
                } catch (\Throwable) {
                    // getimagesize gagal — skip saja
                }
            }

            $data[] = [
                'name'     => $name,
                'ext'      => $ext,
                'size'     => $file->getSize(),
                'is_image' => $isImage,
                'is_wip'   => $this->isWipFile($base),
                'width'    => $width,
                'height'   => $height,
            ];
        }
        return $data;
    }

    private function isWipFile(string $basename): bool
    {
        foreach (self::WIP_KEYWORDS as $kw) {
            if (str_contains($basename, $kw)) return true;
        }
        return false;
    }

    // ────────────────────────────────────────
    // SCORING: JUMLAH FILE (maks 25)
    // ────────────────────────────────────────
    private function scoreFileCount(array $data): array
    {
        $count = count($data);
        $score = 0;
        $notes = [];

        if ($count >= 8) {
            $score = 25;
        } elseif ($count >= 6) {
            $score = 20;
        } elseif ($count >= 4) {
            $score = 14;
        } elseif ($count >= 3) {
            $score = 8;
        } else {
            $score = 3;
            $notes[] = "Hanya {$count} file diupload. Lebih banyak karya memperkuat penilaian.";
        }

        return [
            'score' => $score,
            'note'  => empty($notes) ? null : implode(' ', $notes),
        ];
    }

    // ────────────────────────────────────────
    // SCORING: RESOLUSI GAMBAR (maks 30)
    // ────────────────────────────────────────
    private function scoreResolution(array $data): array
    {
        $images = array_filter($data, fn($f) => $f['is_image'] && $f['width'] !== null);
        $images = array_values($images);

        if (empty($images)) {
            return ['score' => 5, 'note' => 'Resolusi gambar tidak dapat dibaca.'];
        }

        $highRes   = 0; // >= 1920px
        $medRes    = 0; // >= 800px
        $lowRes    = 0; // < 800px
        $notes     = [];

        foreach ($images as $img) {
            $maxDim = max($img['width'], $img['height']);
            if ($maxDim >= 1920)     $highRes++;
            elseif ($maxDim >= 800)  $medRes++;
            else                     $lowRes++;
        }

        $total  = count($images);
        $score  = 0;

        // High res per gambar (maks 20)
        $score += min(20, $highRes * 7);

        // Med res per gambar (maks 10 sisa)
        $score += min(10, $medRes * 4);

        // Low res — tidak mendapat poin, tapi beri catatan
        if ($lowRes > 0) {
            $pct = round($lowRes / $total * 100);
            $notes[] = "{$lowRes} dari {$total} gambar beresolusi rendah (<800px). Karya digital asli biasanya beresolusi tinggi.";
        }

        return [
            'score' => min(30, $score),
            'note'  => empty($notes) ? null : implode(' ', $notes),
        ];
    }

    // ────────────────────────────────────────
    // SCORING: INDIKASI WIP (maks 20)
    // ────────────────────────────────────────
    private function scoreWip(array $data): array
    {
        $wipCount = count(array_filter($data, fn($f) => $f['is_wip']));
        $score    = 0;
        $notes    = [];

        if ($wipCount >= 3) {
            $score = 20;
        } elseif ($wipCount >= 2) {
            $score = 14;
        } elseif ($wipCount >= 1) {
            $score = 8;
        } else {
            $score = 0;
            $notes[] = 'Tidak ada file dengan nama mengandung kata kunci proses (sketch, wip, draft, lineart, dll). Nama file WIP meningkatkan skor.';
        }

        return [
            'score' => $score,
            'note'  => empty($notes) ? null : implode(' ', $notes),
        ];
    }

    // ────────────────────────────────────────
    // SCORING: UKURAN FILE (maks 25)
    // ────────────────────────────────────────
    private function scoreFileSize(array $data): array
    {
        $images = array_filter($data, fn($f) => $f['is_image']);
        $images = array_values($images);

        if (empty($images)) {
            return ['score' => 5, 'note' => null];
        }

        $avgSize  = collect($images)->avg('size');
        $sizes    = array_column($images, 'size');
        $variance = count($sizes) > 1 ? (max($sizes) - min($sizes)) : 0;
        $score    = 0;
        $notes    = [];

        // Rata-rata ukuran (maks 18)
        if ($avgSize >= 5 * 1024 * 1024) {
            $score += 18;
        } elseif ($avgSize >= 2 * 1024 * 1024) {
            $score += 13;
        } elseif ($avgSize >= 800 * 1024) {
            $score += 8;
        } elseif ($avgSize >= 300 * 1024) {
            $score += 4;
        } else {
            $score += 1;
            $kb = round($avgSize / 1024);
            $notes[] = "Ukuran rata-rata gambar sangat kecil ({$kb}KB). Karya digital beresolusi tinggi biasanya lebih dari 300KB.";
        }

        // Variasi ukuran antara file (maks 7) — tanda ada karya berbeda tahap
        if ($variance >= 3 * 1024 * 1024) {
            $score += 7;
        } elseif ($variance >= 1 * 1024 * 1024) {
            $score += 4;
        } elseif ($variance >= 300 * 1024) {
            $score += 2;
        }

        return [
            'score' => min(25, $score),
            'note'  => empty($notes) ? null : implode(' ', $notes),
        ];
    }

    // ────────────────────────────────────────
    // BUILD CATATAN RINGKASAN UNTUK ADMIN
    // ────────────────────────────────────────
    private function buildNotes(array $breakdown, array $fileData): string
    {
        $total = collect($breakdown)->sum('score');

        $level = match (true) {
            $total >= 75 => 'Portofolio memiliki bukti kuat keaslian karya.',
            $total >= 50 => 'Portofolio cukup meyakinkan, ada beberapa kekurangan.',
            $total >= 30 => 'Portofolio kurang lengkap, perlu perhatian ekstra dari admin.',
            default      => 'Portofolio sangat minim bukti — perlu verifikasi manual yang cermat.',
        };

        $count   = count($fileData);
        $wipCount = count(array_filter($fileData, fn($f) => $f['is_wip']));

        $notes = $level
            . "\n\nJumlah file: {$count}"
            . ", mengandung kata kunci WIP: {$wipCount} file.";

        // Kumpulkan poin masalah dari breakdown
        $problems = [];
        foreach ($breakdown as $key => $item) {
            if ($key === 'social') continue;
            if (!empty($item['note'])) {
                $problems[] = '• ' . $item['note'];
            }
        }

        if (!empty($problems)) {
            $notes .= "\n\nPoin yang perlu diperhatikan admin:\n" . implode("\n", $problems);
        }

        return $notes;
    }
}
