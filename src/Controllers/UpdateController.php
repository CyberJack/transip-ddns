<?php

namespace CyberJack\Transip\Controllers;

use DateTime;
use Exception;
use stdClass;
use Transip_DnsEntry;
use CyberJack\Transip\Crypt;
use CyberJack\Transip\Ip;
use CyberJack\Transip\Transip\DomainService;

/**
 * Class PostController
 *
 * @package CyberJack\Transip\Controllers
 */
class UpdateController extends Controller
{
	/**
	 * Update the DNS entry(s)
	 *
	 * Request = $_POST[
	 *     'signature' => '.....',
	 *     'request'   => (json object){
	 * 			'applicationKey' => '....',
	 * 			'time'           => 'yyyy-mm-dd hh:ii:ss.u'
	 * 	   }
	 * ]
	 */
	public function update()
	{
		try
		{
			$request = $this->getRequestData();
			$force = $this->container['config']->debug | isset($request->force) ? true : false;

			$lastIp = $this->container['database']->getLastIp($request->applicationKey);
			$ip = Ip::get();

			if ($force || !$lastIp || $lastIp !== $ip)
			{
				$this->container['database']->updateIp($request->applicationKey, $ip);
				$config = $this->container['config']->applications->{$request->applicationKey};
				foreach ($config as $domain => $dnsRecords)
				{
					$this->updateDnsRecords($request->applicationKey, $domain, $dnsRecords, $ip);
				}
			}
		} catch (Exception $e)
		{
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' 400 Bad Request');
			die($e->getMessage());
		}
	}

	/**
	 * Get the request from the $_POST array
	 *
	 * @throws Exception
	 * @return stdClass
	 */
	protected function getRequestData()
	{
		if (!$_POST || !isset($_POST['request'], $_POST['signature']))
		{
			// @todo: Log data
			throw new Exception("Unable to fetch update request from POST data!");
		}

		$signature = $_POST['signature'];
		$request = json_decode($_POST['request']);

		if (!isset($request->applicationKey, $request->time, $this->container['config']->applications->{$request->applicationKey}) ||
			!$this->validateRequestSignature($request, $signature) ||
			!$this->validateExpirationTime($request)
		)
		{
			// @todo: Log data
			throw new Exception("Invalid update request!");
		}

		return $request;
	}

	/**
	 * Validate a request
	 *
	 * @param stdClass $request
	 * @param string $signature
	 * @throws Exception
	 * @return bool
	 */
	protected function validateRequestSignature($request, $signature)
	{
		$requestSignature = base64_encode(
			hash_hmac('sha256', json_encode($request), $this->container['config']->signatureKey, true)
		);
		return $this->container['config']->debug | Crypt::compareStrings($requestSignature, $signature);
	}

	/**
	 * Make sure the request is no older then 1 minute
	 *
	 * @param stdClass $request
	 * @throws Exception
	 * @return bool
	 */
	protected function validateExpirationTime($request)
	{
		$diff = abs(
			DateTime::createFromFormat('Y-m-d H:i:s.u', $request->time)->getTimestamp() -
			(new DateTime())->getTimestamp()
		) / 60;
		if ($this->container['config']->debug === false & $diff > 1)
		{
			throw new Exception('Update request expired!');
		}
		return true;
	}

	/**
	 * Update all DNS records for a single domain
	 *
	 * @param string $applicationKey
	 * @param string $domain
	 * @param array $dnsRecords
	 * @param string $ip
	 * @return void
	 */
	protected function updateDnsRecords($applicationKey, $domain, $dnsRecords, $ip)
	{
		// Get the current DNS entries
		$dnsEntries = DomainService::getInfo($domain)->dnsEntries;

		// Update the dns records
		/** @var Transip_DnsEntry $dnsEntry */
		foreach ($dnsEntries as $dnsEntry)
		{
			// Check if the entry has to be updated and update it
			if (in_array($dnsEntry->name, $dnsRecords, true))
			{
				$dnsEntry->type = Transip_DnsEntry::TYPE_A;
				$dnsEntry->content = $ip;
			}
		}

		// Send the changes to TransIP
		if (!$this->container['config']->debug)
		{
			DomainService::setDnsEntries($domain, $dnsEntries);
			$this->container['database']->updateDomainLog($applicationKey, $domain, $dnsRecords);
		}
	}
}
