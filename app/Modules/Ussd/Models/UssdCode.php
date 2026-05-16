<?php

declare(strict_types=1);

namespace App\Modules\Ussd\Models;

use App\Modules\Ussd\Enums\UssdActionType;
use App\Modules\Ussd\Enums\UssdCategory;
use App\Shared\Enums\Operator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class UssdCode extends Model
{
    protected $fillable = [
        'operator',
        'category',
        'action_type',
        'slug',
        'label',
        'description',
        'code',
        'params',
        'steps',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'operator' => Operator::class,
            'category' => UssdCategory::class,
            'action_type' => UssdActionType::class,
            'params' => 'array',
            'steps' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<UssdFavorite, $this>
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(UssdFavorite::class);
    }

    /**
     * Build the final USSD code with params filled in.
     *
     * @param array<string, string> $values
     */
    public function buildCode(array $values = []): string
    {
        $built = $this->code;

        foreach ($values as $key => $value) {
            $built = str_replace("{{$key}}", $value, $built);
        }

        return $built;
    }

    /**
     * Get the tel: URI to launch this USSD code.
     *
     * @param array<string, string> $values
     */
    public function toTelUri(array $values = []): string
    {
        $code = $this->buildCode($values);

        // Encode # as %23 for tel: URI
        return 'tel:' . str_replace('#', '%23', $code);
    }

    /**
     * @return array<int, array{key: string, label: string, type: string, placeholder?: string}>
     */
    public function getParamDefinitions(): array
    {
        return $this->params ?? [];
    }

    public function needsParams(): bool
    {
        return !empty($this->params);
    }
}
