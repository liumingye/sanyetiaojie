<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* display/results/page_selector.twig */
class __TwigTemplate_51ae3a5d8d3e81603d67c11887b347dcf9bfbc811b147b8a05ed498232029514 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<td>
  <form action=\"sql.php\" method=\"post\">
    ";
        // line 3
        echo PhpMyAdmin\Url::getHiddenInputs(($context["url_params"] ?? null));
        echo "
    ";
        // line 4
        echo ($context["page_selector"] ?? null);
        echo "
  </form>
</td>
";
    }

    public function getTemplateName()
    {
        return "display/results/page_selector.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 4,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/results/page_selector.twig", "D:\\Soft\\phpStudy\\WWW\\jjjshop\\app\\public\\phpMyAdmin4.8.5\\templates\\display\\results\\page_selector.twig");
    }
}
