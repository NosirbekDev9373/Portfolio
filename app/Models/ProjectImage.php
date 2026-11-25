<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'path', 'alt_uz', 'alt_en', 'sort_order'])]
class ProjectImage extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function localizedAlt(): ?string
    {
        $locale = app()->getLocale();

        return $this->{'alt_'.$locale} ?? $this->alt_en ?? $this->alt_uz;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
