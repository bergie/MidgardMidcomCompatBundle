<?php
use Symfony\Component\Translation\TranslatorInterface;

class midcom_helper_l10n
{
    private $translator;
    private $component = '';

    public function __construct(TranslatorInterface $translator, $component)
    {
        $this->translator = $translator;
        $this->component = $component;
    }

    public function get($string, $language = null)
    {
        if ($language) {
            $orig = $this->translator->getLocale();
            $this->translator->setLocale($language);
            $translation = $this->translator->trans($string, array(), $this->component);
            $this->translator->setLocale($orig);
            return $translation;
        }
        return $this->translator->trans($string, array(), $this->component);
    }

    public function string_available($string)
    {
        if ($this->get($string) != $string) {
            return true;
        }
        return false;
    }

    public function show($string, $language = null)
    {
        echo $this->get($string, $language);
    }
}
