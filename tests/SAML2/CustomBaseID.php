<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2;

use DOMElement;
use SimpleSAML\SAML2\XML\saml\Audience;
use SimpleSAML\SAML2\XML\saml\AbstractBaseID;
use SimpleSAML\Test\SAML2\Constants as C;
use SimpleSAML\Assert\Assert;

/**
 * Example class to demonstrate how BaseID can be extended.
 *
 * @covers \SimpleSAML\Test\SAML2\CustomBaseID
 * @package simplesamlphp\saml2
 */
final class CustomBaseID extends AbstractBaseID
{
    /** @var string */
    protected const XSI_TYPE_NAME = 'CustomBaseIDType';

    /** @var string */
    protected const XSI_TYPE_NAMESPACE = C::NAMESPACE;

    /** @var string */
    protected const XSI_TYPE_PREFIX = 'ssp';

    /** @var \SimpleSAML\SAML2\XML\saml\Audience[] $audience */
    protected array $audience;


    /**
     * CustomBaseID constructor.
     *
     * @param \SimpleSAML\SAML2\XML\saml\Audience[] $audience
     * @param string|null $NameQualifier
     * @param string|null $SPNameQualifier
     */
    public function __construct(array $audience, string $NameQualifier = null, string $SPNameQualifier = null)
    {
        parent::__construct(self::XSI_TYPE_PREFIX . ':' . self::XSI_TYPE_NAME, $NameQualifier, $SPNameQualifier);
        $this->setAudience($audience);
    }


    /**
     * Get the value of the audience-attribute.
     *
     * @return \SimpleSAML\SAML2\XML\saml\Audience[]
     */
    public function getAudience(): array
    {
        return $this->audience;
    }


    /**
     * Set the value of the audience-attribute
     *
     * @param \SimpleSAML\SAML2\XML\saml\Audience[] $audience
     */
    protected function setAudience(array $audience): void
    {
        Assert::allIsInstanceOf($audience, Audience::class);
        $this->audience = $audience;
    }


    /**
     * @inheritDoc
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'BaseID', InvalidDOMElementException::class);
        Assert::notNull($xml->namespaceURI, InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, AbstractBaseID::NS, InvalidDOMElementException::class);
        Assert::true(
            $xml->hasAttributeNS(C::NS_XSI, 'type'),
            'Missing required xsi:type in <saml:BaseID> element.',
            SchemaViolationException::class
        );

        $type = $xml->getAttributeNS(C::NS_XSI, 'type');
        Assert::same($type, self::XSI_TYPE_PREFIX . ':' . self::XSI_TYPE_NAME);

        $nameQualifier = self::getAttribute($xml, 'NameQualifier', null);
        $spNameQualifier = self::getAttribute($xml, 'SPNameQualifier', null);

        $audience = Audience::getChildrenOfClass($xml);

        return new self($audience, $nameQualifier, $spNameQualifier);
    }


    /**
     * Convert this Statement to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this BaseID.
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = parent::toXML($parent);

        foreach ($this->audience as $audience) {
            $audience->toXML($e);
        }

        return $e;
    }
}
