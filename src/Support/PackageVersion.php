<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Support;

use Composer\InstalledVersions;

final class PackageVersion
{
    private const PACKAGE = 'hamzi/corewatch';

    public static function current(): string
    {
        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled(self::PACKAGE)) {
            $version = InstalledVersions::getPrettyVersion(self::PACKAGE);

            if ($version !== null && $version !== '') {
                return $version;
            }
        }

        $composerPath = dirname(__DIR__, 2).'/composer.json';

        if (is_readable($composerPath)) {
            $contents = file_get_contents($composerPath);
            if ($contents !== false) {
                /** @var array<string, mixed>|null $data */
                $data = json_decode($contents, true);

                if (is_array($data) && ! empty($data['version']) && is_string($data['version'])) {
                    return ltrim($data['version'], 'v');
                }
            }
        }

        return 'dev';
    }
}
