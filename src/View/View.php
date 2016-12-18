<?php

namespace Leeflets\View;

use Twig_Environment;
use Twig_Loader_Filesystem;

class View {

    private static $templateExtension = 'twig';

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $onePagerTemplateDir;

    /**
     * View constructor.
     *
     * @param string $onePagerTemplateDir
     * @param string $templateName
     * @param array $data
     */
    public function __construct($onePagerTemplateDir, $templateName, $data = []) {
        $this->data = $data;
        $this->templateName = $templateName;
        $this->onePagerTemplateDir = $onePagerTemplateDir;
    }

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function toHtml() {
        $loader = new Twig_Loader_Filesystem(['src/templates', $this->onePagerTemplateDir]);
        $twig = new Twig_Environment($loader, ['debug' => true]);

        return $twig->render($this->templateName . '.' . self::$templateExtension, $this->data);
    }
}
