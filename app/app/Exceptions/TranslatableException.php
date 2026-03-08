<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\App;
use Throwable;

abstract class TranslatableException extends Exception
{
    protected string $translationKey;
    protected array $translationParams = [];
    protected int $httpStatusCode = 400;
    protected ?string $locale = null;
    
    public function __construct(
        string $translationKey, 
        array $translationParams = [], 
        ?string $locale = null,
        int $code = 0, 
        ?Throwable $previous = null
    ) {
        $this->translationKey = $translationKey;
        $this->translationParams = $translationParams;
        $this->locale = $locale ?? App::getLocale();
        
        $message = $this->getRawTranslation();
        
        parent::__construct($message, $code, $previous);
    }
    
    public function getRawTranslation(): string
    {
        $translation = __($this->translationKey, $this->translationParams, $this->locale);
        
        if ($translation === $this->translationKey && $this->locale !== 'en') {
            $translation = __($this->translationKey, $this->translationParams, 'en');
        }
        
        return $translation === $this->translationKey ? $this->translationKey : $translation;
    }
    

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }
    
    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
    

    public function getTranslationParams(): array
    {
        return $this->translationParams;
    }
    

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
    
    public function toArray(): array
    {
        return [
            'error' => [
                'code' => $this->getCode(),
                'message' => $this->getMessage(),
                'key' => $this->translationKey,
                'locale' => $this->locale,
            ]
        ];
    }
}