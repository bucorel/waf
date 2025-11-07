<?php
namespace Bucorel\Waf\Dal;

class StructException extends \Exception {
    /**
     * @var string The user-friendly error label/message.
     */
    protected $errorLabel;

    /**
     * @var string|null The name of the field causing the exception, if applicable.
     */
    protected $fieldName;

    /**
     * StructException constructor.
     *
     * @param string $errorLabel The user-friendly error label.
     * @param string|null $fieldName The name of the field causing the exception.
     * @param int $code The Exception code.
     * @param \Throwable|null $previous The previous exception used for the exception chaining.
     */
    public function __construct(
        string $errorLabel,
        ?string $fieldName = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        // Set the custom properties
        $this->errorLabel = $errorLabel;
        $this->fieldName = $fieldName;

        // Call the parent constructor with a full, descriptive message (optional)
        // You can use the parent's message for logging/debugging, and your custom
        // properties for application logic.
        $message = $errorLabel . ($fieldName ? " (Field: " . $fieldName . ")" : "");

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the user-friendly error label.
     *
     * @return string
     */
    public function getErrorLabel(): string {
        return $this->errorLabel;
    }

    /**
     * Get the name of the field causing the error.
     *
     * @return string|null
     */
    public function getErrorFieldName(): ?string {
        return $this->fieldName;
    }
}
?>