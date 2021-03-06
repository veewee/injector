<?php

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'align_multiline_comment' => true,
        'array_syntax' => ['syntax' => 'short'],
        'class_keyword_remove' => false,
        'combine_consecutive_unsets' => true,
        'declare_strict_types' => true,
        'doctrine_annotation_array_assignment' => true,
        'doctrine_annotation_braces' => true,
        'doctrine_annotation_indentation' => true,
        'doctrine_annotation_spaces' => true,
        'general_phpdoc_annotation_remove' => false,
        'fully_qualified_strict_types' => true,
        'header_comment' => false,
        'heredoc_to_nowdoc' => false,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => ['syntax' => 'short'],
        'mb_str_functions' => true,
        'native_function_invocation' => false,
        'no_blank_lines_before_namespace' => false,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_null_property_initialization' => true,
        'no_php4_constructor' => true,
        'no_short_echo_tag' => false,
        'no_superfluous_elseif' => true,
        'no_unneeded_curly_braces' => true,
        'no_unneeded_final_method' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'not_operator_with_space' => false,
        'not_operator_with_successor_space' => false,
        'ordered_class_elements' => false,
        'ordered_imports' => true,
        'php_unit_strict' => false,
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'pow_to_exponentiation' => true,
        'psr0' => true,
        'random_api_migration' => false,
        'semicolon_after_instruction' => true,
        'simplified_null_return' => false,
        'single_line_comment_style' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'ternary_to_null_coalescing' => true,
        'void_return' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()->in([
            'src',
            'test',
        ])
    );
