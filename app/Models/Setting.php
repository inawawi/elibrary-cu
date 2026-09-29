<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'setting';
    protected $primaryKey = 'setting_id';
    public $timestamps = false;

    protected $fillable = [
        'setting_name',
        'setting_value',
    ];

    /**
     * Retrieve a setting value by key with optional default.
     * Automatically decodes JSON or PHP serialized data if applicable.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('setting_name', $key)->first();
        if (!$setting || $setting->setting_value === null) {
            return $default;
        }

        $val = $setting->setting_value;

        // Try JSON decode first
        $decodedJson = json_decode($val, true);
        if (json_last_error() === JSON_ERROR_NONE && (is_array($decodedJson) || is_object($decodedJson))) {
            return $decodedJson;
        }

        // Try PHP unserialize (for legacy SLiMS settings)
        $unserialized = @unserialize($val);
        if ($unserialized !== false || $val === 'b:0;') {
            return $unserialized;
        }

        return $val;
    }

    /**
     * Set a setting value by key.
     * Arrays/objects are encoded as JSON.
     */
    public static function set(string $key, mixed $value): self
    {
        if (is_array($value) || is_object($value)) {
            $storeVal = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $storeVal = (string) $value;
        }

        return self::updateOrCreate(
            ['setting_name' => $key],
            ['setting_value' => $storeVal]
        );
    }
}
