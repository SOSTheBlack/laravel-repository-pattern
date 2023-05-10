<?php

namespace SOSTheBlack\Repository\Traits;

/**
 * Trait ComparesVersionsTrait
 * @package SOSTheBlack\Repository\Traits
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
trait ComparesVersionsTrait
{
    /**
     * Version compare function that can compare both Laravel and Lumen versions.
     *
     * @param string $frameworkVersion
     * @param string $compareVersion
     * @param string|null $operator
     *
     * @return  int|bool
     */
    public function versionCompare(string $frameworkVersion, string $compareVersion, string $operator = null): int|bool
    {
        // Lumen (5.5.2) (Laravel Components 5.5.*)
        $lumenPattern = '/Lumen \((\d\.\d\.[\d|\*])\)( \(Laravel Components (\d\.\d\.[\d|\*])\))?/';

        if (preg_match($lumenPattern, $frameworkVersion, $matches)) {
            $frameworkVersion = $matches[3] ?? $matches[1]; // Prefer Laravel Components version.
        }

        return version_compare($frameworkVersion, $compareVersion, $operator);
    }
}
