<?php
/**
 * Created by PhpStorm.
 * User: em
 * Date: 10.07.18
 * Time: 10:58
 */

namespace Marlinc\UserBundle\Manager;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class Marlinc1UserLoader
{
    /**
     * @var Connection[]
     */
    private $connections;

    /**
     * Marlinc1DbLoader constructor.
     * @param array $connections
     */
    public function __construct(array $connections)
    {
        $this->connections = [];

        foreach ($connections as $name => $connection) {
            if (strpos($name, 'marlinc') === 0) {
                $this->connections[] = $connection;
            }
        }
    }

    /**
     * @param Connection $connection
     * @return Marlinc1UserLoader
     */
    public function addConnection(Connection $connection): Marlinc1UserLoader
    {
        $this->connections[] = $connection;

        return $this;
    }

    /**
     * @param string $token
     * @return array|null
     */
    public function findUserByToken(string $token): ?array
    {
        return $this->findUserBy(['auth_token' => $token]);
    }

    /**
     * @param string $login
     * @return array|null
     */
    public function findUserByLogin(string $login): ?array
    {
        return $this->findUserBy([
            'login' => $login
        ]);
    }

    /**
     * @param string $login
     * @param string $password
     * @return array|null
     */
    public function findUserByCredentials(string $login, string $password): ?array
    {
        return $this->findUserBy([
            'login' => $login,
            'password' => md5($password)
        ]);
    }

    /**
     * @param array $criteria
     * @param bool $resetToken
     * @return array|null
     */
    public function findUserBy(array $criteria, $resetToken = false): ?array
    {
        foreach ($this->connections as $connection) {
            $qb = $connection->createQueryBuilder()
                ->select('u.id', 'u.login', 'u.name', 'd.data_name', 'd.data_value')
                ->from('core_users', 'u')
                ->innerJoin('u', 'core_user_data', 'd', 'u.id = d.user_id')
                ->where('u.active = 1');

            foreach ($criteria as $col => $value) {
                $qb
                    ->andWhere('u.'.$col.' = :'.$col)
                    ->setParameter(':'.$col, $value);
            }

            $userData = $qb->execute()->fetchAll(FetchMode::ASSOCIATIVE);

            if (! empty($userData)) {
                $credentials = null;

                // Map user info
                foreach ($userData as $data) {
                    if ($credentials === null) {
                        $credentials = [
                            'id' => $data['id'],
                            'email' => $data['login'],
                            'name' => $data['name'],
                            'data' => []
                        ];
                    }

                    $credentials['data'][$data['data_name']] = $data['data_value'];
                }

                // Reset auth token
                if ($resetToken) {
                    $connection->update('core_users', ['auth_token' => null], ['id' => $credentials['id']]);
                }

                return $credentials;
            }
        }

        return null;
    }
}