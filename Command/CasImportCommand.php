<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 26.08.16
 * Time: 15:58
 */

namespace Marlinc\UserBundle\Command;

use Doctrine\ORM\EntityManager;
use Marlinc\UserBundle\Entity\CasUser;
use Marlinc\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CasImportCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('cas:mapping:update');
        $this->setDescription('Update CasUser mapping data');
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>> Start update CasUsers</info>');
        $step = 0;

        // German cas users.
        $output->writeln('<info>> Downloading CasUsers list (DE)</info>');

        $fileName = 'cas_users_de.csv';
        $file     = $this->download($output, 'https://aha:ucMosId2@login.gdhs.net/sso/token/gdhsnet_IAM_DE_user_format_line_per_companyrelation.csv', $this->getTempDir('/'.$fileName));

        $output->writeln('Load new CAS user list to table...wait');
        $output->writeln('Processing downloaded data...');

        $step += $this->decodeFile($output, $file, "|", 'DE', 1);

        // Swiss cas users.
        $output->writeln('<info>> Downloading CasUsers list (CH)</info>');

        $fileName = 'cas_users_ch.csv';
        $file     = $this->download($output, 'https://aha:ucMosId2@login.gdhs.net/sso/token/ch/gdhsnet_IAM_CH_user_format_line_per_companyrelation.csv', $this->getTempDir('/'.$fileName));

        $output->writeln('Load new CAS user list to table...wait');
        $output->writeln('Processing downloaded data...');

        $step += $this->decodeFile($output, $file, "|", 'CH', 1);

        $output->writeln('<info>> '.$step.' Users imported.</info>');
        $output->writeln('');
        $output->writeln('Done!');
    }

    /**
     * Get temp dir path
     * @param $path
     * @return string
     */
    protected function getTempDir($path = null)
    {
        $tempDir = $this->getContainer()->get('kernel')->getCacheDir().'/cas';
        if (!is_dir($tempDir)) {
            mkdir($tempDir);
        }

        if ($path) {
            $tempDir .= $path;
        }

        return $tempDir;
    }

    protected function decodeFile(OutputInterface $output, $file, $delimiter, $country, $firstLine = 0)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        // Disable SQL logger
        $connection = $em->getConnection();
        $connection->getConfiguration()->setSQLLogger(null);

        $output->writeln('Processing file...');

        // Prepare file by counting lines.
        $handler = fopen($file, 'r');
        $count   = 0;
        while (!feof($handler)) {
            fgets($handler);
            $count++;
        }

        fclose($handler);
        $handler = fopen($file, 'r');

        // Init progress bar.
        $progress = new ProgressBar($output);
        $progress->setFormat('normal_nomax');
        $step = 0;
        $sql  = '';

        $progress->start($count);

        // Step through lines of cas file.
        while (!feof($handler)) {
            $step++;
            $line = fgets($handler);

            if ($step > $firstLine) {
                $values = explode($delimiter, $line);
                if (count($values) > 1) {
                    $username = $values['1'];

                    // Try to find existing cas user entry.
                    $casUser = $em->getRepository('CasUser.php')
                        ->findOneBy(['username' => $username]);

                    if (is_null($casUser)) {
                        $casUser = new CasUser();
                    }

                    // Update values.
                    $casUser
                        ->setUsername($values[1])
                        ->setUsernumber($values[2])
                        ->setSapnr($this->updateSapNumber($values[0]))
                        ->setFullname($values[3])
                        ->setChannel($values[5])
                        ->setEntrepreneur($values[6]);

                    // Mapping of cas users to marlinc users.
                    if ($casUser->getUser() == null) {
                        $this->assignUser($em, $casUser);
                    }

                    $em->persist($casUser);

                    if (($step % 50) === 0) {
                        $progress->setProgress($step);
                        $em->flush();
                    }
                }
            }
        }

        $em->flush();

        return $step;
    }

    /**
     * @param EntityManager $em
     * @param CasUser $casUser
     * @return User
     */
    protected function assignUser(EntityManager $em, CasUser $casUser) {
        $pe = $this->getContainer()->get('security.password_encoder');

        // Get first and last name.
        $names = explode(' ', $casUser->getFullname());
        foreach ($names as $key => $name) {
            $names[$key] = trim($name);
        }
        $firstname = array_shift($names);
        $lastname = implode(' ', $names);

        $possibleUsers = $em->getRepository('MarlincUserBundle:User')
            ->findBy(['firstname' => $firstname, 'lastname' => $lastname]);

        $client = $em->getRepository('MarlincClientBundle:Client')
            ->findOneBy(['registrationId' => $casUser->getSapnr()]);
        $clientAssigned = false;

        if (count($possibleUsers) == 0) {
            $user = new User();

            $user->setEnabled(true);
            $user->setPassword($pe->encodePassword($user, 'DTbhwr%Qb46bHvqa354h'));
            $user->setEmail($casUser->getUsername().'-cas@marlinc.com');
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setLocale('de');
        } else {
            $user = $possibleUsers[0];

            // Try to match client with user.
            if (!is_null($client)) {
                foreach ($possibleUsers as $user) {
                    $clientUsers = $client->getUsers();
                    if ($clientUsers->contains($user)) {
                        $clientAssigned = true;
                        break;
                    }
                }
            }
        }

        // Assign marlinc users to clients if applicable.
        if (!is_null($client) && !$clientAssigned) {
            $client->addUser($user);
            $em->persist($client);
        }

        // Assign marlinc user to cas user.
        $casUser->setUser($user);

        return $user;
    }

    /**
     * Update possible SAP nr to match the canonical format (10 digit number).
     *
     * @param $value
     * @return string
     */
    protected function updateSapNumber($value) {
        if (intval($value) > 0) {
            if (strlen($value) < 6) {
            $value = str_pad($value, 6, '0', STR_PAD_LEFT);
            }
            if (intval($value) < 100000) {
                return str_pad($value, 10, '0030', STR_PAD_LEFT);
            } else {
                return str_pad($value, 10, '0000', STR_PAD_LEFT);
            }
        } else {
            return 'gdts';
        }
    }

    /**
     * Download file from url.
     */
    protected function download(OutputInterface &$output, $from, $to)
    {
        $output->writeln('Download '.$from);
        $progress = new ProgressBar($output);
        $progress->setFormat('normal_nomax');
        $step     = 0;
        $ctx      = stream_context_create(
            array(),
            array(
                'notification' => function ($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max) use ($output, $progress, &$step) {
                    switch ($notification_code) {
                        case STREAM_NOTIFY_FILE_SIZE_IS:
                            $progress->start(100);
                            break;
                        case STREAM_NOTIFY_PROGRESS:
                            $newStep = round(($bytes_transferred / $bytes_max) * 100);
                            if ($newStep > $step) {
                                $step = $newStep;
                                $progress->setProgress($step);
                            }
                            break;
                    }
                },
            )
        );

        $file = file_get_contents($from, false, $ctx);
        $progress->finish();
        file_put_contents($to, $file);
        $output->writeln('');

        return $to;
    }
}