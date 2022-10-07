<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Exception;

trait JsCoverageContextTrait
{
    /** @var array<string, array<string, mixed>> */
    private array $jsCoverage = [];

    protected function isJsCoverageEnabled(): bool
    {
        return is_dir(__DIR__ . '/../../coverage/js');
    }

    public function __destruct()
    {
        if (!$this->isJsCoverageEnabled()) {
            return;
        }

        $outputFile = __DIR__ . '/../../coverage/js/' . hash('sha256', microtime(true) . random_bytes(64)) . '.json';
        file_put_contents($outputFile, json_encode($this->jsCoverage, \JSON_THROW_ON_ERROR));
    }

    protected function saveJsCoverage(): void
    {
        if (!$this->isJsCoverageEnabled()) {
            return;
        }

        $seenPaths = array_keys($this->jsCoverage);
        $coverageAll = $this->getSession()->evaluateScript(<<<'EOF'
            return (function (seenPaths) {
                seenPaths = new Set(seenPaths);
                const istanbulCoverage = window.__coverage__;
                if (typeof istanbulCoverage !== 'object') {
                    throw '"window.__coverage__" is not defined';
                }

                const resAll = {};
                Object.entries(istanbulCoverage).forEach(([path, data]) => {
                    const res = {};
                    Object.entries(data).forEach(([k, v]) => {
                        if (['statementMap', 'fnMap', 'branchMap'].includes(k) && seenPaths.has(path)) {
                            return;
                        }
                        if (typeof v === 'object') {
                            const vKeys = Object.keys(v);
                            if (JSON.stringify(vKeys) === JSON.stringify(vKeys.map((v, k) => k.toString()))) {
                                v = [...Object.values(v)];
                            }
                        }
                        res[k] = v;
                    });
                    resAll[path] = res;
                });

                return resAll;
            })(arguments[0]);
            EOF, [$seenPaths]);

        foreach ($coverageAll as $path => $data) {
            if (!isset($this->jsCoverage[$path])) {
                $this->jsCoverage[$path] = $data;
            } else {
                if ($this->jsCoverage[$path]['hash'] !== $data['hash']
                    || $this->jsCoverage[$path]['_coverageSchema'] !== $data['_coverageSchema']
                    || count($this->jsCoverage[$path]['s']) !== count($data['s'])
                    || count($this->jsCoverage[$path]['f']) !== count($data['f'])
                    || count($this->jsCoverage[$path]['b']) !== count($data['b'])
                ) {
                    throw new Exception('Unexpected JS coverage hash change');
                }

                foreach (['s', 'f', 'b'] as $k) {
                    foreach ($data[$k] as $i => $n) {
                        if ($k === 'b') {
                            foreach ($n as $nI => $nN) {
                                $this->jsCoverage[$path][$k][$i][$nI] += $nN;
                            }
                        } else {
                            $this->jsCoverage[$path][$k][$i] += $n;
                        }
                    }
                }
            }
        }
    }
}
