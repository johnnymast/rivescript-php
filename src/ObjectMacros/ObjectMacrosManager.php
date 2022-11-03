<?php

namespace Axiom\Rivescript\ObjectMacros;

use Axiom\Collections\Collection;
use Axiom\Rivescript\ObjectMacros\Macros\PHPMacro;

class ObjectMacrosManager
{

    /**
     * This object will contain all registered
     * object macro handlers.
     *
     * @var \Axiom\Collections\Collection<\Axiom\Rivescript\Macros\ObjectMacros\ObjectMacroInterface>
     */
    private Collection $macros;

    public function __construct()
    {
        $this->macros = Collection::make([]);

        $this->registerMacro(PHPMacro::class);
    }

    /**
     * @param string $class
     *
     * @return void
     */
    public function registerMacro(string $class): void
    {
        if (class_exists($class)) {
            /**
             * @var ObjectMacroInterface $macro
             */
            $macro = new $class();
            $language = strtolower($macro->getLanguage());

            $this->macros->put($language, $macro);
        }
    }

    /**
     * @param string $language
     * @param string $code
     *
     * @return string
     */
    public function executeMacro(string $language, string $code)
    {
        if ($this->macros->has($language)) {
            /**
             * @var ObjectMacroInterface $macro
             */
            $macro = $this->macros->get($language);

            return $macro->execute($code);
        }
    }
}