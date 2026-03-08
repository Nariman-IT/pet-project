<?php

namespace App\Exceptions;

use Exception;

class ReportNotFoundException extends TranslatableException
{
    protected string $translationKey = 'message.report.not_found';
    protected int $httpStatusCode = 404;
    
    public function __construct(string $reportId, ?string $locale = null)
    {
        parent::__construct(
            $this->translationKey,
            ['id' => $reportId],
            $locale,
            404
        );
    }
}