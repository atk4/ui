<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

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
        $coverages = $this->getSession()->evaluateScript(<<<'EOF'
            return (function (seenPaths) {
                seenPaths = new Set(seenPaths);

                const windowCoverage = window.__coverage__;
                if (typeof windowCoverage !== 'object') {
                    throw new Error('"window.__coverage__" is not defined');
                }

                const transformCoverageFx = function (istanbulCoverage) {
                    const res = {};
                    Object.entries(istanbulCoverage).forEach(([path, data]) => {
                        const resSingle = {};
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
                            resSingle[k] = v;
                        });
                        res[path] = resSingle;
                    });

                    return res;
                };

                if (window.__coverage_beforeunload__ !== true) {
                    window.addEventListener('beforeunload', () => {
                        const navigateCoverages = JSON.parse(window.sessionStorage.getItem('__coverage_navigate__') ?? '[]');
                        navigateCoverages.push(transformCoverageFx(window.__coverage__));
                        window.sessionStorage.setItem('__coverage_navigate__', JSON.stringify(navigateCoverages));
                    });
                    window.__coverage_beforeunload__ = true;
                }
                const navigateCoverages = JSON.parse(window.sessionStorage.getItem('__coverage_navigate__') ?? '[]');
                window.sessionStorage.removeItem('__coverage_navigate__');

                const res = [];
                for (const coverage of [windowCoverage, ...navigateCoverages]) {
                    res.push(transformCoverageFx(coverage));
                }

                return res;
            })(arguments[0]);
            EOF, [$seenPaths]);

        foreach ($coverages as $coverage) {
            foreach ($coverage as $path => $data) {
                if (!isset($this->jsCoverage[$path])) {
                    $this->jsCoverage[$path] = $data;
                } else {
                    if ($this->jsCoverage[$path]['hash'] !== $data['hash']
                        || $this->jsCoverage[$path]['_coverageSchema'] !== $data['_coverageSchema']
                        || count($this->jsCoverage[$path]['s']) !== count($data['s'])
                        || count($this->jsCoverage[$path]['f']) !== count($data['f'])
                        || count($this->jsCoverage[$path]['b']) !== count($data['b'])
                    ) {
                        throw new \Exception('Unexpected JS coverage hash change');
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
}
