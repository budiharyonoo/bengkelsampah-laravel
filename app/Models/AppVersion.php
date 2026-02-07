<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion query()
 * @property int $id
 * @property string $platform
 * @property string $version
 * @property int $version_code
 * @property bool $is_required
 * @property string|null $update_message
 * @property string|null $store_url
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereStoreUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereUpdateMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppVersion whereVersionCode($value)
 * @mixin \Eloquent
 */
class AppVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'version',
        'version_code',
        'is_required',
        'update_message',
        'store_url',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'version_code' => 'integer',
    ];

    /**
     * Get the latest version for a platform
     */
    public static function getLatestVersion($platform)
    {
        return self::where('platform', $platform)
            ->where('is_active', true)
            ->orderBy('version_code', 'desc')
            ->first();
    }

    /**
     * Check if update is required
     */
    public static function isUpdateRequired($platform, $currentVersionCode)
    {
        $latestVersion = self::getLatestVersion($platform);

        if (! $latestVersion) {
            return false;
        }

        return $latestVersion->is_required && $currentVersionCode < $latestVersion->version_code;
    }

    /**
     * Check if update is available (not required but newer version exists)
     */
    public static function isUpdateAvailable($platform, $currentVersionCode)
    {
        $latestVersion = self::getLatestVersion($platform);

        if (! $latestVersion) {
            return false;
        }

        return $currentVersionCode < $latestVersion->version_code;
    }

    /**
     * Get update info for platform
     */
    public static function getUpdateInfo($platform, $currentVersionCode)
    {
        $latestVersion = self::getLatestVersion($platform);

        if (! $latestVersion) {
            return null;
        }

        return [
            'current_version_code' => $currentVersionCode,
            'latest_version' => $latestVersion->version,
            'latest_version_code' => $latestVersion->version_code,
            'is_update_required' => $latestVersion->is_required && $currentVersionCode < $latestVersion->version_code,
            'is_update_available' => $currentVersionCode < $latestVersion->version_code,
            'update_message' => $latestVersion->update_message,
            'store_url' => $latestVersion->store_url,
        ];
    }
}
