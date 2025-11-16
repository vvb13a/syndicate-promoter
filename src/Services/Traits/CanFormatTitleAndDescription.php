<?php

namespace Syndicate\Promoter\Services\Traits;

use Illuminate\Support\Str;

trait CanFormatTitleAndDescription
{
    protected function formatDescription(?string $description): string
    {
        $description = trim(strip_tags($description ?? ''));
        if (empty($description)) {
            return '';
        }

        $maxLength = $this->globalPackageConfig['max_description_length'] ?? null;

        return $maxLength ? Str::limit($description, $maxLength) : $description;
    }

    protected function formatTitle(?string $title): string
    {
        $title = trim($title ?? '');
        $siteName = $this->globalPackageConfig['site_name'] ?? config('app.name');
        $separator = ' '.trim($this->globalPackageConfig['title_separator'] ?? '|').' ';

        if (empty($title)) {
            return $siteName;
        }

        if ($this->globalPackageConfig['title_append_site_name'] ?? true) {
            $title = $title.$separator.$siteName;
        } elseif (($this->globalPackageConfig['title_prepend_site_name'] ?? false)) {
            $title = $siteName.$separator.$title;
        }

        $maxLength = $this->globalPackageConfig['max_title_length'] ?? null;

        return $maxLength ? Str::limit($title, $maxLength, '') : $title;
    }
}
