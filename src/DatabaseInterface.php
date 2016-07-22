<?php

namespace CyberJack\Transip;

/**
 * Interface DatabaseInterface
 *
 * @package CyberJack\Transip
 */
interface DatabaseInterface
{
	/**
	 * Get the last available ip for a application
	 *
	 * @param string $applicationKey
	 * @return array
	 */
	public function getLastIp($applicationKey);

	/**
	 * Update DNS records in the local database
	 *
	 * @param string $applicationKey
	 * @param string $ip
	 * @return void
	 */
	public function updateIp($applicationKey, $ip);

	/**
	 * Add a domain change log entry
	 *
	 * @param string $applicationKey
	 * @parsm string $domain
	 * @param array $dnsRecords
	 * @return void
	 */
	public function updateDomainLog($applicationKey, $domain, $dnsRecords);
}
