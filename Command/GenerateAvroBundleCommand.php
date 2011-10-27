<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Avro\GeneratorBundle\Generator\AvroBundleGenerator;
use Avro\GeneratorBundle\Command\Helper\DialogHelper;


/**
 * Generates bundles.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class GenerateAvroBundleCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('vendor', '', InputOption::VALUE_REQUIRED, 'The vendor name of the bundle to create'),
                new InputOption('basename', '', InputOption::VALUE_REQUIRED, 'The bundle name'),
                new InputOption('db-driver', '', InputOption::VALUE_REQUIRED, 'The bundles database driver (orm, couchdb, mongodb)', 'orm'),
            ))
            ->setDescription('Generates a bundle')
            ->setHelp(<<<EOT
The <info>generate:avro:bundle</info> command helps you generates new bundles.

Note that the bundle name must end with "Bundle".
EOT
            )
            ->setName('generate:avro:bundle')
            ->setAliases(array('generate:avro:bundle'))
        ;
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When namespace doesn't end with Bundle
     * @throws \RuntimeException         When bundle can't be executed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $container = $this->getContainer();
        
        if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
            $output->writeln('<error>Command aborted</error>');

            return 1;
        }

        foreach (array('vendor', 'basename', 'appConfig') as $option) {
            if (null === $input->getOption($option)) {
                throw new \RuntimeException(sprintf('The "%s" option must be provided.', $option));
            }
        }

        $vendor = Validators::validateVendor($input->getOption('vendor'));
        $basename = $input->getOption('basename');
        $bundleName = Validators::validateBundleName($vendor.$basename);
        $dir = dirname($this->getContainer()->getParameter('kernel.root_dir')).'/vendor/bundles/'.$vendor.'/'.$basename;
        $bundleNamespace = Validators::validateBundleNamespace($vendor.'\\'.$basename);
        $dbDriver = Validators::validateDbDriver($input->getOption('db-driver'));

        // TODO:
        $format = 'xml';
        $appRoutingFormat = 'yml';
        
        if (file_exists($dir)) {    
            $output->writeln(array(
                '',
                $bundleName.' already exists.'
            ));
          
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Overwrite bundle?', 'yes', '?'), true)) {
                $output->writeln(array(
                    '<error>Bundle generation cancelled</error>',
                    ''
                ));
                return 1;
            } else {
                $filesystem = $container->get('filesystem');
                $filesystem->remove($dir);
            }        
        }
        
        $output->writeln('');
        $output->write('Generating bundle code: ');
        
        $bundleGenerator = new AvroBundleGenerator($container, $output);
        $bundleGenerator->generate($vendor, $basename, $bundleNamespace, $bundleName, $dir, $dbDriver);

        $output->writeln(array(
            '<info>Done</info>',
            ''
        ));
        

        $output->writeln(array(
            'Be sure to configure your bundle so that you can use it in your application',
            'You can use the command generate:avro:appConfig to ',
            'automatically configure your application with your new bundle',
            ''
        ));            
        
        
        $dialog->writeSection($output, 'Bundle Generated Successfully!'); 
        
        $output->writeln(array(
            '',
            'You can now generate CRUD for '.$bundleName.' using the command',
            'generate:avro:crud',
            ''
        ));
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Avro bundle generator');

        // namespace
        $output->writeln(array(
            '',
            'This command helps you generate 3rd party bundles easily.',
            '',
            'Enter the vendor name for the bundle. (ie. FOS, Sensio, etc)'
        ));

        $vendor = $dialog->askAndValidate($output, $dialog->getQuestion('Bundle vendor', $input->getOption('vendor')), array('Avro\GeneratorBundle\Command\Validators', 'validateVendor'), false, $input->getOption('vendor'));
        $input->setOption('vendor', $vendor);

        // bundle name
        $bundleName = $input->getOption('basename');
        $output->writeln(array(
            '',
            'Enter the name of the bundle. (ie. UserBundle, CalendarBundle, etc)'
        ));
        $bundleName = $dialog->askAndValidate($output, $dialog->getQuestion('Bundle name', $bundleName), array('Avro\GeneratorBundle\Command\Validators', 'validateBundleName'), false, $bundleName);
        $input->setOption('basename', $bundleName);

        // dbDriver
        // TODO: mongodb and couchdb support
        $dbDriver = $input->getOption('db-driver');
        $output->writeln(array(
            '',
            'Choose the database driver for the bundle. (orm)',
        ));
        $dbDriver = $dialog->askAndValidate($output, $dialog->getQuestion('Database Driver', $dbDriver), array('Avro\GeneratorBundle\Command\Validators', 'validateDbDriver'), false, $dbDriver);
        $input->setOption('db-driver', $dbDriver);          
               
        // summary
        $output->writeln(array(
                '',
                'You are going to generate a new bundle called '.$vendor.$bundleName
        ));
    }
    
    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Avro\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
    
   
}
