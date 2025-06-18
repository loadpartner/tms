<?php

namespace App\Services;

class TemplateSecurityService
{
    /**
     * Dangerous Blade directives that should be blocked
     */
    private const DANGEROUS_DIRECTIVES = [
        '@php' => 'PHP code execution',
        '@endphp' => 'PHP code execution',
        '@eval' => 'Code evaluation',
        '@include' => 'File inclusion',
        '@includeIf' => 'Conditional file inclusion',
        '@includeWhen' => 'Conditional file inclusion',
        '@includeUnless' => 'Conditional file inclusion',
        '@each' => 'Loop inclusion',
        '@extends' => 'Template extension',
        '@section' => 'Section definition',
        '@endsection' => 'Section definition',
        '@yield' => 'Content yielding',
        '@parent' => 'Parent content',
        '@inject' => 'Service injection',
        '@can' => 'Authorization check',
        '@cannot' => 'Authorization check',
        '@canany' => 'Authorization check',
        '@auth' => 'Authentication check',
        '@guest' => 'Guest check',
        '@method' => 'HTTP method spoofing',
        '@csrf' => 'CSRF token',
        '@dd' => 'Debug dumping',
        '@dump' => 'Variable dumping'
    ];

    /**
     * Allowed Blade directives for safe template rendering
     */
    private const ALLOWED_DIRECTIVES = [
        '@if', '@elseif', '@else', '@endif',
        '@unless', '@endunless',
        '@foreach', '@endforeach',
        '@for', '@endfor',
        '@while', '@endwhile',
        '@empty', '@endempty'
    ];

    /**
     * Validate template for security issues
     */
    public function validateTemplate(string $template): array
    {
        $issues = [];
        $warnings = [];

        // Check for PHP tags
        if (preg_match('/<\?(?:php|=|\s)/', $template)) {
            $issues[] = 'PHP code tags are not allowed for security reasons';
        }

        // Check for dangerous Blade directives
        foreach (self::DANGEROUS_DIRECTIVES as $directive => $description) {
            if (strpos($template, $directive) !== false) {
                $issues[] = "Directive {$directive} is not allowed ({$description})";
            }
        }

        // Check for function calls in variables
        if (preg_match('/\{\{\s*\$[\w\[\]\.\'\"]*\([^}]*\)\s*\}\}/', $template)) {
            $warnings[] = 'Function calls in template variables are not recommended';
        }

        // Check for complex expressions
        if (preg_match('/\{\{\s*(?![\$][\w\[\]\.\'\"]*\s*(?:\?\?[^}]*)?)\s*[^}]*\}\}/', $template)) {
            $warnings[] = 'Complex expressions may be removed for security';
        }

        // Check for unescaped output with function calls
        if (preg_match('/\{!!\s*\$[\w\[\]\.\'\"]*\([^}]*\)\s*!!\}/', $template)) {
            $warnings[] = 'Function calls in unescaped output are not recommended';
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'warnings' => $warnings
        ];
    }

    /**
     * Sanitize template by removing dangerous code
     */
    public function sanitizeTemplate(string $template): string
    {
        // Remove all PHP tags
        $template = preg_replace('/<\?(?:php|=|\s).*?\?>/s', '', $template);
        
        // Remove dangerous Blade directives
        foreach (array_keys(self::DANGEROUS_DIRECTIVES) as $directive) {
            $template = preg_replace('/' . preg_quote($directive, '/') . '\b.*$/m', '', $template);
        }
        
        // Remove any remaining @directive calls that aren't in our whitelist
        $template = preg_replace_callback('/@(\w+)/', function($matches) {
            $directive = '@' . $matches[1];
            if (!in_array($directive, self::ALLOWED_DIRECTIVES)) {
                return ''; // Remove disallowed directive
            }
            return $matches[0]; // Keep allowed directive
        }, $template);
        
        // Validate variable syntax - only allow {{ $variable }} and {!! $variable !!}
        // Remove any complex expressions or function calls
        $template = preg_replace('/\{\{\s*(?![\$][\w\[\]\.\'\"]*\s*(?:\?\?[^}]*)?)\s*[^}]*\}\}/', '', $template);
        $template = preg_replace('/\{!!\s*(?![\$][\w\[\]\.\'\"]*\s*(?:\?\?[^}]*)?)\s*[^}]*!!\}/', '', $template);
        
        // Remove any function calls within variable expressions
        $template = preg_replace('/\{\{\s*\$[\w\[\]\.\'\"]*\([^}]*\)\s*\}\}/', '', $template);
        $template = preg_replace('/\{!!\s*\$[\w\[\]\.\'\"]*\([^}]*\)\s*!!\}/', '', $template);
        
        return $template;
    }

    /**
     * Get list of dangerous directives for reference
     */
    public function getDangerousDirectives(): array
    {
        return self::DANGEROUS_DIRECTIVES;
    }

    /**
     * Get list of allowed directives for reference
     */
    public function getAllowedDirectives(): array
    {
        return self::ALLOWED_DIRECTIVES;
    }
} 