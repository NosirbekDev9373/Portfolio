<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'title_uz',
    'title_en',
    'summary_uz',
    'summary_en',
    'description_uz',
    'description_en',
    'problem_uz',
    'problem_en',
    'solution_uz',
    'solution_en',
    'features',
    'contributions',
    'status',
    'live_url',
    'github_url',
    'is_featured',
    'is_published',
    'sort_order',
])]
class Project extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
            'features' => 'array',
            'contributions' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function localized(string $field): ?string
    {
        $locale = app()->getLocale();

        return $this->{$field.'_'.$locale} ?? $this->{$field.'_en'} ?? $this->{$field.'_uz'};
    }

    public function localizedProblem(): ?string
    {
        return $this->localizedCaseStudyText('problem');
    }

    public function localizedSolution(): ?string
    {
        return $this->localizedCaseStudyText('solution');
    }

    /**
     * @return array<int, array{title: ?string, description: ?string}>
     */
    public function localizedFeatures(): array
    {
        return $this->localizedItems('features');
    }

    /**
     * @return array<int, array{title: ?string, description: ?string}>
     */
    public function localizedContributions(): array
    {
        return $this->localizedItems('contributions');
    }

    private function localizedCaseStudyText(string $field): ?string
    {
        foreach (array_unique([app()->getLocale(), 'en', 'uz']) as $locale) {
            $value = $this->{$field.'_'.$locale} ?? null;

            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @return array<int, array{title: ?string, description: ?string}>
     */
    private function localizedItems(string $field): array
    {
        $items = $this->{$field};

        if (! is_array($items)) {
            return [];
        }

        $localized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = $this->localizedItemValue($item, 'title');
            $description = $this->localizedItemValue($item, 'description');

            if ($title === null && $description === null) {
                continue;
            }

            $localized[] = [
                'title' => $title,
                'description' => $description,
            ];
        }

        return $localized;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function localizedItemValue(array $item, string $field): ?string
    {
        foreach (array_unique([app()->getLocale(), 'en', 'uz']) as $locale) {
            $value = $item[$field.'_'.$locale] ?? null;

            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return null;
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class, 'project_technology');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
