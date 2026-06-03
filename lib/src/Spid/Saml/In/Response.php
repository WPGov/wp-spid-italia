<?php

namespace Italia\Spid\Spid\Saml\In;

use Italia\Spid\Spid\Interfaces\ResponseInterface;
use Italia\Spid\Spid\Session;
use Italia\Spid\Spid\Saml;

class Response implements ResponseInterface
{

    private $saml;

    public function __construct(Saml $saml)
    {
        $this->saml = $saml;
    }

    public function validate($xml, $hasAssertion): bool
    {
        $accepted_clock_skew_seconds = isset($this->saml->settings['accepted_clock_skew_seconds']) ?
            $this->saml->settings['accepted_clock_skew_seconds'] : 0;

        $accepted_clock_skew_seconds = 7200; // MM 20210915
        
        $root = $xml->getElementsByTagName('Response')->item(0);

        if ($root->getAttribute('Version') == "") {
            throw new \Exception("Missing Version attribute");
        } elseif ($root->getAttribute('Version') != '2.0') {
            throw new \Exception("Invalid Version attribute");
        }
        if ($root->getAttribute('IssueInstant') == "") {
            throw new \Exception("Missing IssueInstant attribute on Response");
        } elseif (!$this->validateDate($root->getAttribute('IssueInstant'))) {
            throw new \Exception("Invalid IssueInstant attribute on Response");
        } elseif (strtotime($root->getAttribute('IssueInstant')) > strtotime('now') + $accepted_clock_skew_seconds) {
            throw new \Exception("IssueInstant attribute on Response is in the future");
        }

        if ($root->getAttribute('InResponseTo') == "" || !isset($_SESSION['RequestID'])) {
            throw new \Exception("Missing InResponseTo attribute, or request ID was not saved correctly " .
                "for comparison");
        } elseif ($root->getAttribute('InResponseTo') != $_SESSION['RequestID']) {
            throw new \Exception("Invalid InResponseTo attribute, expected " . $_SESSION['RequestID'] .
                " but received " . $root->getAttribute('InResponseTo'));
        }

        if ($root->getAttribute('Destination') == "") {
            throw new \Exception("Missing Destination attribute");
        } elseif ($root->getAttribute('Destination') != $_SESSION['acsUrl']) {
            throw new \Exception("Invalid Destination attribute, expected " . $_SESSION['acsUrl'] .
                " but received " . $root->getAttribute('Destination'));
        }

        if ($xml->getElementsByTagName('Issuer')->length == 0) {
            throw new \Exception("Missing Issuer attribute");
            //check item 0, this the Issuer element child of Response
        } elseif ($xml->getElementsByTagName('Issuer')->item(0)->nodeValue != $_SESSION['idpEntityId']) {
            throw new \Exception("Invalid Issuer attribute, expected " . $_SESSION['idpEntityId'] .
                " but received " . $xml->getElementsByTagName('Issuer')->item(0)->nodeValue);
        } elseif ($xml->getElementsByTagName('Issuer')->item(0)->getAttribute('Format') !=
            'urn:oasis:names:tc:SAML:2.0:nameid-format:entity') {
                // MM 20210408
            //throw new \Exception("Invalid Issuer attribute, expected 'urn:oasis:names:tc:SAML:2.0:nameid-format:" ."entity'" . " but received " . $xml->getElementsByTagName('Issuer')->item(0)->getAttribute('Format'));
        }

        if ($hasAssertion) {
            $assertion = $xml->getElementsByTagName('Assertion')->item(0);
            if (is_null($assertion)) {
                throw new \Exception("Assertion element not found");
            }
            
            // Validate Assertion attributes
            if ($assertion->getAttribute('ID') == "" || $assertion->getAttribute('ID') == null) {
                throw new \Exception("Missing ID attribute on Assertion");
            } elseif ($assertion->getAttribute('Version') != '2.0') {
                throw new \Exception("Invalid Version attribute on Assertion");
            } elseif ($assertion->getAttribute('IssueInstant') == "") {
                throw new \Exception("Invalid IssueInstant attribute on Assertion");
            } elseif (!$this->validateDate($assertion->getAttribute('IssueInstant'))) {
                throw new \Exception("Invalid IssueInstant attribute on Assertion");
            } elseif (strtotime($assertion->getAttribute('IssueInstant')) >
                strtotime('now') + $accepted_clock_skew_seconds) {
                throw new \Exception("IssueInstant attribute on Assertion is in the future");
            }

            $assertionIssuers = $assertion->getElementsByTagName('Issuer');
            if ($assertionIssuers->length == 0) {
                throw new \Exception("Missing Issuer in Assertion");
            } elseif ($assertionIssuers->item(0)->nodeValue != $_SESSION['idpEntityId']) {
                throw new \Exception("Invalid Issuer attribute, expected " . $_SESSION['idpEntityId'] .
                    " but received " . $assertionIssuers->item(0)->nodeValue);
            } elseif ($assertionIssuers->item(0)->getAttribute('Format') !=
                'urn:oasis:names:tc:SAML:2.0:nameid-format:entity') {
                throw new \Exception("Invalid Issuer attribute, expected 'urn:oasis:names:tc:SAML:2.0:nameid-format:" .
                "entity'" . " but received " . $assertionIssuers->item(0)->getAttribute('Format'));
            }

            $conditions = $assertion->getElementsByTagName('Conditions');
            if ($conditions->length == 0) {
                throw new \Exception("Missing Conditions attribute");
            } elseif ($conditions->item(0)->getAttribute('NotBefore') == "") {
                throw new \Exception("Missing NotBefore attribute");
            } elseif (!$this->validateDate($conditions->item(0)->getAttribute('NotBefore'))) {
                throw new \Exception("Invalid NotBefore attribute");
            } elseif (strtotime($conditions->item(0)->getAttribute('NotBefore')) >
                strtotime('now') + $accepted_clock_skew_seconds) {
                throw new \Exception("NotBefore attribute is in the future");
            } elseif ($conditions->item(0)->getAttribute('NotOnOrAfter') == "") {
                throw new \Exception("Missing NotOnOrAfter attribute");
            } elseif (!$this->validateDate($conditions->item(0)->getAttribute('NotOnOrAfter'))) {
                throw new \Exception("Invalid NotOnOrAfter attribute");
            } elseif (strtotime($conditions->item(0)->getAttribute('NotOnOrAfter')) <=
                strtotime('now') - $accepted_clock_skew_seconds) {
                throw new \Exception("NotOnOrAfter attribute is in the past");
            }

            $audienceRestriction = $assertion->getElementsByTagName('AudienceRestriction');
            if ($audienceRestriction->length == 0) {
                throw new \Exception("Missing AudienceRestriction attribute");
            }

            $audience = $assertion->getElementsByTagName('Audience');
            if ($audience->length == 0) {
                throw new \Exception("Missing Audience attribute");
            } elseif ($audience->item(0)->nodeValue != $this->saml->settings['sp_entityid']) {
                throw new \Exception("Invalid Audience attribute, expected " . $this->saml->settings['sp_entityid'] .
                    " but received " . $audience->item(0)->nodeValue);
            }

            $nameId = $assertion->getElementsByTagName('NameID');
            if ($nameId->length == 0) {
                throw new \Exception("Missing NameID attribute");
            } elseif ($nameId->item(0)->getAttribute('Format') !=
                'urn:oasis:names:tc:SAML:2.0:nameid-format:transient') {
                throw new \Exception("Invalid NameID attribute, expected " .
                "'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'" . " but received " .
                $nameId->item(0)->getAttribute('Format'));
            } elseif ($nameId->item(0)->getAttribute('NameQualifier') !=
                $_SESSION['idpEntityId']) {
                throw new \Exception("Invalid NameQualifier attribute, expected " . $_SESSION['idpEntityId'] .
                    " but received " . $nameId->item(0)->getAttribute('NameQualifier'));
            }

            $subjectConfirmationData = $assertion->getElementsByTagName('SubjectConfirmationData');
            if ($subjectConfirmationData->length == 0) {
                throw new \Exception("Missing SubjectConfirmationData attribute");
            } elseif ($subjectConfirmationData->item(0)->getAttribute('InResponseTo') !=
                $_SESSION['RequestID']) {
                throw new \Exception("Invalid SubjectConfirmationData attribute, expected " . $_SESSION['RequestID'] .
                    " but received " . $subjectConfirmationData->item(0)->getAttribute('InResponseTo'));
            } elseif (strtotime($subjectConfirmationData->item(0)->getAttribute('NotOnOrAfter')) <= 
                strtotime('now') - $accepted_clock_skew_seconds) {
                throw new \Exception("Invalid NotOnOrAfter attribute");
            } elseif ($subjectConfirmationData->item(0)->getAttribute('Recipient') !=
                $_SESSION['acsUrl']) {
                throw new \Exception("Invalid Recipient attribute, expected " . $_SESSION['acsUrl'] .
                    " but received " . $subjectConfirmationData->item(0)->getAttribute('Recipient'));
            }

            $subjectConfirmation = $assertion->getElementsByTagName('SubjectConfirmation');
            if ($subjectConfirmation->length == 0) {
                throw new \Exception("Missing SubjectConfirmation element");
            } elseif ($subjectConfirmation->item(0)->getAttribute('Method') !=
                'urn:oasis:names:tc:SAML:2.0:cm:bearer') {
                throw new \Exception("Invalid Method attribute, expected 'urn:oasis:names:tc:SAML:2.0:cm:bearer'" .
                    " but received " . $subjectConfirmation->item(0)->getAttribute('Method'));
            }

            $attributes = $assertion->getElementsByTagName('Attribute');
            if ($attributes->length == 0) {
                throw new \Exception("Missing Attribute Element");
            }

            $attributeValues = $assertion->getElementsByTagName('AttributeValue');
            if ($attributeValues->length == 0) {
                throw new \Exception("Missing AttributeValue Element");
            }
        }

        if ($xml->getElementsByTagName('Status')->length <= 0) {
            throw new \Exception("Missing Status element");
        } elseif ($xml->getElementsByTagName('Status')->item(0) == null) {
            throw new \Exception("Missing Status element");
        } elseif ($xml->getElementsByTagName('StatusCode')->item(0) == null) {
            throw new \Exception("Missing StatusCode element");
        } elseif ($xml->getElementsByTagName('StatusCode')->item(0)->getAttribute('Value') ==
            'urn:oasis:names:tc:SAML:2.0:status:Success') {
            if ($hasAssertion && $xml->getElementsByTagName('AuthnStatement')->length <= 0) {
                throw new \Exception("Missing AuthnStatement element");
            }
        } elseif ($xml->getElementsByTagName('StatusCode')->item(0)->getAttribute('Value') !=
            'urn:oasis:names:tc:SAML:2.0:status:Success') {
            if ($xml->getElementsByTagName('StatusMessage')->item(0) != null) {
                $StatusMessage = ' [message: ' . $xml->getElementsByTagName('StatusMessage')->item(0)->nodeValue . ']';
            } else {
                $StatusMessage = "";
            }
            throw new \Exception("StatusCode is not Success" . $StatusMessage);
        } elseif ($xml->getElementsByTagName('StatusCode')->item(1)->getAttribute('Value') ==
            'urn:oasis:names:tc:SAML:2.0:status:AuthnFailed') {
            throw new \Exception("AuthnFailed AuthnStatement element");
        } else {
            // Status code != success
            return false;
        }

        // Response OK
        $session = $this->spidSession($xml);
        $_SESSION['spidSession'] = (array)$session;
        unset($_SESSION['RequestID']);
        unset($_SESSION['idpName']);
        unset($_SESSION['idpEntityId']);
        unset($_SESSION['acsUrl']);
        return true;
    }

