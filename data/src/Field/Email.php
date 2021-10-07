<?php

declare(strict_types=1);

namespace Atk4\Data\Field;

use Atk4\Data\Field;
use Atk4\Data\ValidationException;

/**
 * Stores valid email(s) as per configuration.
 *
 * Usage:
 *  $user->addField('email', [Field\Email::class]);
 *  $user->addField('email_mx_check', [Field\Email::class, 'dns_check' => true]);
 *  $user->addField('email_with_name', [Field\Email::class, 'include_names' => true]);
 *  $user->addField('emails', [Field\Email::class, 'allow_multiple' => true, 'separator' => [',',';']]);
 */
class Email extends Field
{
    /**
     * @var bool Enable lookup for MX record for email addresses stored
     */
    public $dns_check = false;

    /**
     * @var bool Permit entry of multiple email addresses, separated with comma (and extra spaces)
     */
    public $allow_multiple = false;

    /**
     * @var bool Also allow entry of names in format "Romans <me@example.com>"
     */
    public $include_names = false;

    /**
     * @var array Array of allowed separators
     */
    public $separator = [','];

    public function normalize($value)
    {
        if ($value === null) {
            return $value;
        }

        // split value by any number of separator characters
        $emails = preg_split('/[' . implode('', array_map('preg_quote', $this->separator)) . ']+/', $value, -1, \PREG_SPLIT_NO_EMPTY);

        if (!$this->allow_multiple && count($emails) > 1) {
            throw new ValidationException([$this->name => 'Only a single email can be entered'], $this->getOwner());
        }

        // now normalize each email
        $emails = array_map(function ($email) {
            $email = trim($email);

            if ($this->include_names) {
                $email = preg_replace('/^[^<]*<([^>]*)>/', '\1', $email);
            }

            if (strpos($email, '@') === false) {
                throw new ValidationException([$this->name => 'Email address does not have domain'], $this->getOwner());
            }

            [$user, $domain] = explode('@', $email, 2);
            $domain = idn_to_ascii($domain, \IDNA_DEFAULT, \INTL_IDNA_VARIANT_UTS46); // always convert domain to ASCII

            if (!filter_var($user . '@' . $domain, \FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException([$this->name => 'Email address format is invalid'], $this->getOwner());
            }

            if ($this->dns_check) {
                if (!$this->hasAnyDnsRecord($domain)) {
                    throw new ValidationException([$this->name => 'Email address domain does not exist'], $this->getOwner());
                }
            }

            return $email;
        }, $emails);

        return parent::normalize(implode(', ', $emails));
    }

    private function hasAnyDnsRecord(string $domain, array $types = ['MX', 'A', 'AAAA', 'CNAME']): bool
    {
        foreach (array_unique(array_map('strtoupper', $types)) as $t) {
            $dnsConsts = [
                'MX' => \DNS_MX,
                'A' => \DNS_A,
                'AAAA' => \DNS_AAAA,
                'CNAME' => \DNS_CNAME,
            ];

            $records = @dns_get_record($domain . '.', $dnsConsts[$t]);
            if ($records === false) { // retry once on failure
                $records = dns_get_record($domain . '.', $dnsConsts[$t]);
            }
            if ($records !== false && count($records) > 0) {
                return true;
            }
        }

        return false;
    }
}
