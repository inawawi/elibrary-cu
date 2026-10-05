<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item';
    protected $primaryKey = 'item_id';
    public $timestamps = false;

    protected $fillable = [
        'biblio_id',
        'call_number',
        'coll_type_id',
        'item_code',
        'inventory_code',
        'received_date',
        'supplier_id',
        'order_no',
        'location_id',
        'order_date',
        'item_status_id',
        'site',
        'source',
        'invoice',
        'price',
        'price_currency',
        'invoice_date',
        'input_date',
        'last_update',
        'uid',
    ];

    public function biblio()
    {
        return $this->belongsTo(Biblio::class, 'biblio_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function collType()
    {
        return $this->belongsTo(CollType::class, 'coll_type_id');
    }

    public function itemStatus()
    {
        return $this->belongsTo(ItemStatus::class, 'item_status_id', 'item_status_id');
    }

    public function activeLoan()
    {
        return $this->hasOne(Loan::class, 'item_code', 'item_code')->where('is_return', 0);
    }

    public function isAvailable(): bool
    {
        return !$this->activeLoan()->exists();
    }

    public function getBarcodeSvgAttribute(): string
    {
        return \App\Services\BarcodeService::getBarcodeSVG($this->item_code);
    }

    /**
     * Buat nomor barcode / kode eksemplar otomatis:
     * - B: Buku (contoh B00001)
     * - R: Jurnal / Referensi (contoh R00001)
     * - S: Skripsi (contoh S00001)
     */
    public static function generateNextCode(string $prefix = 'B'): string
    {
        $prefix = strtoupper(trim($prefix));

        $codes = self::where('item_code', 'like', $prefix . '%')
            ->orderByRaw('LENGTH(item_code) DESC, item_code DESC')
            ->pluck('item_code');

        $maxNum = 0;
        foreach ($codes as $code) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)/i', $code, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $nextNum = $maxNum + 1;
        return sprintf('%s%05d', $prefix, $nextNum);
    }

    /**
     * Buat beberapa kode eksemplar berurutan sekaligus:
     * Jika startingCode diisi, gunakan itu sebagai nomor awal.
     * Jika tidak diisi, lanjutkan dari nomor urut terakhir di database.
     */
    public static function generateMultipleNextCodes(string $prefix = 'B', int $count = 1, ?string $startingCode = null): array
    {
        $prefix = strtoupper(trim($prefix));
        $count = max(1, $count);

        if (!empty($startingCode) && preg_match('/^([a-zA-Z]*)(\d+)$/', trim($startingCode), $m)) {
            $pref = !empty($m[1]) ? strtoupper($m[1]) : $prefix;
            $startNum = (int)$m[2];
            $padLen = strlen($m[2]);
        } else {
            $pref = $prefix;
            $codes = self::where('item_code', 'like', $pref . '%')
                ->orderByRaw('LENGTH(item_code) DESC, item_code DESC')
                ->pluck('item_code');

            $maxNum = 0;
            foreach ($codes as $code) {
                if (preg_match('/^' . preg_quote($pref, '/') . '(\d+)/i', $code, $matches)) {
                    $num = (int)$matches[1];
                    if ($num > $maxNum) {
                        $maxNum = $num;
                    }
                }
            }
            $startNum = $maxNum + 1;
            $padLen = 5;
        }

        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $curNum = $startNum + $i;
            $result[] = sprintf('%s%0' . $padLen . 'd', $pref, $curNum);
        }

        return $result;
    }
}

