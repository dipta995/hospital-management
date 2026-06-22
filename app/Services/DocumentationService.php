<?php

namespace App\Services;

class DocumentationService
{
    public function locale(string $locale): string
    {
        return in_array($locale, ['en', 'bn'], true) ? $locale : 'en';
    }

    public function modules(string $locale): array
    {
        $locale = $this->locale($locale);
        app()->setLocale($locale === 'bn' ? 'bn' : 'en');

        return trans('documentation.modules', [], $locale);
    }

    public function module(string $locale, string $key): ?array
    {
        $modules = $this->modules($locale);

        return $modules[$key] ?? null;
    }

    public function moduleList(string $locale): array
    {
        $modules = $this->modules($locale);
        $list = [];

        foreach ($modules as $key => $module) {
            $list[] = array_merge($module, ['key' => $key]);
        }

        return $list;
    }

    public function navGroups(string $locale): array
    {
        $locale = $this->locale($locale);

        return trans('documentation.nav_groups', [], $locale);
    }

    public function meta(string $locale): array
    {
        $locale = $this->locale($locale);

        return trans('documentation.meta', [], $locale);
    }
}
