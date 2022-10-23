<?php

declare(strict_types=1);

namespace SimpleSAML\SAML2\XML\md;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML2\Constants as C;
use SimpleSAML\XML\Exception\SchemaViolationException;

use function implode;

/**
 * Class representing SAML 2 RoleDescriptor element.
 *
 * @package simplesamlphp/saml2
 */
abstract class AbstractRoleDescriptor extends AbstractMetadataDocument
{
    /**
     * List of supported protocols.
     *
     * @var string[]
     */
    protected array $protocolSupportEnumeration = [];

    /**
     * Error URL for this role.
     *
     * @var string|null
     */
    protected ?string $errorURL = null;

    /**
     * KeyDescriptor elements.
     *
     * Array of \SimpleSAML\SAML2\XML\md\KeyDescriptor elements.
     *
     * @var \SimpleSAML\SAML2\XML\md\KeyDescriptor[]
     */
    protected array $KeyDescriptors = [];

    /**
     * Organization of this role.
     *
     * @var \SimpleSAML\SAML2\XML\md\Organization|null
     */
    protected ?Organization $Organization = null;

    /**
     * ContactPerson elements for this role.
     *
     * Array of \SimpleSAML\SAML2\XML\md\ContactPerson objects.
     *
     * @var \SimpleSAML\SAML2\XML\md\ContactPerson[]
     */
    protected array $ContactPersons = [];


    /**
     * Initialize a RoleDescriptor.
     *
     * @param string[] $protocolSupportEnumeration A set of URI specifying the protocols supported.
     * @param string|null $ID The ID for this document. Defaults to null.
     * @param int|null $validUntil Unix time of validity for this document. Defaults to null.
     * @param string|null $cacheDuration Maximum time this document can be cached. Defaults to null.
     * @param \SimpleSAML\SAML2\XML\md\Extensions|null $extensions An Extensions object. Defaults to null.
     * @param string|null $errorURL An URI where to redirect users for support. Defaults to null.
     * @param \SimpleSAML\SAML2\XML\md\KeyDescriptor[] $keyDescriptors An array of KeyDescriptor elements. Defaults to an empty array.
     * @param \SimpleSAML\SAML2\XML\md\Organization|null $organization The organization running this entity. Defaults to null.
     * @param \SimpleSAML\SAML2\XML\md\ContactPerson[] $contacts An array of contacts for this entity. Defaults to an empty array.
     * @param \DOMAttr[] $namespacedAttributes
     */
    public function __construct(
        array $protocolSupportEnumeration,
        ?string $ID = null,
        ?int $validUntil = null,
        ?string $cacheDuration = null,
        ?Extensions $extensions = null,
        ?string $errorURL = null,
        array $keyDescriptors = [],
        ?Organization $organization = null,
        array $contacts = [],
        array $namespacedAttributes = []
    ) {
        parent::__construct($ID, $validUntil, $cacheDuration, $extensions, $namespacedAttributes);

        $this->setProtocolSupportEnumeration($protocolSupportEnumeration);
        $this->setErrorURL($errorURL);
        $this->setKeyDescriptors($keyDescriptors);
        $this->setOrganization($organization);
        $this->setContactPersons($contacts);
    }


    /**
     * Collect the value of the errorURL property.
     *
     * @return string|null
     */
    public function getErrorURL()
    {
        return $this->errorURL;
    }


    /**
     * Set the value of the errorURL property.
     *
     * @param string|null $errorURL
     * @throws \SimpleSAML\SAML2\Exception\SchemaViolationException
     */
    protected function setErrorURL(?string $errorURL = null): void
    {
        Assert::nullOrValidURI($errorURL, SchemaViolationException::class); // Covers the empty string
        $this->errorURL = $errorURL;
    }


    /**
     * Collect the value of the protocolSupportEnumeration property.
     *
     * @return string[]
     */
    public function getProtocolSupportEnumeration()
    {
        return $this->protocolSupportEnumeration;
    }


    /**
     * Set the value of the ProtocolSupportEnumeration property.
     *
     * @param string[] $protocols
     * @throws \SimpleSAML\Assert\AssertionFailedException
     * @throws \SimpleSAML\XML\Exception\SchemaViolationException
     */
    protected function setProtocolSupportEnumeration(array $protocols): void
    {
        Assert::minCount($protocols, 1, 'At least one protocol must be supported by this md:' . static::getLocalName() . '.');
        Assert::allValidURI($protocols, SchemaViolationException::class);

        $this->protocolSupportEnumeration = $protocols;
    }


    /**
     * Collect the value of the Organization property.
     *
     * @return \SimpleSAML\SAML2\XML\md\Organization|null
     */
    public function getOrganization()
    {
        return $this->Organization;
    }


    /**
     * Set the value of the Organization property.
     *
     * @param \SimpleSAML\SAML2\XML\md\Organization|null $organization
     */
    protected function setOrganization(?Organization $organization = null): void
    {
        $this->Organization = $organization;
    }


    /**
     * Collect the value of the ContactPersons property.
     *
     * @return \SimpleSAML\SAML2\XML\md\ContactPerson[]
     */
    public function getContactPersons()
    {
        return $this->ContactPersons;
    }


    /**
     * Set the value of the ContactPerson property.
     *
     * @param \SimpleSAML\SAML2\XML\md\ContactPerson[] $contactPersons
     * @throws \SimpleSAML\Assert\AssertionFailedException
     */
    protected function setContactPersons(array $contactPersons): void
    {
        Assert::allIsInstanceOf(
            $contactPersons,
            ContactPerson::class,
            'All contacts must be an instance of md:ContactPerson',
        );

        $this->ContactPersons = $contactPersons;
    }


    /**
     * Collect the value of the KeyDescriptors property.
     *
     * @return \SimpleSAML\SAML2\XML\md\KeyDescriptor[]
     */
    public function getKeyDescriptors()
    {
        return $this->KeyDescriptors;
    }


    /**
     * Set the value of the KeyDescriptor property.
     *
     * @param \SimpleSAML\SAML2\XML\md\KeyDescriptor[] $keyDescriptor
     */
    protected function setKeyDescriptors(array $keyDescriptor): void
    {
        Assert::allIsInstanceOf(
            $keyDescriptor,
            KeyDescriptor::class,
            'All key descriptors must be an instance of md:KeyDescriptor',
        );

        $this->KeyDescriptors = $keyDescriptor;
    }


    /**
     * Add this RoleDescriptor to an EntityDescriptor.
     *
     * @param \DOMElement $parent The EntityDescriptor we should append this endpoint to.
     * @return \DOMElement
     */
    public function toUnsignedXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toUnsignedXML($parent);

        $e->setAttribute('protocolSupportEnumeration', implode(' ', $this->protocolSupportEnumeration));

        if ($this->getErrorURL() !== null) {
            $e->setAttribute('errorURL', $this->getErrorURL());
        }

        foreach ($this->getKeyDescriptors() as $kd) {
            $kd->toXML($e);
        }

        $this->getOrganization()?->toXML($e);

        foreach ($this->getContactPersons() as $cp) {
            $cp->toXML($e);
        }

        return $e;
    }
}
