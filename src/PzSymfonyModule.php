<?php

declare(strict_types=1);

namespace ProjectZer0\PzSymfony;

use LogicException;
use ProjectZer0\Pz\Config\PzModuleConfigurationInterface;
use ProjectZer0\Pz\Console\Command\ProcessCommand;
use ProjectZer0\Pz\Module\PzModule;
use ProjectZer0\Pz\Process\ProcessInterface;
use ProjectZer0\Pz\ProjectZer0Toolkit;
use ProjectZer0\PzDockerCompose\Process\DockerComposeExecProcess;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class PzSymfonyModule extends PzModule
{
    private ?string $cwd = null;

    public function getCommands(): array
    {
        return [
            new class($this->getCWD()) extends ProcessCommand {
                protected static $defaultName = 'symfony:console';

                public function __construct(private string $cwd)
                {
                    parent::__construct();
                }

                protected function configure(): void
                {
                    $this->setDescription('Dependency Management for PHP')
                        ->setAliases(
                            [
                                'c',
                                'console',
                                'sf',
                            ]
                        )
                        ->setDescription('Runs Symfony Console inside app container')
                        ->addOption(
                            'env',
                            null,
                            InputOption::VALUE_REQUIRED,
                            'Docker Compose environment file defined in ".pz.yaml"',
                            'dev',
                        );
                }

                public function getProcess(
                    array $processArgs,
                    InputInterface $input,
                    OutputInterface $output
                ): ProcessInterface {
                    $serviceName = $this->getConfiguration(
                        )['symfony']['docker_compose']['service_name'] ?? 'sf_console';
                    $path        = $this->getConfiguration()['symfony']['symfony_console_path'] ?? './bin/console';

                    $args = [
                        $path,
                        ...$processArgs,
                    ];

                    return new DockerComposeExecProcess(
                        $this->getConfiguration(),
                        $input->getOption('env'),
                        $serviceName,
                        $args,
                        interactive: true,
                        exec: true
                    );
                }
            },
        ];
    }

    public function boot(ProjectZer0Toolkit $toolkit): void
    {
        $this->cwd = $toolkit->getCurrentDirectory();
    }

    /**
     * @psalm-suppress PossiblyUndefinedMethod
     */
    public function getConfiguration(): ?PzModuleConfigurationInterface
    {
        return new class() implements PzModuleConfigurationInterface {
            public function getConfigurationNode(): NodeDefinition
            {
                $treeBuilder = new TreeBuilder('symfony');

                return $treeBuilder->getRootNode()
                    ->children()
                        ->scalarNode('symfony_console_path')
                            ->defaultValue('$PZ_PWD/bin/console')
                        ->end()
                        ->arrayNode('docker_compose')
                        ->children()
                            ->scalarNode('service_name')
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()
                ->end();
            }
        };
    }

    public function getName(): string
    {
        return 'symnfony';
    }

    private function getCWD(): string
    {
        if (null === $this->cwd) {
            throw new LogicException('PzModule was not initialized correctly');
        }

        return $this->cwd;
    }
}
