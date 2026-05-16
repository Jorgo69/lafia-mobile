<?php

declare(strict_types=1);

namespace App\Modules\Sync\Models;

use Illuminate\Database\Eloquent\Model;

final class DynamicSchema extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'scope';
    protected $keyType = 'string';

    protected $fillable = [
        'scope',
        'fields',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
        ];
    }

    /**
     * @return array<int, array{key: string, label: string, type: string, required: bool, options?: array<string>}>
     */
    public function getFieldDefinitions(): array
    {
        return $this->fields ?? [];
    }
}
