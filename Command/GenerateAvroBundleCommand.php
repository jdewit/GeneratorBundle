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
                new InputOption('basename', '', InputOption::VALUE_REQUIRED, 'The bundle basename'),
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
        $container = $this->getContainer();
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Avro bundle generator');

        // namespace
        $output->writeln(array(
            '',
            'This command helps you generate bundles easily.',
            '',
            'Enter the vendor name for the bundle. (ie. FOS, Sensio, etc)'
        ));

        // vendor
        $vendor = $dialog->askAndValidate($output, $dialog->getQuestion('Bundle vendor', $input->getOption('vendor')), array('Avro\GeneratorBundle\Command\Validators', 'validateVendor'), false, $input->getOption('vendor'));
        $vendor = Validators::validateVendor($vendor);

        // bundle name
        $basename = $input->getOption('basename');
        $output->writeln(array(
            '',
            'Enter the basename of the bundle. (ie. UserBundle, CalendarBundle, etc)'
        ));
        $basename = $dialog->askAndValidate($output, $dialog->getQuestion('Bundle basename', $basename), array('Avro\GeneratorBundle\Command\Validators', 'validateBundleName'), false, $basename);
        $bundleName = Validators::validateBundleName($vendor.$basename);

        //bundleNamespace
        $bundleNamespace = Validators::validateBundleNamespace($vendor.'\\'.$basename);

        //third party?
        $thirdParty = $dialog->askConfirmation($output, $dialog->getQuestion('Is this a 3rd party bundle ', 'yes', '?'), false);
            
        //dir
        if ($thirdParty) {
            $dir = dirname($this->getContainer()->getParameter('kernel.root_dir')).'/src/'.$vendor.'/'.$basename;
        } else {
            $dir = dirname($this->getContainer()->getParameter('kernel.root_dir')).'/vendor/bundles/'.$vendor.'/'.$basename;       
        }
        
        // dbDriver
        // TODO: mongodb and couchdb support
        $dbDriver = $input->getOption('db-driver');
        $output->writeln(array(
            '',
            'Choose the database driver for the bundle. (orm)',
        ));
        $dbDriver = $dialog->askAndValidate($output, $dialog->getQuestion('Database Driver', $dbDriver), array('Avro\GeneratorBundle\Command\Validators', 'validateDbDriver'), false, $dbDriver);
        $dbDriver = Validators::validateDbDriver($dbDriver);       
        // summary
        $output->writeln(array(
                '',
                'You are going to generate a new bundle called '.$vendor.$bundleName
        ));
        
        // update routing.yml
        $updateConfig = $dialog->askConfirmation($output, $dialog->getQuestion('Update your apps config file? ', 'yes', '?'), true);
            
        
        if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
            $output->writeln('<error>Command aborted</error>');

            return 1;
        }

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
        $bundleGenerator->generate($thirdParty, $vendor, $basename, $bundleNamespace, $bundleName, $dir, $dbDriver, $updateConfig);
        
        $output->writeln(array(
            '<info>Done</info>',
            ''
        ));
        
        $dialog->writeSection($output, 'Bundle Generated Successfully!'); 
        
        $output->writeln(array(
            '',
            'add this line to the "imports" node in your config.yml file',
            "- { resource: '@".$bundleName."/Resources/config/config.yml' }",
            '',
            
        ));
        
        $output->writeln(array(
            '',
            'You can now generate CRUD for '.$bundleName.' using the command',
            'generate:avro:crud',
            ''
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
