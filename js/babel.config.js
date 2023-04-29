module.exports = (api) => {
    const isCoverage = !!process.env.ISTANBUL_COVERAGE;
    api.cache.using(() => isCoverage);

    return {
        presets: [
            [
                '@babel/preset-env',
                {
                    targets: 'defaults',
                    corejs: { version: '3.9999', proposals: true },
                    useBuiltIns: 'usage',
                },
            ],
        ],
        plugins: [
            ...(isCoverage ? [[
                'babel-plugin-istanbul',
                {
                    extension: ['.js', '.vue'],
                },
            ]] : []),
            '@babel/plugin-transform-runtime',
        ],
    };
};
