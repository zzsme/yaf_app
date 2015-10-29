<?php

/* default\index.html */
class __TwigTemplate_dfc0909fd72d83fbf4767cbe94846704de0106da0dbfac6a9edc255640b21d51 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<html>
<head>
    <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
    <title>Yaf Demo</title>
</head>
<body>
    <h3>Yaf Demo</h3>
    <a href=\"";
        // line 8
        echo twig_escape_filter($this->env, Tools_help::url("default/test", array("id" => "1", "name" => "codejm")), "html", null, true);
        echo "\" >URL</a>
</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "default\\index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  28 => 8,  19 => 1,);
    }
}
