---
layout: default
title: Avro Generator Bundle
---
<div class="hero-unit">
    <h1>Generate Symfony2 code from the command line!</h1>
    <p> 
        Because Symfony2 is awesome.
        <a href="http://github.com/jdewit/generatorBundle" class="btn btn-primary btn-large pull-right">Download</a>
    </p>
</div>
<div class="page-header">
    <h3>Step 1. <small>Create a template</small></h3>
</div>
<pre class="prettify lang-php">
// AcmeDemoBundle/Skeleton/Controller/Controller.php

namespace {{ bundleNamespace }}\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * {{ entityTitle }} controller.
 *
 * @Route("/{{ entityCC }}")
 */
class {{ entity }}Controller extends ContainerAware
{
    /**
     * Create a new {{ entityTitle }}.
     *
     * @Route("/new", name="{{ bundleAlias }}_{{ entityCC }}_new")
     * @Template()     
     */
    public function newAction()
    {
        $form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');
        $formHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form.handler');

        $process = $formHandler->process();
        if ($process) {
            ${{ entityCC }} = $form->getData();
            $this->container->get('session')->getFlashBag()->set('success', '{{ entity | title }} created.');

            return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_edit', array('id' => ${{ entityCC }}->getId())), 301);
        }

        return array(
            'form' => $form->createView(),
        );

    }
}
</pre>
<div class="page-header">
    <h3>Step 2. <small>Add Template to Configuration</small></h3> 
</div>
<pre class="prettify lang-yaml">
avro_generator:
    files:
        controller: 
            filename: 'Controller/{{ entity }}Controller.php'
            template: 'AcmeDemoBundle:Skeleton/Controller/Controller.php' 
</pre>
<div class="page-header">
    <h3>Step 3. <small>Run Generator From Console</small></h3> 
</div>
<pre class="prettyprint lang-bsh">
    $ php app/console avro:generate
    $ AcmeDemoBundle:Test
    $ (enter)
</pre>
<div class="page-header">
    <h3>Result</h3>
</div>
<pre class="prettify lang-php">
namespace Acme\DemoBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test controller.
 *
 * @Route("/test")
 */
class TestController extends ContainerAware
{
    /**
     * Create a new Test.
     *
     * @Route("/new", name="acme_demo_test_new")
     * @Template()     
     */
    public function newAction()
    {
        $form = $this->container->get('acme_demo.test.form');
        $formHandler = $this->container->get('acme_demo.test.form.handler');

        $process = $formHandler->process();
        if ($process) {
            $test = $form->getData();
            $this->container->get('session')->getFlashBag()->set('success', 'Test created.');

            return new RedirectResponse($this->container->get('router')->generate('acme_demo', array('id' => $test->getId())), 301);
        }

        return array(
            'form' => $form->createView(),
        );
    }
}
</pre>
<div style="text-align: center;">
    <img src="assets/images/happy_programmer.jpg" width="400px">
    <p>Party on!</p>
</div>
<br />
<a class="btn pull-right" href="installation.html">Installation<i class="icon-arrow-right"></i></a>
