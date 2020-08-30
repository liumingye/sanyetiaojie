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

/* fk_checkbox.twig */
class __TwigTemplate_17750b949b7b15d93421aec321af8cb43b248f45d0c1587b76cd7450a2874ce2 extends \Twig\Template
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
        echo "<input type=\"hidden\" name=\"fk_checks\" value=\"0\">
<input type=\"checkbox\" name=\"fk_checks\" id=\"fk_checks\" value=\"1\"";
        // line 3
        echo ((($context["checked"] ?? null)) ? (" checked") : (""));
        echo ">
<label for=\"fk_checks\">";
        // line 4
        echo _gettext("Enable foreign key checks");
        echo "</label>
";
    }

    public function getTemplateName()
    {
        return "fk_checkbox.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  44 => 4,  40 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "fk_checkbox.twig", "D:\\Soft\\phpStudy\\WWW\\jjjshop\\app\\public\\phpMyAdmin4.8.5\\templates\\fk_checkbox.twig");
    }
}
