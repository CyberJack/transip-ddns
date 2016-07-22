<?php

namespace CyberJack\Transip;

use PDO;
use DateTime;

/**
 * Class Database
 *
 * @package CyberJack\Transip
 */
class Database implements DatabaseInterface
{
	/**
	 * The SQLite database connection
	 *
	 * @var PDO
	 */
	protected $db = null;

	/**
	 * Database constructor.
	 */
	public function __construct()
	{
		// Create (connect to) SQLite database in file
		$this->db = new PDO('sqlite:' . APP_DIR . '/data/ip.db');

		// Set errormode to exceptions
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Init
		$this->createTables();
	}

	/**
	 * Create the initial database if needed
	 *
	 * @return void
	 */
	protected function createTables()
	{
		$this->db->exec(
			'CREATE TABLE IF NOT EXISTS ip (
				id INTEGER PRIMARY KEY,
				applicationKey TEXT,
				ip TEXT
			)'
		);

		$this->db->exec(
			'CREATE TABLE IF NOT EXISTS history (
				id INTEGER PRIMARY KEY,
				applicationKey TEXT,
				domain TEXT,
				dnsRecords TEXT,
				time INTEGER
			)'
		);
	}

	/**
	 * Get the last available ip for a application
	 *
	 * @param string $applicationKey
	 * @return array
	 */
	public function getLastIp($applicationKey)
	{
		$stmt = $this->db->prepare('SELECT `ip` FROM `ip` WHERE `applicationKey` = :applicationKey');
		$stmt->bindValue(':applicationKey', $applicationKey, PDO::PARAM_STR);
		$stmt->execute();

		// Update the return set
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($result !== false)
		{
			return $result['ip'];
		}
		return null;
	}

	/**
	 * Update DNS records in the local database
	 *
	 * @param string $applicationKey
	 * @param string $ip
	 * @return void
	 */
	public function updateIp($applicationKey, $ip)
	{
		$lastIp = $this->getLastIp($applicationKey);
		$this->db->beginTransaction();

		// Update last ip
		if(!$lastIp)
		{
			$update = $this->db->prepare('INSERT INTO `ip` (`ip`, `applicationKey`) VALUES (:ip, :applicationKey)');
		}
		else
		{
			$update = $this->db->prepare('UPDATE `ip` SET `ip` = :ip WHERE `applicationKey` = :applicationKey');
		}
		$update->bindParam(':ip', $ip, PDO::PARAM_STR);
		$update->bindParam(':applicationKey', $applicationKey, PDO::PARAM_STR);
		$update->execute();

		$this->db->commit();
	}

	/**
	 * Add a domain change log entry
	 *
	 * @param string $applicationKey
	 * @parsm string $domain
	 * @param array $dnsRecords
	 * @return void
	 */
	public function updateDomainLog($applicationKey, $domain, $dnsRecords)
	{
		$this->db->beginTransaction();

		$stmt = $this->db->prepare(
			'INSERT INTO `history` (`applicationKey`, `domain`, `dnsRecords`, `time`)
				VALUES (:applicationKey, :domain, :dnsRecords, :time)'
		);
		$stmt->bindValue(':applicationKey', $applicationKey, PDO::PARAM_STR);
		$stmt->bindValue(':domain', $domain, PDO::PARAM_STR);
		$stmt->bindValue(':dnsRecords', serialize($dnsRecords), PDO::PARAM_STR);
		$stmt->bindValue(':time', (new DateTime())->format('U'));
		$stmt->execute();

		$this->db->commit();
	}
}
