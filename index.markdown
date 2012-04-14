---
layout: default
title: Avro Generator Bundle
---
<img style="float: right" src="assets/images/happy_programmer.jpg" width="400px">
<div class="hero-unit">
    <h1>Generate Symfony2 related code from the command line!</h1>
</div>
<p>Use twig templates to generate commonly repeated code for your mapped entities. It's easy.</p>
<div class="page-header">
    <h3>1. <small>Create your template</small></h3>
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
    <h3>2. <small>Add configuration</small> 
</div>
<pre class="prettify lang-yaml">
avro_generator:
    files:
        controller: 
            filename: 'Controller/{{ entity }}Controller.php'
            template: 'AcmeDemoBundle:Skeleton/Controller/Controller.php' 
</pre>
<div class="page-header">
    <h3>3. <small>Run Generator</small> 
</div>
<pre class="prettyprint lang-bsh">
    $ php app/console avro:generate
    $ AcmeDemoBundle:Test
    $ <enter>
</pre>
<div class="page-header">
    <h3>Result</h3>
</div>
<pre class="prettify lang-php">
// AcmeDemoBundle/Skeleton/Controller/Controller.php

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

