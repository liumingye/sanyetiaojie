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

/* display/results/data_for_resetting_column_order.twig */
class __TwigTemplate_1737ed18b4db5092b1695f6ed6f1206b25dca7eb4f090a499c579005f741bf1a extends \Twig\Template
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
        if (($context["column_order"] ?? null)) {
            // line 2
            echo "  <input class=\"col_order\" type=\"hidden\" value=\"";
            echo twig_escape_filter($this->env, twig_join_filter(($context["column_order"] ?? null), ","), "html", null, true);
            echo "\">
";
        }
        // line 4
        if (($context["column_visibility"] ?? null)) {
            // line 5
            echo "  <input class=\"col_visib\" type=\"hidden\" value=\"";
            echo twig_escape_filter($this->env, twig_join_filter(($context["column_visibility"] ?? null), ","), "html", null, true);
            echo "\">
";
        }
        // line 7
        if ( !($context["is_view"] ?? null)) {
            // line 8
            echo "  <input class=\"table_create_time\" type=\"hidden\" value=\"";
            echo twig_escape_filter($this->env, ($context["table_create_time"] ?? null), "html", null, true);
            echo "\">
";
        }
    }

    public function getTemplateName()
    {
        return "display/results/data_for_resetting_column_order.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  55 => 8,  53 => 7,  47 => 5,  45 => 4,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/results/data_for_resetting_column_order.twig", "D:\\Soft\\phpStudy\\WWW\\jjjshop\\app\\public\\phpMyAdmin4.8.5\\templates\\display\\results\\data_for_resetting_column_order.twig");
    }
}
