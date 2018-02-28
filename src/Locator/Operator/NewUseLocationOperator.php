<?php

declare(strict_types=1);

namespace Injector\Locator\Operator;

use Injector\Exception\LocationException;
use Injector\Locator\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

final class NewUseLocationOperator implements TokenOperatorInterface
{
    private const NEW_USE_REGEX = '/NEWUSE\(([^\)].*)\)/';

    public function operates(string $location): bool
    {
        return (bool) preg_match(self::NEW_USE_REGEX, $location);
    }

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int
    {
        $useClass = $this->parseNewUseClass($location);
        $existingUses = (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens);
        $injectAtIndex = $previousIndex;

        foreach ($existingUses as $existingUse) {
            if ($existingUse->getFullName() === $useClass) {
                throw new LocationException('Use statement for '.$useClass.' already exists in file.');
            }

            // Check imports alphabetically
            if ($existingUse->getFullName() < $useClass) {
                $injectAtIndex = $existingUse->getEndIndex() + 1; // +1 to move behind semicolon
            }
        }

        // It did not find a good location ...
        if ($injectAtIndex === $previousIndex) {
            return null;
        }

        return $injectAtIndex;
    }

    private function parseNewUseClass($location): string
    {
        $matches = [];
        preg_match(self::NEW_USE_REGEX, $location, $matches);

        return $matches[1] ?? '';
    }
}