    private function validateDate($date)
    {
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.\d+)?Z$/', $date, $parts) == true) {
            $time = gmmktime($parts[4], $parts[5], $parts[6], $parts[2], $parts[3], $parts[1]);

            $input_time = strtotime($date);
            if ($input_time === false) {
                return false;
            }

            return $input_time == $time;
        } else {
            return false;
        }
    }

    private function spidSession(\DOMDocument $xml)
    {
        $session = new Session();

        $attributes = array();

        $assertionElements = $xml->getElementsByTagName('Assertion');
        if ($assertionElements->length == 0) {
            throw new \Exception("No Assertion found in Response");
        }
        $assertion = $assertionElements->item(0);

        $attributeStatements = $assertion->getElementsByTagName('AttributeStatement');

        if ($attributeStatements->length > 0) {
            foreach ($attributeStatements->item(0)->childNodes as $attr) {
                if ($attr->hasAttributes()) {
                    $attrName = $attr->attributes->getNamedItem('Name');
                    if (!is_null($attrName)) {
                        $attributes[$attrName->value] = trim($attr->nodeValue);
                    }
                }
            }
        }

        $session->sessionID = $_SESSION['RequestID'];
        $session->idp = $_SESSION['idpName'];
        $issuerElements = $xml->getElementsByTagName('Issuer');
        if ($issuerElements->length == 0) {
            throw new \Exception("Missing Issuer in Response");
        }
        $session->idpEntityID = $issuerElements->item(0)->nodeValue;
        $session->attributes = $attributes;

        $authnContextElements = $assertion->getElementsByTagName('AuthnContextClassRef');
        if ($authnContextElements->length > 0) {
            $session->level = substr($authnContextElements->item(0)->nodeValue, -1);
        }

        return $session;
    }
}
