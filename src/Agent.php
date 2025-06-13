<?php
/**
 * Inventory
 *
 * PHP Version 8.2
 * SugarCRM Versions 6.5 - 7.6
 *
 * @author Rémi Sauvat
 * @copyright 2005-2015 iNet Process
 *
 * @package inetprocess/inventory
 *
 * @license GNU General Public License v2.0
 *
 * @link http://www.inetprocess.com
 */

namespace Inet\Inventory;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Inet\Inventory\Facter\FacterInterface;

/**
 * Send gathered facts to Inventory server.
 */
class Agent
{
    protected array $facters;
    protected LoggerInterface $logger;
    protected Client $client;
    protected string $account_name;

    const SYSTEM = 0;
    const SUGARCRM = 1;

    public function __construct(LoggerInterface $logger, Client $client, string $account_name = '')
    {
        $this->facters = [];
        $this->logger = $logger;
        $this->account_name = $account_name;
        $this->client = $client;
    }

    public function setFacter(FacterInterface $facter, int $type): void
    {
        $this->facters[$type] = $facter;
    }

    public function getFacter(int $type): FacterInterface
    {
        if (!isset($this->facters[$type])) {
            throw new \RuntimeException('No facter found for this type. Please set the facter object first.');
        }
        return $this->facters[$type];
    }

    public function getFacts(int $type): array
    {
        return $this->getFacter($type)->getFacts();
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getServerFqdn(): string
    {
        $facts = $this->getFacts(self::SYSTEM);
        return $facts['fqdn'];
    }

    public function getAccountId(string $account_name): ?string
    {
        return $this->getEntityId('Account', 'name', $account_name);
    }

    public function getServerId(string $server_fqdn): ?string
    {
        return $this->getEntityId('Server', 'fqdn', $server_fqdn);
    }

    private function getEntityId(string $entity_name, string $key_id, string $search): ?string
    {
        $client = $this->getClient();
        try {
            $response = $client->get("{$entity_name}s/{$search}");
            $entity = json_decode($response->getBody()->getContents(), true);
            return $entity['id'] ?? null;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return null;
            }
            throw $e;
        }
    }

    public function sendServer(): void
    {
        $this->getLogger()->info('Fetching system facts.');
        $facts = $this->getFacts(self::SYSTEM);
        $fqdn = $facts['fqdn'];
        $server_data = [
            'fqdn' => $fqdn,
            'facts' => $facts,
        ];
        $this->sendEntity('Server', $server_data, 'fqdn');
    }

    public function sendAccount(): void
    {
        $this->sendEntity(
            'Account',
            ['name' => $this->account_name],
            'name'
        );
    }

    public function sendSugarInstance(?string $server_id = null, ?string $account_id = null): void
    {
        $this->getLogger()->info('Fetch sugarcrm facts.');
        $facts = $this->getFacts(self::SUGARCRM);
        $sugar_data = [
            'instance_id' => $facts['instance_id'],
            'facts' => $facts,
        ];
        if ($server_id !== null) {
            $sugar_data['server'] = $server_id;
        }
        if ($account_id !== null) {
            $sugar_data['account'] = $account_id;
        }
        $this->sendEntity('SugarInstance', $sugar_data, 'instance_id');
    }

    private function sendEntity(string $entity_name, array $data, string $key_id): void
    {
        $client = $this->getClient();
        $this->getLogger()->info('Sending new ' . $entity_name . '.');
        try {
            $this->getLogger()->info('Try to PUT data to existing ' . $entity_name . ' record.');
            $data[$key_id . '_uri'] = $data[$key_id];
            $client->put("{$entity_name}s/{$data[$key_id]}", ['json' => $data]);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                // The server doesn't exist yet. We need to POST it.
                $this->getLogger()->info($entity_name . ' was not found on PUT request. Doing POST to create it.');
                $client->post("{$entity_name}s", ['json' => $data]);
            } else {
                // This is not a 404 error, throw the exception.
                throw $e;
            }
        }
        $this->getLogger()->info('The ' . $entity_name . ' information has been successfully sent.');
    }

    public function sendAll(): void
    {
        $this->sendServer();
        $server_id = $this->getServerId($this->getServerFqdn());
        $this->sendAccount();
        $account_id = $this->getAccountId($this->account_name);
        $this->sendSugarInstance($server_id, $account_id);
    }
}
