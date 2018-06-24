<?php
namespace TYPO3\Surf\Task\Generic;

/*
 * This file is part of TYPO3 Surf.
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Domain\Model\Task;
use TYPO3\Surf\Domain\Service\ShellCommandServiceAwareInterface;
use TYPO3\Surf\Domain\Service\ShellCommandServiceAwareTrait;

/**
 * Creates directories for a release.
 *
 * It takes the following options:
 *
 * * baseDirectory (optional) - Can be set as base path.
 * * directories - An array of directories to create. The paths can be relative to the baseDirectory, if set.
 *
 * Example:
 *  $workflow
 *      ->setTaskOptions('TYPO3\Surf\Task\Generic\CreateDirectoriesTask', [
 *              'baseDirectory' => '/var/www/outerspace',
 *              'directories' => [
 *                  'uploads/spaceship',
 *                  'uploads/freighter',
 *                  '/tmp/outerspace/lonely_planet'
 *              ]
 *          ]
 *      );
 */
class CreateDirectoriesTask extends Task implements ShellCommandServiceAwareInterface
{
    use ShellCommandServiceAwareTrait;

    /**
     * Execute this task
     *
     * @param \TYPO3\Surf\Domain\Model\Node $node
     * @param \TYPO3\Surf\Domain\Model\Application $application
     * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
     * @param array $options
     */
    public function execute(Node $node, Application $application, Deployment $deployment, array $options = [])
    {
        if (!isset($options['directories']) || !is_array($options['directories']) || $options['directories'] === []) {
            return;
        }

        $baseDirectory = isset($options['baseDirectory']) ? $options['baseDirectory'] : $deployment->getApplicationReleasePath($application);

        $commands = [
            'cd ' . $baseDirectory
        ];
        foreach ($options['directories'] as $path) {
            $commands[] = 'mkdir -p ' . $path;
        }

        $this->shell->executeOrSimulate($commands, $node, $deployment);
    }

    /**
     * Simulate this task
     *
     * @param \TYPO3\Surf\Domain\Model\Node $node
     * @param \TYPO3\Surf\Domain\Model\Application $application
     * @param \TYPO3\Surf\Domain\Model\Deployment $deployment
     * @param array $options
     */
    public function simulate(Node $node, Application $application, Deployment $deployment, array $options = [])
    {
        $this->execute($node, $application, $deployment, $options);
    }
}
