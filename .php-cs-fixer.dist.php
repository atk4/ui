<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__])
    ->exclude([
        'cache',
        'build',
        'vendor',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@PHP74Migration:risky' => true,
        '@PHP74Migration' => true,

        // required by PSR-12
        'concat_space' => [
            'spacing' => 'one',
        ],

        // disable some too strict rules
        'phpdoc_types' => [
            // keep enabled, but without "alias" group to not fix
            // "Callback" to "callback" in phpdoc
            'groups' => ['simple', 'meta']
        ],
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ],
        'single_line_throw' => false,
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
        ],
        'native_constant_invocation' => true,
        'native_function_invocation' => false,
        'void_return' => false,
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'exit'],
        ],
        'combine_consecutive_issets' => false,
        'combine_consecutive_unsets' => false,
        'multiline_whitespace_before_semicolons' => false,
        'no_superfluous_elseif' => false,
        'ordered_class_elements' => false,
        'php_unit_internal_class' => false,
        'php_unit_test_case_static_method_calls' => [
            'call_type' => 'this',
        ],
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_add_missing_param_annotation' => false,
        'return_assignment' => false,
        'comment_to_phpdoc' => false,
        'general_phpdoc_annotation_remove' => [
            'annotations' => ['author', 'copyright', 'throws'],
        ],
        'nullable_type_declaration_for_default_null_value' => [
            'use_nullable_type_declaration' => false,
        ],

        // fn => without curly brackets is less readable,
        // also prevent bounding of unwanted variables for GC
        'use_arrow_functions' => false,
    ])
    ->setFinder($finder)
    ->setCacheFile(sys_get_temp_dir() . '/php-cs-fixer.' . md5(__DIR__) . '.cache');
