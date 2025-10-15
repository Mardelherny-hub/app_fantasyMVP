<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    // ========================================
    // CONSTANTES DE GRUPOS
    // ========================================
    const GROUP_GENERAL = 'general';
    const GROUP_LEAGUES = 'leagues';
    const GROUP_MARKET = 'market';
    const GROUP_QUIZ = 'quiz';
    const GROUP_SCORING = 'scoring';
    const GROUP_REWARDS = 'rewards';
    const GROUP_EMAIL = 'email';
    const GROUP_SECURITY = 'security';
    const GROUP_SOCIAL = 'social';
    const GROUP_API = 'api';

    const GROUPS = [
        self::GROUP_GENERAL => 'General',
        self::GROUP_LEAGUES => 'Leagues & Competition',
        self::GROUP_MARKET => 'Transfer Market',
        self::GROUP_QUIZ => 'Quiz & Trivia',
        self::GROUP_SCORING => 'Scoring Rules',
        self::GROUP_REWARDS => 'Economy & Rewards',
        self::GROUP_EMAIL => 'Email Notifications',
        self::GROUP_SECURITY => 'Security',
        self::GROUP_SOCIAL => 'Social Media',
        self::GROUP_API => 'API Settings',
    ];

    // ========================================
    // CONSTANTES DE TIPOS
    // ========================================
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_JSON = 'json';
    const TYPE_ARRAY = 'array';
    const TYPE_FLOAT = 'float';
    const TYPE_TEXT = 'text';
    const TYPE_EMAIL = 'email';
    const TYPE_URL = 'url';

    const TYPES = [
        self::TYPE_STRING => 'String',
        self::TYPE_INTEGER => 'Integer',
        self::TYPE_BOOLEAN => 'Boolean',
        self::TYPE_JSON => 'JSON',
        self::TYPE_ARRAY => 'Array',
        self::TYPE_FLOAT => 'Float',
        self::TYPE_TEXT => 'Text',
        self::TYPE_EMAIL => 'Email',
        self::TYPE_URL => 'URL',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
        'options',
        'default_value',
        'validation_rules',
        'is_editable',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'is_editable' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Cache key prefix
     */
    const CACHE_PREFIX = 'setting:';
    const CACHE_TTL = 3600; // 1 hora

    // ========================================
    // BOOT METHOD
    // ========================================
    
    protected static function boot()
    {
        parent::boot();

        // Limpiar cache cuando se guarda o elimina
        static::saved(function ($setting) {
            self::clearCache($setting->key);
        });

        static::deleted(function ($setting) {
            self::clearCache($setting->key);
        });
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by group.
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope active settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope editable settings.
     */
    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    /**
     * Scope ordered by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }

    // ========================================
    // MÉTODOS ESTÁTICOS - GET SETTINGS
    // ========================================

    /**
     * Obtener valor de un setting con cache.
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = self::CACHE_PREFIX . $key;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($key, $default) {
            $setting = self::where('key', $key)->where('is_active', true)->first();

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Obtener múltiples settings por grupo.
     */
    public static function getGroup(string $group): array
    {
        $cacheKey = self::CACHE_PREFIX . 'group:' . $group;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($group) {
            $settings = self::where('group', $group)
                           ->where('is_active', true)
                           ->get();

            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->key] = self::castValue($setting->value, $setting->type);
            }

            return $result;
        });
    }

    /**
     * Obtener todos los settings activos.
     */
    public static function getAll(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'all';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $settings = self::where('is_active', true)->get();

            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->key] = self::castValue($setting->value, $setting->type);
            }

            return $result;
        });
    }

    // ========================================
    // MÉTODOS ESTÁTICOS - SET SETTINGS
    // ========================================

    /**
     * Establecer valor de un setting.
     */
    public static function set(string $key, $value): bool
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return false;
        }

        // Convertir valor según tipo
        $processedValue = self::processValue($value, $setting->type);

        $setting->update(['value' => $processedValue]);

        return true;
    }

    /**
     * Crear o actualizar un setting.
     */
    public static function upsert(string $key, $value, array $attributes = []): Setting
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            array_merge($attributes, ['value' => $value])
        );

        return $setting;
    }

    // ========================================
    // MÉTODOS DE CASTING
    // ========================================

    /**
     * Castear valor según tipo.
     */
    protected static function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        switch ($type) {
            case self::TYPE_BOOLEAN:
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);

            case self::TYPE_INTEGER:
                return (int) $value;

            case self::TYPE_FLOAT:
                return (float) $value;

            case self::TYPE_JSON:
            case self::TYPE_ARRAY:
                return is_string($value) ? json_decode($value, true) : $value;

            case self::TYPE_STRING:
            case self::TYPE_TEXT:
            case self::TYPE_EMAIL:
            case self::TYPE_URL:
            default:
                return (string) $value;
        }
    }

    /**
     * Procesar valor antes de guardarlo.
     */
    protected static function processValue($value, string $type): string
    {
        switch ($type) {
            case self::TYPE_BOOLEAN:
                return $value ? '1' : '0';

            case self::TYPE_JSON:
            case self::TYPE_ARRAY:
                return is_string($value) ? $value : json_encode($value);

            default:
                return (string) $value;
        }
    }

    // ========================================
    // MÉTODOS DE CACHE
    // ========================================

    /**
     * Limpiar cache de un setting específico.
     */
    public static function clearCache(string $key): void
    {
        Cache::forget(self::CACHE_PREFIX . $key);
        Cache::forget(self::CACHE_PREFIX . 'all');
        
        // Limpiar cache del grupo al que pertenece
        $setting = self::where('key', $key)->first();
        if ($setting) {
            Cache::forget(self::CACHE_PREFIX . 'group:' . $setting->group);
        }
    }

    /**
     * Limpiar todo el cache de settings.
     */
    public static function clearAllCache(): void
    {
        Cache::forget(self::CACHE_PREFIX . 'all');
        
        foreach (self::GROUPS as $groupKey => $groupLabel) {
            Cache::forget(self::CACHE_PREFIX . 'group:' . $groupKey);
        }
    }

    // ========================================
    // MÉTODOS HELPER
    // ========================================

    /**
     * Verificar si un setting existe.
     */
    public static function has(string $key): bool
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Eliminar un setting.
     */
    public static function forget(string $key): bool
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return false;
        }

        return $setting->delete();
    }

    /**
     * Obtener valor tipado directamente.
     */
    public function getTypedValue()
    {
        return self::castValue($this->value, $this->type);
    }

    /**
     * Verificar si el setting es válido.
     */
    public function isValid(): bool
    {
        if (!$this->validation_rules) {
            return true;
        }

        $validator = validator(
            ['value' => $this->getTypedValue()],
            ['value' => $this->validation_rules]
        );

        return !$validator->fails();
    }
}